@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Overview</h1>
</div>

@if(session('success'))
<div class="card mb-6" style="background: rgba(46, 204, 113, 0.2); border-left: 4px solid #2ecc71;">
    <div class="p-4 text-green-800 font-semibold">{{ session('success') }}</div>
</div>
@endif

<div class="grid md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Total Accepted Volume</div>
        <div class="text-3xl font-bold">${{ number_format($totalRevenue, 2) }}</div>
    </div>
    
    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Pending Requests</div>
        <div class="text-3xl font-bold">{{ number_format($pendingTransactions) }}</div>
    </div>

    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Accepted</div>
        <div class="text-3xl font-bold text-green-600">{{ number_format($acceptedTransactions) }}</div>
    </div>

    <div class="card">
        <div class="text-sm font-semibold text-muted mb-2">Rejected</div>
        <div class="text-3xl font-bold text-red-600">{{ number_format($rejectedTransactions) }}</div>
    </div>
</div>

<div class="card">
    <h2 class="text-lg font-semibold mb-4">Transactions</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Sender Name</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $txn)
                <tr>
                    <td class="font-semibold">{{ $txn->unique_reference_number }}</td>
                    <td>{{ $txn->sender_name }}</td>
                    <td>{{ $txn->sender_phone }}</td>
                    <td class="font-bold">${{ number_format($txn->amount, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $txn->status === 'accepted' ? 'success' : ($txn->status === 'rejected' ? 'danger' : 'primary') }}">
                            {{ ucfirst($txn->status) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                    <td class="text-right">
                        @if($txn->status === 'pending')
                        <form action="{{ route('admin.transactions.status.update', $txn) }}" method="POST" class="inline-block m-0">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; background: #2ecc71; color: white;">Accept</button>
                        </form>
                        <form action="{{ route('admin.transactions.status.update', $txn) }}" method="POST" class="inline-block m-0 ml-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; background: #e74c3c; color: white;">Reject</button>
                        </form>
                        @else
                        <span class="text-muted text-sm">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-8">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($recentTransactions->hasPages())
    <div class="mt-6">
        {{ $recentTransactions->links() }}
    </div>
    @endif
</div>
@endsection
