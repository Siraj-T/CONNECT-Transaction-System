<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Financial Transaction System') }} - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; padding: 0 4px;">
                <div class="brand brand-text" style="margin-bottom: 0; padding: 0; font-size: 15px; font-weight: 700; letter-spacing: -0.01em; white-space: nowrap;">
                    FINANCIAL TRANSACTIONS
                </div>
                <button id="sidebarToggle" class="burger-btn" aria-label="Toggle Sidebar" title="Toggle Sidebar" style="margin-bottom: 0; width: 34px; height: 34px; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
            <nav>
                @if(Auth::user()->hasRole('admin'))
                    <div class="sidebar-label text-xs font-semibold text-muted mb-2 px-3 mt-4">ADMIN</div>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon" style="flex-shrink: 0;">
                            <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                            <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                        </svg>
                        <span class="nav-text" style="margin-left: 10px;">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.audit-logs') }}" class="nav-link {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}" title="Audit Logs">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon" style="flex-shrink: 0;">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        <span class="nav-text" style="margin-left: 10px;">Audit Logs</span>
                    </a>
                @endif
            </nav>
            <div class="sidebar-bottom">
                <div class="user-profile">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-surface); border: 1px solid var(--color-border); display: flex; justify-content: center; align-items: center; flex-shrink: 0;" title="{{ Auth::user()->name }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-text-muted);">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="user-details" style="overflow: hidden;">
                        <div style="font-size: 14px; font-weight: 600; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">{{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}</div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" style="display: flex;">
                    @csrf
                    <button type="submit" class="logout-btn" aria-label="Log Out" title="Log Out">
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

            <div class="page-header">
                <h1 class="page-title">Overview</h1>
            </div>
            
            <div class="grid-2x2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px;">
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
                                <th>Payment Method</th>
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
                        </tbody>
                    </table>
                </div>
                
                {{-- Always Visible Pagination with Arrows --}}
                <div class="pagination-container" style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border); flex-wrap: wrap; gap: 12px;">
                    <div>
                        <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                            Showing 
                            <span style="font-weight: 600; color: var(--color-text-main);">{{ $recentTransactions->total() > 0 ? ($recentTransactions->firstItem() ?? 1) : 0 }}</span>
                            to 
                            <span style="font-weight: 600; color: var(--color-text-main);">{{ $recentTransactions->total() > 0 ? ($recentTransactions->lastItem() ?? 1) : 0 }}</span>
                            of 
                            <span style="font-weight: 600; color: var(--color-text-main);">{{ $recentTransactions->total() }}</span> results
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        {{-- Previous Arrow Button --}}
                        @if ($recentTransactions->onFirstPage())
                            <span class="pagination-arrow-btn disabled" aria-disabled="true" title="Previous page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-muted); opacity: 0.4; cursor: not-allowed;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $recentTransactions->previousPageUrl() }}" class="pagination-arrow-btn" title="Previous page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-main); text-decoration: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        @endif

                        {{-- Current Page Indicator --}}
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; border-radius: 8px; background: var(--color-primary); color: white; font-size: 13px; font-weight: 600;">
                            {{ $recentTransactions->currentPage() }}
                        </span>

                        {{-- Next Arrow Button --}}
                        @if ($recentTransactions->hasMorePages())
                            <a href="{{ $recentTransactions->nextPageUrl() }}" class="pagination-arrow-btn" title="Next page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-main); text-decoration: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        @else
                            <span class="pagination-arrow-btn disabled" aria-disabled="true" title="Next page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-muted); opacity: 0.4; cursor: not-allowed;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>
