<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $citizen = Citizen::firstOrCreate(
            ['phone_number' => $validated['sender_phone']],
            ['name' => $validated['sender_name']]
        );

        $transaction = Transaction::create([
            'unique_reference_number' => 'TRX-' . strtoupper(Str::random(8)),
            'amount' => $validated['amount'],
            'status' => 'pending',
            'citizen_id' => $citizen->id,
            'payment_method_id' => $validated['payment_method_id'],
        ]);

        return response()->json([
            'message' => 'Transaction submitted successfully',
            'data' => $transaction->load(['citizen', 'paymentMethod'])
        ], 201);
    }
}
