<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherPlan;
use App\Models\Transaction;
use App\Models\WalletLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResellerVoucherController extends Controller
{
    public function inventoryIndex()
    {
        $vouchers = Voucher::with('plan')
            ->where('sold_by', Auth::id())
            ->where('status', 'reserved') // reserved means bought by reseller but not yet sold/redeemed
            ->paginate(20);
            
        return view('reseller.vouchers.inventory', compact('vouchers'));
    }
    public function buyIndex()
    {
        // Get available vouchers grouped by plan without caching the counts
        $availablePlans = VoucherPlan::where('is_active', true)
            ->withCount(['vouchers' => function ($query) {
                $query->where('status', 'available');
            }])
            ->get()
            ->filter(function ($plan) {
                return $plan->vouchers_count > 0;
            });

        return view('reseller.vouchers.buy', compact('availablePlans'));
    }

    public function buyStore(Request $request)
    {
        $validated = $request->validate([
            'voucher_plan_id' => 'required|exists:voucher_plans,id',
            'quantity'        => 'required|integer|min:1|max:1000',
        ]);

        $reseller = Auth::user();
        $plan = VoucherPlan::findOrFail($validated['voucher_plan_id']);
        $quantity = $validated['quantity'];
        
        $totalCost = $plan->reseller_price_lyd * $quantity;

        try {
            DB::beginTransaction();

            // Lock the reseller profile to prevent double-spending
            $profile = $reseller->resellerProfile()->lockForUpdate()->first();
            
            if (!$profile) {
                throw new \Exception('Reseller profile not found.');
            }

            if ($profile->wallet_balance < $totalCost) {
                throw new \Exception('Insufficient wallet balance.');
            }

            // Lock vouchers to prevent race conditions
            $vouchers = Voucher::where('voucher_plan_id', $plan->id)
                ->where('status', 'available')
                ->limit($quantity)
                ->lockForUpdate()
                ->get();

            if ($vouchers->count() < $quantity) {
                throw new \Exception('Not enough vouchers available for this plan.');
            }

            // Update vouchers
            $voucherIds = $vouchers->pluck('id');
            Voucher::whereIn('id', $voucherIds)->update([
                'status'  => 'reserved', // Status when reseller owns it
                'sold_by' => $reseller->id,
            ]);

            // Deduct from wallet safely
            $newBalance = $profile->wallet_balance - $totalCost;
            $profile->update(['wallet_balance' => $newBalance]);

            // Create Transaction
            $reference = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $transaction = Transaction::create([
                'reference_no' => $reference,
                'type'         => 'purchase',
                'status'       => 'completed',
                'user_id'      => $reseller->id,
                'total_amount' => $totalCost,
                'notes'        => "Bought {$quantity}x {$plan->name}",
            ]);

            // Create Ledger Entry
            WalletLedger::create([
                'user_id'        => $reseller->id,
                'transaction_id' => $transaction->id,
                'type'           => 'debit',
                'amount'         => $totalCost,
                'balance_after'  => $newBalance,
                'description'    => "Purchased vouchers (Ref: {$reference})",
            ]);

            DB::commit();

            return redirect()->route('reseller.dashboard')->with('success', "Successfully purchased {$quantity} vouchers.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
