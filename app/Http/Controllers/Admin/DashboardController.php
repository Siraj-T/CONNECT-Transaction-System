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
                                    
        $recentTransactions = Transaction::with(['citizen', 'paymentMethod'])
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
        $recentTransactions = Transaction::with(['citizen', 'paymentMethod'])
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
                'totalRevenue' => '$' . number_format((float) ($metrics->total_revenue ?? 0), 2),
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
            'action' => 'required|in:accept,reject'
        ]);

        $status = $validated['action'] === 'accept' ? 'accepted' : 'rejected';

        $transaction->update([
            'status' => $status,
            'admin_id' => auth()->id()
        ]);

        AuditLog::create([
            'admin_id' => auth()->id(),
            'action' => $status . '_transaction',
            'reference_number' => $transaction->unique_reference_number,
            'details' => "Transaction {$transaction->unique_reference_number} was {$status}."
        ]);

        return redirect()->back()->with('success', "Transaction {$transaction->status} successfully.");
    }
}
