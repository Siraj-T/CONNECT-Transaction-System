@extends('layouts.app')
@section('title', 'Voucher Plans')

@section('content')
<div class="page-header">
    <h1 class="page-title">Voucher Plans</h1>
    <a href="{{ route('admin.voucher-plans.create') }}" class="btn btn-primary">Create New Plan</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Data Limit</th>
                    <th>Duration</th>
                    <th>Retail Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 16px; height: 16px; border-radius: 4px; background-color: {{ $plan->color_hex }};"></div>
                            <span class="font-semibold">{{ $plan->name }}</span>
                        </div>
                    </td>
                    <td>{{ $plan->data_limit_gb ? $plan->data_limit_gb . ' GB' : 'Unlimited' }}</td>
                    <td>{{ $plan->duration_days }} Days</td>
                    <td>{{ number_format($plan->retail_price_lyd, 3) }} LYD</td>
                    <td>
                        @if($plan->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.voucher-plans.edit', $plan) }}" class="text-sm font-medium" style="margin-right: 12px;">Edit</a>
                        <form action="{{ route('admin.voucher-plans.destroy', $plan) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-danger" style="border: none; background: none; color: var(--color-danger); cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding: 32px; text-align: center;">No voucher plans created yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
