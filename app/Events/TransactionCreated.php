<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
        $this->transaction->loadMissing(['citizen', 'recipientCitizen', 'paymentMethod']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-transactions'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transaction.created';
    }

    public function broadcastWith(): array
    {
        $metrics = Transaction::selectRaw("
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END), 0) as accepted_count,
            COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_count
        ")->first();

        $totalTransactions = Transaction::count();

        return [
            'transaction' => [
                'id'               => $this->transaction->id,
                'reference'        => $this->transaction->unique_reference_number,
                'citizen_name'     => $this->transaction->citizen->name ?? 'Citizen',
                'citizen_phone'    => $this->transaction->citizen->phone_number ?? '—',
                'recipient_name'   => $this->transaction->recipientCitizen->name ?? 'Direct Payment',
                'payment_method'   => $this->transaction->paymentMethod->name ?? 'Wallet',
                'amount'           => number_format((float) $this->transaction->amount, 2),
                'status'           => $this->transaction->status,
                'rejection_reason' => $this->transaction->rejection_reason,
                'created_at'       => $this->transaction->created_at->format('M d, Y H:i'),
                'update_url'       => route('admin.transactions.status.update', $this->transaction),
            ],
            'metrics' => [
                'totalRevenue'        => 'LYD ' . number_format((float) ($metrics->total_revenue ?? 0), 2),
                'pendingTransactions' => number_format((int) ($metrics->pending_count ?? 0)),
                'acceptedTransactions'=> number_format((int) ($metrics->accepted_count ?? 0)),
                'rejectedTransactions'=> number_format((int) ($metrics->rejected_count ?? 0)),
                'totalCount'          => number_format($totalTransactions),
            ],
        ];
    }
}
