<?php

namespace App\Livewire;

use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class OtpVerification extends Component
{
    public string $otp          = '';
    public ?string $resendMessage = null;
    public int $resendCooldown  = 0;

    /** Berapa menit OTP berlaku */
    private const OTP_TTL_MINUTES = 5;

    /** Berapa detik cooldown resend */
    private const RESEND_COOLDOWN_SECONDS = 60;

    // ─── Cache key helpers ────────────────────────────────────────────────────

    private function otpCacheKey(): string
    {
        return 'otp:' . sha1(auth()->id() . '|' . auth()->user()->email);
    }

    private function resendRateLimitKey(): string
    {
        return 'otp-resend:' . auth()->id();
    }

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        // Hanya user yang baru daftar (session otp_pending) yang boleh di sini
        if (!session()->has('otp_pending')) {
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        // Kirim OTP otomatis saat pertama kali halaman dibuka
        // (hanya jika belum ada OTP aktif di cache)
        if (!Cache::has($this->otpCacheKey())) {
            $this->sendOtp();
        }
    }

    // ─── Send OTP ─────────────────────────────────────────────────────────────

    private function sendOtp(): void
    {
        $user = auth()->user();
        $code = $this->generateCode();

        // Simpan ke Cache dengan TTL 5 menit
        Cache::put(
            $this->otpCacheKey(),
            $code,
            now()->addMinutes(self::OTP_TTL_MINUTES)
        );

        // Kirim email menggunakan SendOtpMail
        Mail::to($user->email)->send(
            new SendOtpMail($code, $user->name, self::OTP_TTL_MINUTES)
        );
    }

    // ─── Verify ───────────────────────────────────────────────────────────────

    public function verify(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 6 digit.',
            'otp.regex'    => 'Kode OTP hanya boleh berisi angka.',
        ]);

        $cacheKey = $this->otpCacheKey();
        $stored   = Cache::get($cacheKey);

        // Cache sudah tidak ada → kedaluwarsa
        if ($stored === null) {
            $this->addError('otp', 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.');
            return;
        }

        // Kode tidak cocok
        if (!hash_equals((string) $stored, $this->otp)) {
            $this->addError('otp', 'Kode OTP tidak valid. Periksa kembali kode yang dikirim.');
            return;
        }

        // ✅ Valid — hapus cache, tandai verified
        Cache::forget($cacheKey);

        auth()->user()->update(['email_verified_at' => now()]);

        // Hapus session flag
        session()->forget('otp_pending');

        $this->redirect(route('dashboard'), navigate: true);
    }

    // ─── Resend ───────────────────────────────────────────────────────────────

    public function resend(): void
    {
        $rateLimitKey = $this->resendRateLimitKey();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->resendMessage  = "Tunggu {$seconds} detik sebelum mengirim ulang.";
            $this->resendCooldown = $seconds;
            return;
        }

        RateLimiter::hit($rateLimitKey, self::RESEND_COOLDOWN_SECONDS);

        // Hapus OTP lama, kirim yang baru
        Cache::forget($this->otpCacheKey());
        $this->sendOtp();

        $this->otp            = '';
        $this->resendMessage  = 'Kode OTP baru telah dikirim ke email Anda.';
        $this->resendCooldown = self::RESEND_COOLDOWN_SECONDS;
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(): void
    {
        Cache::forget($this->otpCacheKey());
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('login'), navigate: true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.auth.otp-verification')
            ->layout('layouts.guest');
    }
}
