@extends('layouts.app')
@section('title', 'Customer Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Account</h1>
    <button class="btn btn-primary">Redeem Voucher</button>
</div>

<div class="card mb-6">
    <h2 class="text-lg font-semibold mb-4">Active Plan</h2>
    
    <div style="padding: 24px; border: 1px solid var(--color-border); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div class="text-xl font-bold mb-1">10GB - 7 Days Standard</div>
            <div class="text-muted">Expires in 3 days, 14 hours</div>
        </div>
        <div style="text-align: right;">
            <div class="text-2xl font-bold" style="color: var(--color-primary)">4.2 GB</div>
            <div class="text-sm text-muted">Remaining</div>
        </div>
    </div>
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
                <tr>
                    <td class="font-mono">CONN-****-****-1234</td>
                    <td>10GB - 7 Days Standard</td>
                    <td>Oct 03, 2026</td>
                    <td><span class="badge badge-success">Active</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
