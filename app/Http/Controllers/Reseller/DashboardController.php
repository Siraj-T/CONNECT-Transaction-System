<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $reseller = Auth::user();
        
        $inventoryCount = Voucher::where('sold_by', $reseller->id)
                                 ->where('status', 'reserved') // reserved means reseller owns it, not sold to customer yet
                                 ->count();
                                 
        $recentSales = Transaction::with('counterpart')
                                  ->where('user_id', $reseller->id)
                                  ->where('type', 'sale') // when reseller sells to customer
                                  ->orderByDesc('created_at')
                                  ->limit(5)
                                  ->get();
                                  
        return view('reseller.dashboard', compact('reseller', 'inventoryCount', 'recentSales'));
    }
}
