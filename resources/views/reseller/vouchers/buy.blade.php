@extends('layouts.app')
@section('title', 'Buy Vouchers')

@section('content')
<div class="page-header">
    <h1 class="page-title">Buy Vouchers</h1>
    <a href="{{ route('reseller.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="card mb-6" style="background: var(--color-surface-hover); display: inline-block;">
    <div class="text-sm font-semibold text-muted mb-1">Your Wallet Balance</div>
    <div class="text-2xl font-bold" style="color: var(--color-primary)">
        {{ number_format(Auth::user()->getWalletBalance(), 3) }} LYD
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    @forelse($availablePlans as $plan)
    <div class="card">
        <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 16px; height: 16px; border-radius: 4px; background-color: {{ $plan->color_hex }};"></div>
                <h3 class="font-semibold text-lg">{{ $plan->name }}</h3>
            </div>
            <div class="badge badge-success">{{ $plan->vouchers_count }} Available</div>
        </div>
        
        <p class="text-sm text-muted mb-4">{{ $plan->duration_days }} Days • {{ $plan->data_limit_gb ?? 'Unlimited' }} GB</p>
        
        <div class="mb-4" style="padding: 12px; background: var(--color-bg); border-radius: var(--radius-md);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span class="text-sm text-muted">Your Cost</span>
                <span class="font-semibold">{{ number_format($plan->reseller_price_lyd, 3) }} LYD</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span class="text-sm text-muted">Suggested Retail</span>
                <span class="font-semibold">{{ number_format($plan->retail_price_lyd, 3) }} LYD</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 8px; border-top: 1px solid var(--color-border); padding-top: 8px;">
                <span class="text-sm font-semibold text-success">Potential Profit</span>
                <span class="font-semibold text-success">{{ number_format($plan->retail_price_lyd - $plan->reseller_price_lyd, 3) }} LYD</span>
            </div>
        </div>

        <form action="{{ route('reseller.vouchers.buy.store') }}" method="POST">
            @csrf
            <input type="hidden" name="voucher_plan_id" value="{{ $plan->id }}">
            <div style="display: flex; gap: 12px;">
                <input type="number" name="quantity" class="form-input" value="1" min="1" max="{{ $plan->vouchers_count }}" style="width: 100px;" required>
                <button type="submit" class="btn btn-primary" style="flex: 1;" onclick="return confirm('Confirm purchase? Wallet will be deducted.');">Purchase</button>
            </div>
        </form>
    </div>
    @empty
    <div class="card" style="grid-column: span 2; text-align: center; padding: 48px;">
        <p class="text-muted text-lg">No vouchers are currently available for purchase.</p>
    </div>
    @endforelse
</div>
@endsection
