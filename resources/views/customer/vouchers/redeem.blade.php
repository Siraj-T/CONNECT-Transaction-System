@extends('layouts.app')
@section('title', 'Redeem Voucher')

@section('content')
<div class="page-header">
    <h1 class="page-title">Redeem Voucher</h1>
    <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="card" style="max-width: 500px; margin: 0 auto; text-align: center; padding: 48px 32px;">
    
    <div style="width: 80px; height: 80px; background: rgba(0, 113, 227, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-primary);">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
    </div>

    <h2 class="text-xl font-bold mb-2">Enter your voucher code</h2>
    <p class="text-muted mb-8 text-sm">Scratch the card provided by your reseller to reveal the 12-character code.</p>

    <form action="{{ route('customer.vouchers.redeem.store') }}" method="POST">
        @csrf
        <div class="form-group" style="text-align: left;">
            <input type="text" name="voucher_code" class="form-input" style="font-size: 24px; text-align: center; letter-spacing: 2px; text-transform: uppercase;" placeholder="CONN-XXXX-XXXX-XXXX" required>
            @error('voucher_code') <span class="text-sm text-danger mt-2" style="display: block; text-align: center;">{{ $message }}</span> @enderror
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 16px; margin-top: 16px;">Redeem Now</button>
    </form>
</div>
@endsection
