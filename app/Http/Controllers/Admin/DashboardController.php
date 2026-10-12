<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Consolidated metrics in a single database query to eliminate multi-query overhead
        $metrics = Transaction::selectRaw("
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END), 0) as accepted_count,
            COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_count
        ")->first();

        $totalRevenue = (float) ($metrics->total_revenue ?? 0);
        $pendingTransactions = (int) ($metrics->pending_count ?? 0);
        $acceptedTransactions = (int) ($metrics->accepted_count ?? 0);
        $rejectedTransactions = (int) ($metrics->rejected_count ?? 0);
                                    
        $recentTransactions = Transaction::with(['citizen.user.wallet', 'recipientCitizen.user.wallet', 'paymentMethod'])
            ->orderByDesc('created_at')
            ->paginate(15);

        // Version fingerprint for client auto-sync
        $latestMeta = Transaction::selectRaw('COUNT(*) as total_count, MAX(updated_at) as max_updated')->first();
        $initialVersion = ($latestMeta->total_count ?? 0) . '_' . ($latestMeta->max_updated ?? '0');
                                         
        return view('admin.dashboard', compact(
            'totalRevenue', 
            'pendingTransactions', 
            'acceptedTransactions', 
            'rejectedTransactions', 
            'recentTransactions',
            'initialVersion'
        ));
    }

    public function liveDashboard(Request $request)
    {
        // Fast lightweight fingerprint check: only count and max timestamp (~1ms execution)
        $latestMeta = Transaction::selectRaw('COUNT(*) as total_count, MAX(updated_at) as max_updated')->first();
        $version = ($latestMeta->total_count ?? 0) . '_' . ($latestMeta->max_updated ?? '0');

        // Zero overhead: if nothing changed, respond immediately with tiny payload (~35 bytes)
        if ($request->query('v') === $version) {
            return response()->json([
                'changed' => false,
                'version' => $version,
            ]);
        }

        // New data detected: compute metrics and render table rows
        $metrics = Transaction::selectRaw("
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END), 0) as accepted_count,
            COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_count
        ")->first();

        $page = max(1, (int) $request->query('page', 1));
        $recentTransactions = Transaction::with(['citizen.user.wallet', 'recipientCitizen.user.wallet', 'paymentMethod'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page', $page);

        $view = view('admin.dashboard', [
            'totalRevenue' => (float) ($metrics->total_revenue ?? 0),
            'pendingTransactions' => (int) ($metrics->pending_count ?? 0),
            'acceptedTransactions' => (int) ($metrics->accepted_count ?? 0),
            'rejectedTransactions' => (int) ($metrics->rejected_count ?? 0),
            'recentTransactions' => $recentTransactions,
            'initialVersion' => $version,
        ]);

        return response()->json([
            'changed' => true,
            'version' => $version,
            'metrics' => [
                'totalRevenue' => 'LYD ' . number_format((float) ($metrics->total_revenue ?? 0), 2),
                'pendingTransactions' => number_format((int) ($metrics->pending_count ?? 0)),
                'acceptedTransactions' => number_format((int) ($metrics->accepted_count ?? 0)),
                'rejectedTransactions' => number_format((int) ($metrics->rejected_count ?? 0)),
            ],
            'rowsHtml' => $view->fragment('transaction-rows'),
            'paginationHtml' => $view->fragment('pagination'),
        ]);
    }

    public function auditLogs()
    {
        $metrics = AuditLog::selectRaw("
            COUNT(*) as total_decisions,
            COALESCE(SUM(CASE WHEN action LIKE '%accept%' THEN 1 ELSE 0 END), 0) as accepted_decisions,
            COALESCE(SUM(CASE WHEN action LIKE '%reject%' THEN 1 ELSE 0 END), 0) as rejected_decisions
        ")->first();

        $totalDecisions = (int) ($metrics->total_decisions ?? 0);
        $acceptedDecisions = (int) ($metrics->accepted_decisions ?? 0);
        $rejectedDecisions = (int) ($metrics->rejected_decisions ?? 0);

        $auditLogs = AuditLog::with('admin')
            ->orderByDesc('created_at')
            ->paginate(15);

        $latestMeta = AuditLog::selectRaw('COUNT(*) as total_count, MAX(created_at) as max_created')->first();
        $initialVersion = ($latestMeta->total_count ?? 0) . '_' . ($latestMeta->max_created ?? '0');

        return view('admin.audit-logs', compact(
            'auditLogs',
            'totalDecisions',
            'acceptedDecisions',
            'rejectedDecisions',
            'initialVersion'
        ));
    }

    public function liveAuditLogs(Request $request)
    {
        $latestMeta = AuditLog::selectRaw('COUNT(*) as total_count, MAX(created_at) as max_created')->first();
        $version = ($latestMeta->total_count ?? 0) . '_' . ($latestMeta->max_created ?? '0');

        if ($request->query('v') === $version) {
            return response()->json([
                'changed' => false,
                'version' => $version,
            ]);
        }

        $metrics = AuditLog::selectRaw("
            COUNT(*) as total_decisions,
            COALESCE(SUM(CASE WHEN action LIKE '%accept%' THEN 1 ELSE 0 END), 0) as accepted_decisions,
            COALESCE(SUM(CASE WHEN action LIKE '%reject%' THEN 1 ELSE 0 END), 0) as rejected_decisions
        ")->first();

        $page = max(1, (int) $request->query('page', 1));
        $auditLogs = AuditLog::with('admin')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page', $page);

        $view = view('admin.audit-logs', [
            'auditLogs' => $auditLogs,
            'totalDecisions' => (int) ($metrics->total_decisions ?? 0),
            'acceptedDecisions' => (int) ($metrics->accepted_decisions ?? 0),
            'rejectedDecisions' => (int) ($metrics->rejected_decisions ?? 0),
            'initialVersion' => $version,
        ]);

        return response()->json([
            'changed' => true,
            'version' => $version,
            'metrics' => [
                'totalDecisions' => number_format((int) ($metrics->total_decisions ?? 0)),
                'acceptedDecisions' => number_format((int) ($metrics->accepted_decisions ?? 0)),
                'rejectedDecisions' => number_format((int) ($metrics->rejected_decisions ?? 0)),
            ],
            'rowsHtml' => $view->fragment('audit-log-rows'),
            'paginationHtml' => $view->fragment('pagination'),
        ]);
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'action' => 'required|in:accept,reject',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', "Transaction has already been {$transaction->status}.");
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $transaction, $request) {
            $senderCitizen = $transaction->citizen;
            $senderUser = $senderCitizen?->user;
            $senderWallet = $senderUser?->wallet;
            $amount = (float) $transaction->amount;

            if ($validated['action'] === 'accept') {
                // Verify sender still has sufficient balance before finalizing
                if ($senderWallet && !$senderWallet->hasSufficientBalance($amount)) {
                    $rejectionReason = "Auto-rejected on review: Sender balance became insufficient (Available: LYD {$senderWallet->available_balance}, Required: LYD {$amount}).";

                    $transaction->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $rejectionReason,
                        'admin_id'         => auth()->id(),
                    ]);

                    $auditLog = AuditLog::create([
                        'admin_id'         => auth()->id(),
                        'action'           => 'reject_transaction',
                        'reference_number' => $transaction->unique_reference_number,
                        'details'          => "Transaction {$transaction->unique_reference_number} auto-rejected: Insufficient sender wallet balance."
                    ]);

                    broadcast(new \App\Events\TransactionStatusUpdated($transaction, $auditLog));

                    return redirect()->back()->with('error', "Cannot accept transaction: Sender has insufficient balance.");
                }

                // If transfer to another citizen, transfer funds atomically
                if ($transaction->recipient_citizen_id && $transaction->recipientCitizen?->user) {
                    $recipientUser = $transaction->recipientCitizen->user;
                    $recipientWallet = $recipientUser->wallet()->firstOrCreate(
                        ['user_id' => $recipientUser->id],
                        ['balance' => 0.00, 'currency' => 'LYD']
                    );

                    if ($senderWallet) {
                        $senderWallet->transferTo(
                            $recipientWallet,
                            $amount,
                            "Transfer from {$senderUser->name} (Ref: {$transaction->unique_reference_number})",
                            $transaction->unique_reference_number
                        );
                    }

                    $auditDetails = "Transaction {$transaction->unique_reference_number} accepted. Transferred LYD {$amount} from {$senderUser->name} to {$recipientUser->name}.";
                } elseif ($senderWallet) {
                    // Direct payment: deduct from sender
                    $senderWallet->withdraw(
                        $amount,
                        "Payment accepted (Ref: {$transaction->unique_reference_number})",
                        $transaction->unique_reference_number
                    );
                    $auditDetails = "Transaction {$transaction->unique_reference_number} accepted. LYD {$amount} debited from {$senderUser->name}.";
                } else {
                    $auditDetails = "Transaction {$transaction->unique_reference_number} was accepted.";
                }

                $transaction->update([
                    'status'   => 'accepted',
                    'admin_id' => auth()->id(),
                ]);

                $auditLog = AuditLog::create([
                    'admin_id'         => auth()->id(),
                    'action'           => 'accept_transaction',
                    'reference_number' => $transaction->unique_reference_number,
                    'details'          => $auditDetails,
                ]);

                broadcast(new \App\Events\TransactionStatusUpdated($transaction, $auditLog));

                return redirect()->back()->with('success', "Transaction {$transaction->unique_reference_number} accepted and balances updated.");

            } else {
                // Reject transaction
                $reason = $validated['reason'] ?? $request->input('reason', 'Rejected by Administrator.');

                $transaction->update([
                    'status'           => 'rejected',
                    'rejection_reason' => $reason,
                    'admin_id'         => auth()->id(),
                ]);

                $auditLog = AuditLog::create([
                    'admin_id'         => auth()->id(),
                    'action'           => 'reject_transaction',
                    'reference_number' => $transaction->unique_reference_number,
                    'details'          => "Transaction {$transaction->unique_reference_number} was rejected by admin. Reason: {$reason}"
                ]);

                broadcast(new \App\Events\TransactionStatusUpdated($transaction, $auditLog));

                return redirect()->back()->with('success', "Transaction {$transaction->unique_reference_number} rejected.");
            }
        });
    }
}
