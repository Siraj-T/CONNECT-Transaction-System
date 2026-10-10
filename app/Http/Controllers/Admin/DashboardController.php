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
        // Compute real metrics
        $totalRevenue = Transaction::where('status', 'accepted')->sum('amount');
        
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $acceptedTransactions = Transaction::where('status', 'accepted')->count();
        $rejectedTransactions = Transaction::where('status', 'rejected')->count();
                                    
        $recentTransactions = Transaction::with(['citizen', 'paymentMethod'])
            ->orderByDesc('created_at')
            ->paginate(15);
                                         
        return view('admin.dashboard', compact(
            'totalRevenue', 
            'pendingTransactions', 
            'acceptedTransactions', 
            'rejectedTransactions',
            'recentTransactions'
        ));
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
