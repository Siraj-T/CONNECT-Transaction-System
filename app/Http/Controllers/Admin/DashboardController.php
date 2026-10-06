<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Compute real metrics
        $totalRevenue = Transaction::where('status', 'completed')
                                    ->where('type', 'purchase')
                                    ->sum('total_amount');
                                    
        $activeResellers = User::role('reseller')->where('is_active', true)->count();
        
        $vouchersSoldToday = Voucher::where('status', 'sold')
                                    ->whereDate('sold_at', today())
                                    ->count();
                                    
        $recentTransactions = Transaction::with('user')
                                         ->orderByDesc('created_at')
                                         ->limit(5)
                                         ->get();
                                         
        return view('admin.dashboard', compact(
            'totalRevenue', 
            'activeResellers', 
            'vouchersSoldToday', 
            'recentTransactions'
        ));
    }
}
