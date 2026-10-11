<?php

namespace App\Events;

use App\Models\AuditLog;
use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public AuditLog $auditLog
    ) {
        $this->transaction->loadMissing(['citizen', 'paymentMethod']);
        $this->auditLog->loadMissing('admin');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-transactions'),
            new Channel('admin-audit-logs'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transaction.updated';
    }

    public function broadcastWith(): array
    {
        $metrics = Transaction::selectRaw("
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END), 0) as accepted_count,
            COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_count
        ")->first();

        $auditMetrics = AuditLog::selectRaw("
            COUNT(*) as total_decisions,
            COALESCE(SUM(CASE WHEN action LIKE '%accept%' THEN 1 ELSE 0 END), 0) as accepted_decisions,
            COALESCE(SUM(CASE WHEN action LIKE '%reject%' THEN 1 ELSE 0 END), 0) as rejected_decisions
        ")->first();

        return [
            'transaction' => [
                'id' => $this->transaction->id,
                'reference' => $this->transaction->unique_reference_number,
                'status' => $this->transaction->status,
            ],
            'audit_log' => [
                'id' => $this->auditLog->id,
                'timestamp' => $this->auditLog->created_at->format('M d, Y H:i:s'),
                'admin_name' => $this->auditLog->admin->name ?? 'Admin',
                'action' => $this->auditLog->action,
                'reference' => $this->auditLog->reference_number ?? '—',
                'details' => $this->auditLog->details,
            ],
            'metrics' => [
                'totalRevenue' => '$' . number_format((float) ($metrics->total_revenue ?? 0), 2),
                'pendingTransactions' => number_format((int) ($metrics->pending_count ?? 0)),
                'acceptedTransactions' => number_format((int) ($metrics->accepted_count ?? 0)),
                'rejectedTransactions' => number_format((int) ($metrics->rejected_count ?? 0)),
            ],
            'audit_metrics' => [
                'totalDecisions' => number_format((int) ($auditMetrics->total_decisions ?? 0)),
                'acceptedDecisions' => number_format((int) ($auditMetrics->accepted_decisions ?? 0)),
                'rejectedDecisions' => number_format((int) ($auditMetrics->rejected_decisions ?? 0)),
                'totalRecords' => number_format((int) ($auditMetrics->total_decisions ?? 0)),
            ],
        ];
    }
}
