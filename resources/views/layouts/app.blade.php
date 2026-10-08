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
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                CONNECT
            </div>

            <nav>
                @if(Auth::user()->hasRole('admin'))
                    <div class="text-xs font-semibold text-muted mb-2 px-3 mt-4">ADMIN</div>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                @endif
            </nav>

            <div class="sidebar-bottom">
                <div class="user-profile">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-surface); border: 1px solid var(--color-border); display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-text-muted);">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 14px; font-weight: 600; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">{{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}</div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" style="display: flex;">
                    @csrf
                    <button type="submit" class="logout-btn" aria-label="Log Out">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="main-content">
            <button id="sidebarToggle" class="burger-btn" aria-label="Toggle Sidebar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

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

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('sidebar-collapsed');
            document.getElementById('main-content').classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>
