@extends('layouts.app')
@section('title', 'Generate Vouchers')

@section('content')
<div class="page-header">
    <h1 class="page-title">Generate Vouchers</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.vouchers.generate.store') }}" method="POST">
        @csrf

        <div class="form-group mb-6">
            <label class="form-label">Select Voucher Plan</label>
            <select name="voucher_plan_id" class="form-input" required>
                <option value="">-- Choose a Plan --</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->retail_price_lyd, 3) }} LYD)</option>
                @endforeach
            </select>
            @error('voucher_plan_id') <span class="text-sm text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-6">
            <label class="form-label">Quantity to Generate</label>
            <input type="number" name="quantity" class="form-input" value="100" min="1" max="500" required>
            <p class="text-xs text-muted mt-2">Maximum 500 vouchers per batch to ensure performance.</p>
            @error('quantity') <span class="text-sm text-danger">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--color-border); text-align: right;">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to generate these vouchers?');">Generate Batch</button>
        </div>
    </form>
</div>
@endsection
