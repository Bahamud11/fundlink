<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FundlinkApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name ?? 'api')->plainTextToken,
            'user' => $user->load('unit')
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        return response()->json([
            'token' => $user->createToken($request->device_name ?? 'api')->plainTextToken,
            'user' => $user->load('unit')
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $query = Transaction::query();

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        }

        $totalPemasukan = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'saldo' => (float)$saldo,
            'total_pemasukan' => (float)$totalPemasukan,
            'total_pengeluaran' => (float)$totalPengeluaran,
            'unit' => $user->unit
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();
        $query = Transaction::query();

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        }

        $transactions = $query->latest('transaction_date')->paginate(15);

        return response()->json($transactions);
    }

    public function storeTransaction(Request $request)
    {
        $user = $request->user();

        if (!$user->unit_id && !$user->isAdmin()) {
            return response()->json(['message' => 'Akun Anda belum ditautkan ke cabang mana pun. Silakan hubungi Admin.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // For admin, they can pass unit_id or we fall back to their unit_id (which is usually null). 
        // Wait, the API didn't have unit_id in validation. Let's allow admin to pass unit_id, else fallback.
        $unit_id = $user->isAdmin() && $request->has('unit_id') ? $request->unit_id : $user->unit_id;

        $data = [
            'unit_id' => $unit_id,
            'user_id' => $user->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $transaction = Transaction::create($data);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            if ($admin->id !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Transaksi Baru',
                    'message' => "Transaksi {$request->type} sebesar Rp " . number_format($request->amount, 0, ',', '.') . " ditambahkan oleh {$user->name}.",
                    'type' => 'transaction',
                    'is_read' => false,
                ]);
            }
        }

        return response()->json([
            'message' => 'Transaction recorded successfully',
            'transaction' => $transaction
        ], 201);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load('unit'));
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = \App\Models\Notification::where('user_id', $user->id)->latest()->paginate(15);
        return response()->json($notifications);
    }

    public function markNotificationRead(Request $request, $id)
    {
        $notification = \App\Models\Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('unit')
        ]);
    }

    public function units()
    {
        return response()->json(\App\Models\Unit::all());
    }

    public function users(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json(User::with('unit')->latest()->get());
    }
}
