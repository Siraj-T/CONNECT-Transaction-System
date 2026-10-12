<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'held_balance',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'balance'      => 'decimal:2',
            'held_balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(WalletLedger::class)->orderByDesc('created_at');
    }

    /**
     * Check if user has sufficient available (unheld) balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        $available = (float) $this->balance - (float) $this->held_balance;
        return $available >= $amount;
    }

    /**
     * Get available balance.
     */
    public function getAvailableBalanceAttribute(): float
    {
        return max(0.00, (float) $this->balance - (float) $this->held_balance);
    }

    /**
     * Deposit funds into wallet.
     */
    public function deposit(float $amount, ?string $description = null, ?string $refNumber = null): WalletLedger
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be strictly positive.');
        }

        return DB::transaction(function () use ($amount, $description, $refNumber) {
            $this->increment('balance', $amount);
            $this->refresh();

            return $this->ledgers()->create([
                'type'             => 'deposit',
                'amount'           => $amount,
                'balance_after'    => $this->balance,
                'reference_number' => $refNumber,
                'description'      => $description ?? 'Direct deposit to wallet',
            ]);
        });
    }

    /**
     * Withdraw/deduct funds directly from wallet.
     */
    public function withdraw(float $amount, ?string $description = null, ?string $refNumber = null): WalletLedger
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be strictly positive.');
        }

        if (!$this->hasSufficientBalance($amount)) {
            throw new InvalidArgumentException('Insufficient available wallet balance.');
        }

        return DB::transaction(function () use ($amount, $description, $refNumber) {
            $this->decrement('balance', $amount);
            $this->refresh();

            return $this->ledgers()->create([
                'type'             => 'withdrawal',
                'amount'           => $amount,
                'balance_after'    => $this->balance,
                'reference_number' => $refNumber,
                'description'      => $description ?? 'Direct withdrawal from wallet',
            ]);
        });
    }

    /**
     * Atomically transfer funds from this wallet to another wallet.
     */
    public function transferTo(Wallet $recipientWallet, float $amount, ?string $description = null, ?string $refNumber = null): array
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be strictly positive.');
        }

        return DB::transaction(function () use ($recipientWallet, $amount, $description, $refNumber) {
            // Deduct from sender
            $this->decrement('balance', $amount);
            $this->refresh();

            $senderLedger = $this->ledgers()->create([
                'type'             => 'transfer_out',
                'amount'           => $amount,
                'balance_after'    => $this->balance,
                'reference_number' => $refNumber,
                'description'      => $description ?? "Transfer sent to user #{$recipientWallet->user_id}",
            ]);

            // Credit to recipient
            $recipientWallet->increment('balance', $amount);
            $recipientWallet->refresh();

            $recipientLedger = $recipientWallet->ledgers()->create([
                'type'             => 'transfer_in',
                'amount'           => $amount,
                'balance_after'    => $recipientWallet->balance,
                'reference_number' => $refNumber,
                'description'      => $description ?? "Transfer received from user #{$this->user_id}",
            ]);

            return [
                'sender_ledger'    => $senderLedger,
                'recipient_ledger' => $recipientLedger,
            ];
        });
    }
}
