@extends('layouts.app')
@section('title', isset($voucherPlan) ? 'Edit Plan' : 'Create Plan')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ isset($voucherPlan) ? 'Edit Voucher Plan' : 'Create Voucher Plan' }}</h1>
    <a href="{{ route('admin.voucher-plans.index') }}" class="btn btn-secondary">Back to Plans</a>
</div>

<div class="card" style="max-width: 800px;">
    <form action="{{ isset($voucherPlan) ? route('admin.voucher-plans.update', $voucherPlan) : route('admin.voucher-plans.store') }}" method="POST">
        @csrf
        @if(isset($voucherPlan))
            @method('PUT')
        @endif

        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">Plan Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $voucherPlan->name ?? '') }}" required placeholder="e.g. 10GB - 7 Days">
                @error('name') <span class="text-sm" style="color: var(--color-danger)">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Color Hex</label>
                <input type="color" name="color_hex" class="form-input" value="{{ old('color_hex', $voucherPlan->color_hex ?? '#0071E3') }}" style="height: 46px; padding: 4px;" required>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-input" rows="3">{{ old('description', $voucherPlan->description ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Data Limit (GB)</label>
                <input type="number" step="0.01" name="data_limit_gb" class="form-input" value="{{ old('data_limit_gb', $voucherPlan->data_limit_gb ?? '') }}" placeholder="Leave blank for unlimited">
            </div>

            <div class="form-group">
                <label class="form-label">Duration (Days)</label>
                <input type="number" name="duration_days" class="form-input" value="{{ old('duration_days', $voucherPlan->duration_days ?? '30') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Admin Cost (LYD)</label>
                <input type="number" step="0.001" name="price_lyd" class="form-input" value="{{ old('price_lyd', $voucherPlan->price_lyd ?? '0.000') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Reseller Price (LYD)</label>
                <input type="number" step="0.001" name="reseller_price_lyd" class="form-input" value="{{ old('reseller_price_lyd', $voucherPlan->reseller_price_lyd ?? '0.000') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Suggested Retail Price (LYD)</label>
                <input type="number" step="0.001" name="retail_price_lyd" class="form-input" value="{{ old('retail_price_lyd', $voucherPlan->retail_price_lyd ?? '0.000') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Speed Limit (Mbps)</label>
                <input type="number" name="speed_mbps" class="form-input" value="{{ old('speed_mbps', $voucherPlan->speed_mbps ?? '') }}" placeholder="Leave blank for unlimited">
            </div>
            
            <div class="form-group" style="grid-column: span 2; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $voucherPlan->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <label for="is_active" class="form-label" style="margin: 0; cursor: pointer;">Plan is Active</label>
            </div>
        </div>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--color-border); text-align: right;">
            <button type="submit" class="btn btn-primary">{{ isset($voucherPlan) ? 'Update Plan' : 'Create Plan' }}</button>
        </div>
    </form>
</div>
@endsection
