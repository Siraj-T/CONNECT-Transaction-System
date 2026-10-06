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
        <div class="text-3xl font-bold">124,500 <span class="text-sm font-normal text-muted">LYD</span></div>
    </div>
    
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Active Resellers</div>
        <div class="text-3xl font-bold">42</div>
    </div>

    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Vouchers Sold Today</div>
        <div class="text-3xl font-bold">856</div>
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
                <tr>
                    <td class="font-semibold">TXN-20261006-001</td>
                    <td>Reseller Topup</td>
                    <td>+500.000 LYD</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td class="text-muted">Just now</td>
                </tr>
                <tr>
                    <td class="font-semibold">TXN-20261006-002</td>
                    <td>Voucher Batch Gen</td>
                    <td>0.000 LYD</td>
                    <td><span class="badge badge-primary">Completed</span></td>
                    <td class="text-muted">1 hour ago</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
