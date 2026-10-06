@extends('layouts.app')
@section('title', 'Customer Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Account</h1>
    <a href="{{ route('customer.vouchers.redeem') }}" class="btn btn-primary">Redeem Voucher</a>
</div>

<div class="card mb-6">
    <h2 class="text-lg font-semibold mb-4">Active Plan</h2>
    
    @if($activeVoucher)
    <div style="padding: 24px; border: 1px solid var(--color-border); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div class="text-xl font-bold mb-1">{{ $activeVoucher->plan->name }}</div>
            <div class="text-muted">Expires in {{ $activeVoucher->expires_at->diffForHumans() }}</div>
        </div>
        <div style="text-align: right;">
            @if($activeVoucher->plan->data_limit_gb)
            <div class="text-2xl font-bold" style="color: var(--color-primary)">{{ $activeVoucher->plan->data_limit_gb }} GB</div>
            <div class="text-sm text-muted">Total Limit</div>
            @else
            <div class="text-2xl font-bold" style="color: var(--color-primary)">Unlimited</div>
            <div class="text-sm text-muted">Data</div>
            @endif
        </div>
    </div>
    @else
    <div class="text-muted" style="padding: 24px; border: 1px solid var(--color-border); border-radius: var(--radius-md); text-align: center;">
        No active plan right now. Redeem a voucher to get connected.
    </div>
    @endif
</div>

<div class="card">
    <h2 class="text-lg font-semibold mb-4">Voucher History</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Voucher Code</th>
                    <th>Plan</th>
                    <th>Redeemed At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($voucherHistory as $voucher)
                <tr>
                    <td class="font-mono">CONN-****-****-{{ substr($voucher->code, -4) }}</td>
                    <td>{{ $voucher->plan->name }}</td>
                    <td>{{ $voucher->redeemed_at->format('M d, Y') }}</td>
                    <td>
                        @if($voucher->expires_at && $voucher->expires_at->isPast())
                        <span class="badge badge-secondary">Expired</span>
                        @else
                        <span class="badge badge-success">Active</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">You haven't redeemed any vouchers yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
