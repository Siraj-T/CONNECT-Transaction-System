@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>

<div class="card" style="max-width: 800px;">
    <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email ?? '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-input" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', isset($user) ? $user->roles->first()?->name : '') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Password {{ isset($user) ? '(Leave blank to keep current)' : '' }}</label>
                <input type="password" name="password" class="form-input" {{ isset($user) ? '' : 'required' }}>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>

            <div class="form-group" style="grid-column: span 2; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <label for="is_active" class="form-label" style="margin: 0; cursor: pointer;">Account is Active</label>
            </div>
        </div>

        <!-- Optional Profile Fields -->
        <h3 class="text-lg font-semibold mt-8 mb-4">Profile Details (Role Specific)</h3>
        <div class="grid md:grid-cols-2 gap-6 p-4" style="background: var(--color-surface-hover); border-radius: var(--radius-md);">
            <!-- Reseller Fields -->
            <div class="form-group">
                <label class="form-label">Business Name (Reseller)</label>
                <input type="text" name="business_name" class="form-input" value="{{ old('business_name', $user->resellerProfile->business_name ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Commission Rate % (Reseller)</label>
                <input type="number" step="0.01" name="commission_rate" class="form-input" value="{{ old('commission_rate', $user->resellerProfile->commission_rate ?? '5.00') }}">
            </div>
            
            <!-- Customer Fields -->
            <div class="form-group">
                <label class="form-label">National ID (Customer)</label>
                <input type="text" name="national_id" class="form-input" value="{{ old('national_id', $user->customerProfile->national_id ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Address (Customer)</label>
                <input type="text" name="address" class="form-input" value="{{ old('address', $user->customerProfile->address ?? '') }}">
            </div>
        </div>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--color-border); text-align: right;">
            <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update User' : 'Create User' }}</button>
        </div>
    </form>
</div>
@endsection
