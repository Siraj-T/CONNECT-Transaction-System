<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherPlanController extends Controller
{
    public function index()
    {
        $plans = VoucherPlan::with('creator')->orderBy('sort_order')->get();
        return view('admin.voucher-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.voucher-plans.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string',
            'data_limit_gb'      => 'nullable|numeric|min:0',
            'duration_days'      => 'required|integer|min:1',
            'price_lyd'          => 'required|numeric|min:0',
            'reseller_price_lyd' => 'required|numeric|min:0',
            'retail_price_lyd'   => 'required|numeric|min:0',
            'speed_mbps'         => 'nullable|integer|min:1',
            'color_hex'          => 'required|string|size:7',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active']  = $request->has('is_active');

        VoucherPlan::create($validated);

        return redirect()->route('admin.voucher-plans.index')->with('success', 'Voucher plan created successfully.');
    }

    public function edit(VoucherPlan $voucherPlan)
    {
        return view('admin.voucher-plans.form', compact('voucherPlan'));
    }

    public function update(Request $request, VoucherPlan $voucherPlan)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string',
            'data_limit_gb'      => 'nullable|numeric|min:0',
            'duration_days'      => 'required|integer|min:1',
            'price_lyd'          => 'required|numeric|min:0',
            'reseller_price_lyd' => 'required|numeric|min:0',
            'retail_price_lyd'   => 'required|numeric|min:0',
            'speed_mbps'         => 'nullable|integer|min:1',
            'color_hex'          => 'required|string|size:7',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $voucherPlan->update($validated);

        return redirect()->route('admin.voucher-plans.index')->with('success', 'Voucher plan updated successfully.');
    }

    public function destroy(VoucherPlan $voucherPlan)
    {
        $voucherPlan->delete();
        return redirect()->route('admin.voucher-plans.index')->with('success', 'Voucher plan deactivated/deleted successfully.');
    }
}
