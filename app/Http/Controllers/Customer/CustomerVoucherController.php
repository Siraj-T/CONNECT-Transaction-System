<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerVoucherController extends Controller
{
    public function redeemIndex()
    {
        return view('customer.vouchers.redeem');
    }

    public function redeemStore(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $customer = Auth::user();

        // Format to exact standard just in case
        $code = strtoupper(trim($validated['voucher_code']));

        try {
            DB::beginTransaction();

            $voucher = Voucher::where('code', $code)
                ->whereIn('status', ['available', 'reserved']) // Can be bought directly or via reseller
                ->lockForUpdate()
                ->first();

            if (!$voucher) {
                throw new \Exception('Invalid voucher code or voucher already redeemed.');
            }

            // Calculate expiration based on plan
            $now = now();
            $expiresAt = $now->copy()->addDays($voucher->plan->duration_days);

            // Update voucher
            $voucher->update([
                'status'      => 'redeemed',
                'redeemed_by' => $customer->id,
                'redeemed_at' => $now,
                'expires_at'  => $expiresAt,
            ]);

            // Track redemption in transactions
            $reference = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            Transaction::create([
                'reference_no' => $reference,
                'type'         => 'purchase', // or 'redemption'
                'status'       => 'completed',
                'user_id'      => $customer->id,
                'total_amount' => 0.00, // They already paid the reseller
                'notes'        => "Redeemed voucher plan: {$voucher->plan->name}",
                'metadata'     => ['voucher_id' => $voucher->id],
            ]);

            DB::commit();

            return redirect()->route('customer.dashboard')->with('success', "Successfully redeemed {$voucher->plan->name}. It will expire on " . $expiresAt->format('M d, Y') . ".");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
