<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = Auth::user();
        
        // Find active plan
        $activeVoucher = Voucher::with('plan')
                                ->where('redeemed_by', $customer->id)
                                ->where('status', 'redeemed')
                                ->where('expires_at', '>', now())
                                ->orderByDesc('expires_at')
                                ->first();
                                
        $voucherHistory = Voucher::with('plan')
                                 ->where('redeemed_by', $customer->id)
                                 ->orderByDesc('redeemed_at')
                                 ->limit(5)
                                 ->get();
                                 
        return view('customer.dashboard', compact('customer', 'activeVoucher', 'voucherHistory'));
    }
}
