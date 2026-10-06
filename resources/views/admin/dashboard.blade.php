@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Overview</h1>
    <a href="{{ route('admin.vouchers.generate') }}" class="btn btn-primary">Generate Vouchers</a>
</div>

<div class="grid md:grid-cols-3 gap-6 mb-6">
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Total Revenue</div>
        <div class="text-3xl font-bold">{{ number_format($totalRevenue, 3) }} <span class="text-sm font-normal text-muted">LYD</span></div>
    </div>
    
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Active Resellers</div>
        <div class="text-3xl font-bold">{{ number_format($activeResellers) }}</div>
    </div>

    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Vouchers Sold Today</div>
        <div class="text-3xl font-bold">{{ number_format($vouchersSoldToday) }}</div>
    </div>
</div>

<div class="card">
    <h2 class="text-lg font-semibold mb-4">Recent Transactions</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $txn)
                <tr>
                    <td class="font-semibold">{{ $txn->reference_no }}</td>
                    <td class="capitalize">{{ str_replace('_', ' ', $txn->type) }}</td>
                    <td>{{ $txn->type == 'wallet_topup' || $txn->type == 'purchase' ? '+' : '' }}{{ number_format($txn->total_amount, 3) }} LYD</td>
                    <td><span class="badge badge-{{ $txn->status === 'completed' ? 'success' : 'primary' }}">{{ ucfirst($txn->status) }}</span></td>
                    <td class="text-muted">{{ $txn->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No recent transactions.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
