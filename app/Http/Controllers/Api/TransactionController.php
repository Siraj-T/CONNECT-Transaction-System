<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
        ]);

        $validated['unique_reference_number'] = 'TRX-' . strtoupper(Str::random(8));
        $validated['status'] = 'pending';

        $transaction = Transaction::create($validated);

        return response()->json([
            'message' => 'Transaction submitted successfully',
            'data' => $transaction
        ], 201);
    }
}
