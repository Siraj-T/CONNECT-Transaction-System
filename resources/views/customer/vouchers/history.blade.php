@extends('layouts.app')
@section('title', 'My Vouchers')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Vouchers</h1>
    <a href="{{ route('customer.vouchers.redeem') }}" class="btn btn-primary">Redeem a Voucher</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Voucher Code</th>
                    <th>Plan</th>
                    <th>Redeemed At</th>
                    <th>Expires At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td class="font-mono">CONN-****-****-{{ substr($voucher->code, -4) }}</td>
                    <td>{{ $voucher->plan->name }}</td>
                    <td>{{ $voucher->redeemed_at->format('M d, Y H:i') }}</td>
                    <td>{{ $voucher->expires_at ? $voucher->expires_at->format('M d, Y H:i') : 'N/A' }}</td>
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
                    <td colspan="5" class="text-center text-muted">You haven't redeemed any vouchers yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($vouchers->hasPages())
        <div style="margin-top: 20px;">
            {{ $vouchers->links('pagination.apple') }}
        </div>
    @endif
</div>
@endsection
