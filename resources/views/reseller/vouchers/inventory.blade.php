@extends('layouts.app')
@section('title', 'My Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Voucher Inventory</h1>
    <a href="{{ route('reseller.vouchers.buy') }}" class="btn btn-primary">Buy More Vouchers</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Voucher Code</th>
                    <th>Plan</th>
                    <th>Duration</th>
                    <th>Cost Price</th>
                    <th>Retail Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td class="font-mono font-semibold">{{ $voucher->code }}</td>
                    <td>{{ $voucher->plan->name }}</td>
                    <td>{{ $voucher->plan->duration_days }} Days</td>
                    <td>{{ number_format($voucher->plan->reseller_price_lyd, 3) }} LYD</td>
                    <td>{{ number_format($voucher->plan->retail_price_lyd, 3) }} LYD</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">You have no vouchers in your inventory. Buy some to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($vouchers->hasPages())
        <div style="margin-top: 20px;">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>
@endsection
