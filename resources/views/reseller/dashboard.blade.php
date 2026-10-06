@extends('layouts.app')
@section('title', 'Reseller Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Reseller Portal</h1>
    <a href="{{ route('reseller.vouchers.buy') }}" class="btn btn-primary">Buy Vouchers</a>
</div>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="card" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); color: white;">
        <div class="text-sm font-medium mb-2" style="color: rgba(255,255,255,0.8)">Wallet Balance</div>
        <div class="text-4xl font-bold mb-4">{{ number_format($reseller->getWalletBalance(), 3) }} <span class="text-lg font-normal">LYD</span></div>
        <button class="btn" style="background: rgba(255,255,255,0.2); color: white; width: 100%;">Request Topup</button>
    </div>
    
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Available Inventory</div>
        <div class="text-3xl font-bold mb-4">{{ number_format($inventoryCount) }} <span class="text-sm font-normal text-muted">Vouchers</span></div>
        <a href="{{ route('reseller.vouchers.inventory') }}" class="btn btn-secondary w-full" style="width: 100%; display: block; text-align: center;">View Inventory</a>
    </div>
</div>

<div class="card">
    <h2 class="text-lg font-semibold mb-4">Recent Sales</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Plan</th>
                    <th>Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSales as $sale)
                <tr>
                    <td>{{ $sale->counterpart->name ?? 'Unknown Customer' }}</td>
                    <td>{{ $sale->notes }}</td>
                    <td>{{ number_format($sale->total_amount, 3) }} LYD</td>
                    <td>{{ $sale->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-muted text-center">No sales yet today.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
