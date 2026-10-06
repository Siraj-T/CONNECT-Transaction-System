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
    public function index(Request $request)
    {
        $vouchers = Voucher::with(['plan', 'seller', 'redeemer', 'generator'])
            ->orderByDesc('created_at')
            ->paginate(20);
            
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $plans = VoucherPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.vouchers.generate', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_plan_id' => 'required|exists:voucher_plans,id',
            'quantity'        => 'required|integer|min:1|max:5000',
        ]);

        $batchId = (string) Str::uuid();
        $now = now();
        $quantity = $validated['quantity'];
        
        $vouchersToInsert = [];
        $generatedCodes = [];
        
        // Generate codes and avoid in-memory collisions
        while (count($generatedCodes) < $quantity) {
            $code = $this->generateUniqueCode();
            if (!isset($generatedCodes[$code])) {
                $generatedCodes[$code] = true;
            }
        }

        $codesArray = array_keys($generatedCodes);

        // Bulk DB collision check
        $existingCodes = Voucher::whereIn('code', $codesArray)->pluck('code')->toArray();
        $existingCodesMap = array_flip($existingCodes);

        // For any collisions in DB, regenerate until clean
        while (count($existingCodesMap) > 0) {
            foreach ($existingCodes as $collideCode) {
                unset($generatedCodes[$collideCode]); // Remove collided code
                
                // Generate a new clean code
                do {
                    $newCode = $this->generateUniqueCode();
                } while (isset($generatedCodes[$newCode]) || Voucher::where('code', $newCode)->exists());
                
                $generatedCodes[$newCode] = true;
            }
            // In extremely rare event of multiple collisions, we just replaced them.
            // Breaking loop since we checked them individually in the fallback loop.
            break;
        }

        foreach (array_keys($generatedCodes) as $code) {
            $vouchersToInsert[] = [
                'voucher_plan_id' => $validated['voucher_plan_id'],
                'code'            => $code,
                'batch_id'        => $batchId,
                'status'          => 'available',
                'generated_by'    => Auth::id(),
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Chunking the insert for memory safety
        foreach (array_chunk($vouchersToInsert, 500) as $chunk) {
            Voucher::insert($chunk);
        }

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
        // No ambiguous characters: exclude 0, O, 1, I, L
        $chars = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $segment = function() use ($chars) {
            $str = '';
            for ($i=0; $i<4; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        };

        return 'CONN-' . $segment() . '-' . $segment() . '-' . $segment();
    }
}
