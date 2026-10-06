@extends('layouts.app')
@section('title', 'All Transactions')

@section('content')
<div class="page-header">
    <h1 class="page-title">All Transactions</h1>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Reference No</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr>
                    <td class="font-mono">{{ $txn->reference_no }}</td>
                    <td>{{ $txn->user->name ?? 'System' }} <br><span class="text-xs text-muted">{{ $txn->user->email ?? '' }}</span></td>
                    <td class="capitalize">{{ str_replace('_', ' ', $txn->type) }}</td>
                    <td>{{ number_format($txn->total_amount, 3) }} LYD</td>
                    <td><span class="badge badge-{{ $txn->status === 'completed' ? 'success' : 'primary' }}">{{ ucfirst($txn->status) }}</span></td>
                    <td>{{ $txn->created_at->format('M d, Y H:i') }}</td>
                    <td class="text-muted">{{ $txn->notes }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($transactions->hasPages())
        <div style="margin-top: 20px;">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
