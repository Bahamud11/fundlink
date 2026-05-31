<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class FundlinkApiController extends Controller
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function success($data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    private function error(string $message, int $status = 400, $errors = null): JsonResponse
    {
        return response()->json(array_filter([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ]), $status);
    }

    // ─── Auth ─────────────────────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah.', 401);
        }

        // Revoke token lama dari device yang sama jika ada
        if ($request->device_name) {
            $user->tokens()->where('name', $request->device_name)->delete();
        }

        $token = $user->createToken($request->device_name ?? 'mobile')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => $this->formatUser($user->load('unit')),
        ], 'Login berhasil.');
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|lowercase|email|max:255|unique:users,email',
            'password'    => ['required', 'string', Password::min(8)],
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        $token = $user->createToken($request->device_name ?? 'mobile')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => $this->formatUser($user->load('unit')),
        ], 'Registrasi berhasil.', 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logout berhasil.');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->success(null, 'Semua sesi berhasil diakhiri.');
    }

    // ─── User ─────────────────────────────────────────────────────────────────

    public function user(Request $request): JsonResponse
    {
        return $this->success($this->formatUser($request->user()->load('unit')));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        return $this->success($this->formatUser($user->load('unit')), 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'string', Password::min(8), 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Password saat ini tidak sesuai.', 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Revoke semua token lain (paksa login ulang di device lain)
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return $this->success(null, 'Password berhasil diubah.');
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Transaction::query();

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        }

        // Filter periode opsional: ?period=monthly|weekly|yearly&year=2025&month=5
        $this->applyPeriodFilter($query, $request);

        $summary = (clone $query)->selectRaw(
            'SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as pemasukan,
             SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as pengeluaran'
        )->first();

        $pemasukan   = (float) ($summary->pemasukan ?? 0);
        $pengeluaran = (float) ($summary->pengeluaran ?? 0);

        // Transaksi terbaru
        $recent = (clone $query)
            ->with(['unit:id,name', 'user:id,name'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($t) => $this->formatTransaction($t));

        return $this->success([
            'saldo'              => $pemasukan - $pengeluaran,
            'total_pemasukan'    => $pemasukan,
            'total_pengeluaran'  => $pengeluaran,
            'recent_transactions'=> $recent,
            'unit'               => $user->unit ? ['id' => $user->unit->id, 'name' => $user->unit->name] : null,
        ]);
    }

    // ─── Transactions ─────────────────────────────────────────────────────────

    public function transactions(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Transaction::with(['unit:id,name', 'user:id,name']);

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Filter tipe: ?type=pemasukan|pengeluaran
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter kategori: ?category=Donasi
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter periode
        $this->applyPeriodFilter($query, $request);

        // Search keterangan/kategori: ?search=donasi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) =>
                $q->where('category', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
            );
        }

        $perPage      = min((int) $request->get('per_page', 15), 50);
        $transactions = $query->orderBy('transaction_date', 'desc')
                              ->orderBy('id', 'desc')
                              ->paginate($perPage);

        return $this->success([
            'data'         => $transactions->map(fn ($t) => $this->formatTransaction($t)),
            'pagination'   => $this->formatPagination($transactions),
        ]);
    }

    public function showTransaction(Request $request, int $id): JsonResponse
    {
        $user        = $request->user();
        $transaction = Transaction::with(['unit:id,name', 'user:id,name'])->findOrFail($id);

        // Non-admin hanya bisa lihat transaksi unit sendiri
        if (!$user->isAdmin() && $transaction->unit_id !== $user->unit_id) {
            return $this->error('Akses ditolak.', 403);
        }

        return $this->success($this->formatTransaction($transaction));
    }

    public function storeTransaction(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->unit_id && !$user->isAdmin()) {
            return $this->error('Akun Anda belum ditautkan ke cabang. Hubungi Admin.', 403);
        }

        $request->validate([
            'unit_id'          => $user->isAdmin() ? 'required|exists:units,id' : 'nullable',
            'type'             => 'required|in:pemasukan,pengeluaran',
            'amount'           => 'required|numeric|min:1',
            'category'         => 'required|string|max:100',
            'description'      => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
            'attachment'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $unitId = $user->isAdmin() ? $request->unit_id : $user->unit_id;

        $data = [
            'unit_id'          => $unitId,
            'user_id'          => $user->id,
            'type'             => $request->type,
            'amount'           => $request->amount,
            'category'         => $request->category,
            'description'      => $request->description,
            'transaction_date' => $request->transaction_date,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $transaction = Transaction::create($data);

        // Notifikasi admin
        User::where('role', 'admin')
            ->where('id', '!=', $user->id)
            ->get()
            ->each(fn ($admin) => Notification::create([
                'user_id' => $admin->id,
                'title'   => 'Transaksi Baru',
                'message' => "Transaksi {$request->type} sebesar Rp " . number_format($request->amount, 0, ',', '.') . " ditambahkan oleh {$user->name}.",
                'type'    => 'transaction',
                'is_read' => false,
            ]));

        return $this->success(
            $this->formatTransaction($transaction->load(['unit:id,name', 'user:id,name'])),
            'Transaksi berhasil disimpan.',
            201
        );
    }

    public function updateTransaction(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Hanya admin yang dapat mengubah transaksi.', 403);
        }

        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'unit_id'          => 'required|exists:units,id',
            'type'             => 'required|in:pemasukan,pengeluaran',
            'amount'           => 'required|numeric|min:1',
            'category'         => 'required|string|max:100',
            'description'      => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
            'attachment'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['unit_id', 'type', 'amount', 'category', 'description', 'transaction_date']);

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $transaction->update($data);

        return $this->success(
            $this->formatTransaction($transaction->load(['unit:id,name', 'user:id,name'])),
            'Transaksi berhasil diperbarui.'
        );
    }

    public function deleteTransaction(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Hanya admin yang dapat menghapus transaksi.', 403);
        }

        $transaction = Transaction::findOrFail($id);

        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }

        $transaction->delete();

        return $this->success(null, 'Transaksi berhasil dihapus.');
    }

    // ─── Notifications ────────────────────────────────────────────────────────

    public function notifications(Request $request): JsonResponse
    {
        $perPage       = min((int) $request->get('per_page', 15), 50);
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($perPage);

        return $this->success([
            'data'       => $notifications->items(),
            'pagination' => $this->formatPagination($notifications),
            'unread_count' => Notification::where('user_id', $request->user()->id)
                                ->where('is_read', false)->count(),
        ]);
    }

    public function markNotificationRead(Request $request, int $id): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->findOrFail($id)
            ->update(['is_read' => true]);

        return $this->success(null, 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->success(null, 'Semua notifikasi ditandai sudah dibaca.');
    }

    // ─── Units ────────────────────────────────────────────────────────────────

    public function units(Request $request): JsonResponse
    {
        $units = Unit::withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn ($unit) => [
                'id'             => $unit->id,
                'name'           => $unit->name,
                'address'        => $unit->address,
                'google_maps_url'=> $unit->google_maps_url,
                'users_count'    => $unit->users_count,
                'created_at'     => $unit->created_at,
            ]);

        return $this->success($units);
    }

    public function showUnit(Request $request, int $id): JsonResponse
    {
        $unit = Unit::withCount('users')->findOrFail($id);

        $summary = $unit->transactions()
            ->selectRaw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as pemasukan,
                         SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as pengeluaran')
            ->first();

        $pemasukan   = (float) ($summary->pemasukan ?? 0);
        $pengeluaran = (float) ($summary->pengeluaran ?? 0);

        return $this->success([
            'id'              => $unit->id,
            'name'            => $unit->name,
            'address'         => $unit->address,
            'google_maps_url' => $unit->google_maps_url,
            'users_count'     => $unit->users_count,
            'saldo'           => $pemasukan - $pengeluaran,
            'total_pemasukan' => $pemasukan,
            'total_pengeluaran'=> $pengeluaran,
            'created_at'      => $unit->created_at,
        ]);
    }

    public function storeUnit(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Hanya admin yang dapat menambah unit.', 403);
        }

        $request->validate([
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
            'initial_balance' => 'nullable|numeric|min:0',
        ]);

        $unit = Unit::create($request->only(['name', 'address', 'google_maps_url']));

        if ($request->filled('initial_balance') && $request->initial_balance > 0) {
            $unit->transactions()->create([
                'user_id'          => $request->user()->id,
                'type'             => 'pemasukan',
                'amount'           => $request->initial_balance,
                'category'         => 'Dana Awal',
                'description'      => 'Saldo awal unit baru',
                'transaction_date' => now(),
            ]);
        }

        return $this->success($unit, 'Unit berhasil ditambahkan.', 201);
    }

    public function updateUnit(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Hanya admin yang dapat mengubah unit.', 403);
        }

        $unit = Unit::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
        ]);

        $unit->update($request->only(['name', 'address', 'google_maps_url']));

        return $this->success($unit, 'Unit berhasil diperbarui.');
    }

    public function deleteUnit(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Hanya admin yang dapat menghapus unit.', 403);
        }

        Unit::findOrFail($id)->delete();

        return $this->success(null, 'Unit berhasil dihapus.');
    }

    // ─── Users (Admin) ────────────────────────────────────────────────────────

    public function users(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Akses ditolak.', 403);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $users   = User::with('unit:id,name')
            ->when($request->filled('search'), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', '%' . $request->search . '%')
                       ->orWhere('email', 'like', '%' . $request->search . '%')
                )
            )
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->success([
            'data'       => $users->map(fn ($u) => $this->formatUser($u)),
            'pagination' => $this->formatPagination($users),
        ]);
    }

    public function storeUser(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Akses ditolak.', 403);
        }

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email',
            'password'=> ['required', 'string', Password::min(8)],
            'role'    => 'required|in:admin,user',
            'unit_id' => 'required_if:role,user|nullable|exists:units,id',
        ]);

        $user = User::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'password'=> Hash::make($request->password),
            'role'    => $request->role,
            'unit_id' => $request->role === 'admin' ? null : $request->unit_id,
        ]);

        return $this->success($this->formatUser($user->load('unit')), 'Pengguna berhasil ditambahkan.', 201);
    }

    public function updateUser(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Akses ditolak.', 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $id,
            'password'=> 'nullable|string|min:8',
            'role'    => 'required|in:admin,user',
            'unit_id' => 'required_if:role,user|nullable|exists:units,id',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role'    => $request->role,
            'unit_id' => $request->role === 'admin' ? null : $request->unit_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return $this->success($this->formatUser($user->load('unit')), 'Pengguna berhasil diperbarui.');
    }

    public function deleteUser(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return $this->error('Akses ditolak.', 403);
        }

        if ($id === $request->user()->id) {
            return $this->error('Tidak dapat menghapus akun sendiri.', 422);
        }

        User::findOrFail($id)->delete();

        return $this->success(null, 'Pengguna berhasil dihapus.');
    }

    // ─── Kategori ─────────────────────────────────────────────────────────────

    public function categories(): JsonResponse
    {
        return $this->success([
            'pemasukan' => [
                'Dana BOS', 'Donasi', 'Infaq', 'Zakat',
                'Iuran Siswa', 'Bantuan Pemerintah', 'Hibah',
                'Pendapatan Usaha', 'Lainnya',
            ],
            'pengeluaran' => [
                'Gaji Pegawai', 'Listrik & Air', 'Internet', 'Pemeliharaan',
                'Alat Tulis Kantor', 'Konsumsi', 'Kegiatan Siswa',
                'Transportasi', 'Kebersihan', 'Perlengkapan', 'Lainnya',
            ],
        ]);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'role'              => $user->role,
            'unit_id'           => $user->unit_id,
            'unit'              => $user->unit ? ['id' => $user->unit->id, 'name' => $user->unit->name] : null,
            'profile_photo_url' => $user->profile_photo_path
                                    ? Storage::disk('public')->url($user->profile_photo_path)
                                    : null,
            'email_verified_at' => $user->email_verified_at,
            'created_at'        => $user->created_at,
        ];
    }

    private function formatTransaction(Transaction $t): array
    {
        return [
            'id'               => $t->id,
            'type'             => $t->type,
            'amount'           => (float) $t->amount,
            'category'         => $t->category,
            'description'      => $t->description,
            'transaction_date' => $t->transaction_date?->format('Y-m-d'),
            'attachment_url'   => $t->attachment_path
                                    ? Storage::disk('public')->url($t->attachment_path)
                                    : null,
            'unit'             => $t->relationLoaded('unit') && $t->unit
                                    ? ['id' => $t->unit->id, 'name' => $t->unit->name]
                                    : null,
            'recorded_by'      => $t->relationLoaded('user') && $t->user
                                    ? ['id' => $t->user->id, 'name' => $t->user->name]
                                    : null,
            'created_at'       => $t->created_at,
        ];
    }

    private function formatPagination($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'has_more'     => $paginator->hasMorePages(),
        ];
    }

    private function applyPeriodFilter($query, Request $request): void
    {
        $period = $request->get('period'); // monthly | weekly | yearly | custom

        if ($period === 'weekly') {
            $query->whereBetween('transaction_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ]);
        } elseif ($period === 'monthly') {
            $year  = (int) $request->get('year', now()->year);
            $month = (int) $request->get('month', now()->month);
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $month);
        } elseif ($period === 'yearly') {
            $year = (int) $request->get('year', now()->year);
            $query->whereYear('transaction_date', $year);
        } elseif ($period === 'custom') {
            if ($request->filled('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }
        }
    }
}
