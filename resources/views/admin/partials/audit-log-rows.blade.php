@forelse($auditLogs as $log)
<tr>
    <td class="text-muted" style="white-space: nowrap;">
        {{ $log->created_at->format('M d, Y H:i:s') }}
    </td>
    <td>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 26px; height: 26px; border-radius: 50%; background: var(--color-bg); border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--color-primary);">
                {{ strtoupper(substr($log->admin->name ?? 'A', 0, 1)) }}
            </div>
            <span class="font-medium">{{ $log->admin->name ?? 'Admin' }}</span>
        </div>
    </td>
    <td>
        @if(str_contains($log->action, 'accept'))
            <span class="badge badge-success">Accepted</span>
        @elseif(str_contains($log->action, 'reject'))
            <span class="badge badge-danger">Rejected</span>
        @else
            <span class="badge badge-primary">{{ ucfirst($log->action) }}</span>
        @endif
    </td>
    <td class="font-semibold">{{ $log->reference_number ?? '—' }}</td>
    <td class="text-muted" style="font-size: 13px;">{{ $log->details }}</td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center text-muted py-8">
        No audit entries yet. Approving or rejecting a transaction will record forensic history here.
    </td>
</tr>
@endforelse
