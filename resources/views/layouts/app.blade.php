<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CONNECT') }} - @yield('title')</title>

    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-primary)">
                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                    <polyline points="16 6 12 2 8 6"></polyline>
                    <line x1="12" y1="2" x2="12" y2="15"></line>
                </svg>
                CONNECT
            </div>

            <nav>
                @if(Auth::user()->hasRole('admin'))
                    <div class="text-xs font-semibold text-muted mb-2 px-3 mt-4">ADMIN</div>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users & Roles</a>
                    <a href="{{ route('admin.voucher-plans.index') }}" class="nav-link {{ request()->routeIs('admin.voucher-plans.*') ? 'active' : '' }}">Voucher Plans</a>
                    <a href="#" class="nav-link">Transactions</a>
                @elseif(Auth::user()->hasRole('reseller'))
                    <div class="text-xs font-semibold text-muted mb-2 px-3 mt-4">RESELLER</div>
                    <a href="{{ route('reseller.dashboard') }}" class="nav-link {{ request()->routeIs('reseller.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('reseller.vouchers.buy') }}" class="nav-link {{ request()->routeIs('reseller.vouchers.buy') ? 'active' : '' }}">Buy Vouchers</a>
                    <a href="#" class="nav-link">My Inventory</a>
                    <a href="#" class="nav-link">Transaction History</a>
                @elseif(Auth::user()->hasRole('customer'))
                    <div class="text-xs font-semibold text-muted mb-2 px-3 mt-4">CUSTOMER</div>
                    <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('customer.vouchers.redeem') }}" class="nav-link {{ request()->routeIs('customer.vouchers.redeem') ? 'active' : '' }}">Redeem Voucher</a>
                    <a href="#" class="nav-link">My Vouchers</a>
                @endif
            </nav>

            <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid var(--color-border);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding: 0 12px;">
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%;">
                    <div>
                        <div style="font-size: 14px; font-weight: 500;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link" style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer;">
                        Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="card mb-6" style="background: rgba(52, 199, 89, 0.1); border-color: rgba(52, 199, 89, 0.2); color: var(--color-success); padding: 16px;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="card mb-6" style="background: rgba(255, 59, 48, 0.1); border-color: rgba(255, 59, 48, 0.2); color: var(--color-danger); padding: 16px;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
