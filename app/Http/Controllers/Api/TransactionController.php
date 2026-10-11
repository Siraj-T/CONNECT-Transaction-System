<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'            => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($validated, $user) {
            // Automatically pull and synchronize citizen profile from authenticated user
            $phone = $user->phone ?: ($user->citizen->phone_number ?? ('+1' . str_pad((string)$user->id, 9, '0', STR_PAD_LEFT)));
            
            $citizen = Citizen::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'         => $user->name,
                    'phone_number' => $phone,
                ]
            );

            $transaction = Transaction::create([
                'unique_reference_number' => 'TRX-' . strtoupper(Str::random(8)),
                'amount'                  => $validated['amount'],
                'status'                  => 'pending',
                'citizen_id'              => $citizen->id,
                'payment_method_id'       => $validated['payment_method_id'],
            ]);

            return response()->json([
                'message' => 'Transaction submitted successfully',
                'data'    => $transaction->load(['citizen', 'paymentMethod'])
            ], 201);
        });
    }
}
