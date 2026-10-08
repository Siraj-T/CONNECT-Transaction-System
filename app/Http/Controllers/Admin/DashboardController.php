<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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
                                    
        $recentTransactions = Transaction::orderByDesc('created_at')->paginate(15);
                                         
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

        $transaction->update([
            'status' => $validated['action'] === 'accept' ? 'accepted' : 'rejected',
            'admin_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', "Transaction {$transaction->status} successfully.");
    }
}
