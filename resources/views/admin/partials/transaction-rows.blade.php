@forelse($recentTransactions as $txn)
<tr>
    <td class="font-semibold">{{ $txn->unique_reference_number }}</td>
    <td>{{ $txn->citizen->name }}</td>
    <td>{{ $txn->citizen->phone_number }}</td>
    <td>
        <span class="badge" style="background: #eef2f7; color: #475569;">
            {{ $txn->paymentMethod->name }}
        </span>
    </td>
    <td class="font-bold">${{ number_format($txn->amount, 2) }}</td>
    <td>
        <span class="badge badge-{{ $txn->status === 'accepted' ? 'success' : ($txn->status === 'rejected' ? 'danger' : 'primary') }}">
            {{ ucfirst($txn->status) }}
        </span>
    </td>
    <td class="text-muted">{{ $txn->created_at->format('M d, Y H:i') }}</td>
    <td class="text-right" style="white-space: nowrap;">
        @if($txn->status === 'pending')
        <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 8px;">
            <form action="{{ route('admin.transactions.status.update', $txn) }}" method="POST" style="margin: 0; display: inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="accept">
                <button type="submit" class="action-btn action-btn-accept" title="Accept Transaction" aria-label="Accept Transaction">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
            </form>
            <form action="{{ route('admin.transactions.status.update', $txn) }}" method="POST" style="margin: 0; display: inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="action-btn action-btn-reject" title="Reject Transaction" aria-label="Reject Transaction">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </form>
        </div>
        @else
        <span class="text-muted text-sm" style="font-weight: 500;">Processed</span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted py-8">No transactions found.</td>
</tr>
@endforelse
