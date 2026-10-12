<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Citizen;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'               => 'required|numeric|min:0.01',
            'payment_method_id'    => 'nullable|exists:payment_methods,id',
            'recipient_citizen_id' => 'nullable|exists:citizens,id',
            'recipient_id'         => 'nullable|integer',
            'recipient_email'      => 'nullable|email',
            'recipient_phone'      => 'nullable|string',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($validated, $user, $request) {
            // Automatically pull and synchronize citizen profile from authenticated user
            $phone = $user->phone ?: ($user->citizen->phone_number ?? ('+1' . str_pad((string)$user->id, 9, '0', STR_PAD_LEFT)));

            $senderCitizen = Citizen::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'         => $user->name,
                    'phone_number' => $phone,
                ]
            );

            // Resolve recipient if provided
            $recipientCitizenId = $validated['recipient_citizen_id'] ?? $validated['recipient_id'] ?? null;
            if (!$recipientCitizenId && $request->filled('recipient_email')) {
                $recipientUser = User::where('email', $request->input('recipient_email'))->first();
                $recipientCitizenId = $recipientUser?->citizen?->id;
            }
            if (!$recipientCitizenId && $request->filled('recipient_phone')) {
                $recipientCitizen = Citizen::where('phone_number', $request->input('recipient_phone'))->first();
                $recipientCitizenId = $recipientCitizen?->id;
            }

            // Ensure sender is not sending to themselves if recipient was specified
            if ($recipientCitizenId && (int)$recipientCitizenId === (int)$senderCitizen->id) {
                return response()->json([
                    'message' => 'Cannot transfer funds to yourself.',
                ], 422);
            }

            // Ensure sender wallet exists
            $wallet = $user->wallet()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0.00, 'currency' => 'LYD']
            );

            $amount = (float) $validated['amount'];
            $paymentMethodId = $validated['payment_method_id'] ?? 1; // Default to Credit Card / Wallet Transfer
            $referenceNumber = 'TRX-' . strtoupper(Str::random(8));

            // Check if sender has sufficient balance
            if (!$wallet->hasSufficientBalance($amount)) {
                $available = number_format($wallet->available_balance, 2);
                $reqAmt = number_format($amount, 2);
                $rejectionReason = "Auto-rejected: Insufficient wallet balance. (Available: LYD {$available}, Requested: LYD {$reqAmt})";

                // Auto-reject and record transaction
                $transaction = Transaction::create([
                    'unique_reference_number' => $referenceNumber,
                    'amount'                  => $amount,
                    'status'                  => 'rejected',
                    'rejection_reason'        => $rejectionReason,
                    'citizen_id'              => $senderCitizen->id,
                    'recipient_citizen_id'    => $recipientCitizenId,
                    'payment_method_id'       => $paymentMethodId,
                    'type'                    => $recipientCitizenId ? 'transfer' : 'payment',
                ]);

                // Record audit log for automated system rejection
                $systemAdmin = User::role('admin')->first();
                AuditLog::create([
                    'admin_id'         => $systemAdmin?->id ?? 1,
                    'action'           => 'auto_reject_transaction',
                    'reference_number' => $transaction->unique_reference_number,
                    'details'          => "Transaction auto-rejected due to insufficient balance. Sender: {$user->name} (#{$user->id}), Available: LYD {$available}, Requested: LYD {$reqAmt}."
                ]);

                // Broadcast real-time WebSocket event via Laravel Reverb
                broadcast(new \App\Events\TransactionCreated($transaction));

                return response()->json([
                    'message'           => 'Transaction auto-rejected due to insufficient wallet balance.',
                    'status'            => 'rejected',
                    'rejection_reason'  => $rejectionReason,
                    'available_balance' => $wallet->available_balance,
                    'currency'          => $wallet->currency,
                    'data'              => $transaction->load(['citizen', 'recipientCitizen', 'paymentMethod']),
                ], 422);
            }

            // Sufficient balance: Create transaction as pending for Admin approval
            $transaction = Transaction::create([
                'unique_reference_number' => $referenceNumber,
                'amount'                  => $amount,
                'status'                  => 'pending',
                'rejection_reason'        => null,
                'citizen_id'              => $senderCitizen->id,
                'recipient_citizen_id'    => $recipientCitizenId,
                'payment_method_id'       => $paymentMethodId,
                'type'                    => $recipientCitizenId ? 'transfer' : 'payment',
            ]);

            // Broadcast real-time WebSocket event via Laravel Reverb
            broadcast(new \App\Events\TransactionCreated($transaction));

            return response()->json([
                'message'           => 'Transfer submitted successfully. Pending administrator approval.',
                'status'            => 'pending',
                'sender_balance'    => $wallet->available_balance,
                'currency'          => $wallet->currency,
                'data'              => $transaction->load(['citizen', 'recipientCitizen', 'paymentMethod']),
            ], 201);
        });
    }
}
