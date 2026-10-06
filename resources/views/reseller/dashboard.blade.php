@extends('layouts.app')
@section('title', 'Reseller Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Reseller Portal</h1>
    <button class="btn btn-primary">Buy Vouchers</button>
</div>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="card" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); color: white;">
        <div class="text-sm font-medium mb-2" style="color: rgba(255,255,255,0.8)">Wallet Balance</div>
        <div class="text-4xl font-bold mb-4">{{ number_format(Auth::user()->getWalletBalance(), 3) }} <span class="text-lg font-normal">LYD</span></div>
        <button class="btn" style="background: rgba(255,255,255,0.2); color: white; width: 100%;">Request Topup</button>
    </div>
    
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Available Inventory</div>
        <div class="text-3xl font-bold mb-4">120 <span class="text-sm font-normal text-muted">Vouchers</span></div>
        <button class="btn btn-secondary w-full" style="width: 100%">View Inventory</button>
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
                    <th>Profit</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-muted">No sales yet today.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
