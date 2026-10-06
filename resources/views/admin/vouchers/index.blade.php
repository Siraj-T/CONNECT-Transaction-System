@extends('layouts.app')
@section('title', 'All Vouchers')

@section('content')
<div class="page-header">
    <h1 class="page-title">Voucher Inventory</h1>
    <a href="{{ route('admin.vouchers.generate') }}" class="btn btn-primary">Generate Vouchers</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Reseller</th>
                    <th>Redeemed By</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td class="font-mono font-semibold">{{ $voucher->code }}</td>
                    <td>{{ $voucher->plan->name ?? 'Unknown' }}</td>
                    <td>
                        <span class="badge badge-{{ $voucher->status === 'available' ? 'success' : ($voucher->status === 'reserved' ? 'primary' : 'secondary') }}">
                            {{ ucfirst($voucher->status) }}
                        </span>
                    </td>
                    <td>{{ $voucher->seller->name ?? '-' }}</td>
                    <td>{{ $voucher->redeemer->name ?? '-' }}</td>
                    <td>{{ $voucher->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No vouchers generated yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($vouchers->hasPages())
        <div style="padding: 16px 20px;">
            {{ $vouchers->links('pagination.apple') }}
        </div>
    @endif
</div>
@endsection
