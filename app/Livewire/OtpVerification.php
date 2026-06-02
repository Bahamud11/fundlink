<?php

namespace App\Livewire;

use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class OtpVerification extends Component
{
    public string  $otp           = '';
    public ?string $resendMessage = null;
    public int     $resendCooldown = 0;
    public bool    $mailError     = false;  // true jika pengiriman email gagal

    private const OTP_TTL_MINUTES        = 5;
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
        if (!session()->has('otp_pending')) {
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        // Kirim OTP otomatis hanya jika belum ada di cache
        if (!Cache::has($this->otpCacheKey())) {
            $this->sendOtp();
        }
    }

    // ─── Send OTP ─────────────────────────────────────────────────────────────

    private function sendOtp(): void
    {
        $user = auth()->user();
        $code = $this->generateCode();

        // Simpan ke cache dulu — verifikasi tetap bisa berjalan meski email gagal
        Cache::put(
            $this->otpCacheKey(),
            $code,
            now()->addMinutes(self::OTP_TTL_MINUTES)
        );

        try {
            Mail::to($user->email)->send(
                new SendOtpMail($code, $user->name, self::OTP_TTL_MINUTES)
            );
            $this->mailError = false;
        } catch (\Throwable $e) {
            // Log error untuk debugging, tapi jangan crash halaman
            Log::error('OTP mail failed', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
            $this->mailError = true;
        }
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

        if ($stored === null) {
            $this->addError('otp', 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.');
            return;
        }

        if (!hash_equals((string) $stored, $this->otp)) {
            $this->addError('otp', 'Kode OTP tidak valid. Periksa kembali kode yang dikirim.');
            return;
        }

        Cache::forget($cacheKey);
        auth()->user()->update(['email_verified_at' => now()]);
        session()->forget('otp_pending');

        $this->redirect(route('dashboard'), navigate: true);
    }

    // ─── Resend ───────────────────────────────────────────────────────────────

    public function resend(): void
    {
        $rateLimitKey = $this->resendRateLimitKey();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $seconds              = RateLimiter::availableIn($rateLimitKey);
            $this->resendMessage  = "Tunggu {$seconds} detik sebelum mengirim ulang.";
            $this->resendCooldown = $seconds;
            return;
        }

        RateLimiter::hit($rateLimitKey, self::RESEND_COOLDOWN_SECONDS);

        Cache::forget($this->otpCacheKey());
        $this->sendOtp();
        $this->otp = '';

        if ($this->mailError) {
            $this->resendMessage  = 'Gagal mengirim email. Periksa konfigurasi SMTP.';
            $this->resendCooldown = 0;
        } else {
            $this->resendMessage  = 'Kode OTP baru telah dikirim ke email Anda.';
            $this->resendCooldown = self::RESEND_COOLDOWN_SECONDS;
        }
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

    public function render()
    {
        return view('livewire.pages.auth.otp-verification')
            ->layout('layouts.guest');
    }
}
