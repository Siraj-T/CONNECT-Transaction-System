<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoucherGeneratorController extends Controller
{
    public function create()
    {
        $plans = VoucherPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.vouchers.generate', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_plan_id' => 'required|exists:voucher_plans,id',
            'quantity'        => 'required|integer|min:1|max:500',
        ]);

        $batchId = (string) Str::uuid();
        $vouchersToInsert = [];
        $now = now();

        for ($i = 0; $i < $validated['quantity']; $i++) {
            $vouchersToInsert[] = [
                'voucher_plan_id' => $validated['voucher_plan_id'],
                'code'            => $this->generateUniqueCode(),
                'batch_id'        => $batchId,
                'status'          => 'available',
                'generated_by'    => Auth::id(),
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Chunking the insert for memory safety if max is increased later
        foreach (array_chunk($vouchersToInsert, 100) as $chunk) {
            Voucher::insert($chunk);
        }

        // Log the batch generation (assuming we are tracking this)
        // Here we could record it in transaction table if generating vouchers had a "cost",
        // but typically admin generates them for free. We can just add an AuditLog.
        \App\Models\AuditLog::create([
            'user_id'      => Auth::id(),
            'action'       => 'vouchers.generated',
            'subject_type' => 'Batch',
            'subject_id'   => null,
            'new_values'   => [
                'batch_id' => $batchId,
                'quantity' => $validated['quantity'],
                'plan_id'  => $validated['voucher_plan_id']
            ]
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Successfully generated {$validated['quantity']} vouchers in batch {$batchId}.");
    }

    private function generateUniqueCode()
    {
        // Format: CONN-XXXX-XXXX-XXXX
        $code = 'CONN-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        
        // Ensure strictly unique (extremely unlikely to collide but necessary)
        if (Voucher::where('code', $code)->exists()) {
            return $this->generateUniqueCode();
        }

        return $code;
    }
}
