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
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user->load('unit')
        ]);
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

        $data = [
            'unit_id' => $user->unit_id,
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
}
