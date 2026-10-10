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
            <div class="brand">
                FINANCIAL TRANSACTIONS
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

            <div class="page-header">
                <h1 class="page-title">Overview</h1>
            </div>
            
            <div class="grid md:grid-cols-4 gap-6 mb-6">
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
                                <td>{{ $txn->sender_name }}</td>
                                <td>{{ $txn->sender_phone }}</td>
                                <td class="font-bold">${{ number_format($txn->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $txn->status === 'accepted' ? 'success' : ($txn->status === 'rejected' ? 'danger' : 'primary') }}">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-right">
                                    @if($txn->status === 'pending')
                                    <form action="{{ route('admin.transactions.status.update', $txn) }}" method="POST" class="inline-block m-0">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="accept">
                                        <button type="submit" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; background: #2ecc71; color: white;">Accept</button>
                                    </form>
                                    <form action="{{ route('admin.transactions.status.update', $txn) }}" method="POST" class="inline-block m-0 ml-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; background: #e74c3c; color: white;">Reject</button>
                                    </form>
                                    @else
                                    <span class="text-muted text-sm">Processed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-8">No transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($recentTransactions->hasPages())
                <div class="mt-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-700 leading-5">
                            Showing <span class="font-medium">{{ $recentTransactions->firstItem() }}</span> to <span class="font-medium">{{ $recentTransactions->lastItem() }}</span> of <span class="font-medium">{{ $recentTransactions->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                            @if ($recentTransactions->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-500 cursor-default rounded-l-md">Previous</span>
                            @else
                                <a href="{{ $recentTransactions->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 rounded-l-md">Previous</a>
                            @endif
            
                            @if ($recentTransactions->hasMorePages())
                                <a href="{{ $recentTransactions->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 rounded-r-md">Next</a>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-500 cursor-default rounded-r-md">Next</span>
                            @endif
                        </span>
                    </div>
                </div>
                @endif
            </div>

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
