<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Financial Transaction System') }} - Audit Logs</title>
    <script>
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
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
            <div class="page-header" style="margin-bottom: 24px;">
                <h1 class="page-title" style="margin-bottom: 4px;">Audit Trail</h1>
                <p class="text-sm text-muted" style="margin: 0;">Forensic record of all administrative transaction decisions and operator logs.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
                <div class="card">
                    <div class="text-sm font-semibold text-muted mb-2">Total Decisions Logged</div>
                    <div class="text-3xl font-bold" id="metricTotalDecisions">{{ number_format($totalDecisions) }}</div>
                </div>
                
                <div class="card">
                    <div class="text-sm font-semibold text-muted mb-2">Approved Decisions</div>
                    <div class="text-3xl font-bold text-green-600" id="metricAcceptedDecisions">{{ number_format($acceptedDecisions) }}</div>
                </div>
            
                <div class="card">
                    <div class="text-sm font-semibold text-muted mb-2">Rejected Decisions</div>
                    <div class="text-3xl font-bold text-red-600" id="metricRejectedDecisions">{{ number_format($rejectedDecisions) }}</div>
                </div>
            </div>
            
            <div class="card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <h2 class="text-lg font-semibold" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-primary);">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                            Audit History Records
                        </h2>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="badge" id="auditCountBadge" style="background: #eef2f7; color: #475569; font-size: 12px; font-weight: 600;">
                            {{ $auditLogs->total() }} Records
                        </span>
                        <button id="manualRefreshBtn" type="button" title="Refresh audit logs" style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: var(--color-text-muted); transition: all 0.2s;">
                            <svg id="refreshIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.4s ease;">
                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Admin Operator</th>
                                <th>Action Taken</th>
                                <th>Transaction Ref</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="auditLogsTableBody">
                            @fragment('audit-log-rows')
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
                            @endfragment
                        </tbody>
                    </table>
                </div>

                {{-- Always Visible Pagination with Arrows --}}
                <div id="auditLogsPagination">
                    @fragment('pagination')
                    <div class="pagination-container" style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border); flex-wrap: wrap; gap: 12px;">
                        <div>
                            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                                Showing 
                                <span style="font-weight: 600; color: var(--color-text-main);">{{ $auditLogs->total() > 0 ? ($auditLogs->firstItem() ?? 1) : 0 }}</span>
                                to 
                                <span style="font-weight: 600; color: var(--color-text-main);">{{ $auditLogs->total() > 0 ? ($auditLogs->lastItem() ?? 1) : 0 }}</span>
                                of 
                                <span style="font-weight: 600; color: var(--color-text-main);">{{ $auditLogs->total() }}</span> results
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            {{-- Previous Arrow Button --}}
                            @if ($auditLogs->onFirstPage())
                                <span class="pagination-arrow-btn disabled" aria-disabled="true" title="Previous page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-muted); opacity: 0.4; cursor: not-allowed;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $auditLogs->previousPageUrl() }}" class="pagination-arrow-btn" title="Previous page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-main); text-decoration: none;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </a>
                            @endif

                            {{-- Current Page Indicator --}}
                            <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; border-radius: 8px; background: var(--color-primary); color: white; font-size: 13px; font-weight: 600;">
                                {{ $auditLogs->currentPage() }}
                            </span>

                            {{-- Next Arrow Button --}}
                            @if ($auditLogs->hasMorePages())
                                <a href="{{ $auditLogs->nextPageUrl() }}" class="pagination-arrow-btn" title="Next page" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-main); text-decoration: none;">
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
                    @endfragment
                </div>
            </div>

        </main>
    </div>

    <script>
        // Sidebar toggle with localStorage persistence
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.getElementById('sidebarToggle');

        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
            if (sidebar) sidebar.classList.add('sidebar-collapsed');
            if (mainContent) mainContent.classList.add('sidebar-collapsed');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                const isCurrentlyCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                const nextState = !isCurrentlyCollapsed;

                document.documentElement.classList.toggle('sidebar-collapsed', nextState);
                if (sidebar) sidebar.classList.toggle('sidebar-collapsed', nextState);
                if (mainContent) mainContent.classList.toggle('sidebar-collapsed', nextState);

                localStorage.setItem('sidebar_collapsed', nextState ? 'true' : 'false');
            });
        }

        // --- Efficient On-Demand & Focus-Driven Controller (Zero Periodic Polling) ---
        (function() {
            let currentVersion = "{{ $initialVersion }}";
            const liveUrl = "{{ route('admin.audit-logs.live') }}";
            const currentPage = {{ $auditLogs->currentPage() }};
            
            let currentAbortCtrl = null;
            let isFetching = false;

            const elTotal = document.getElementById('metricTotalDecisions');
            const elAccepted = document.getElementById('metricAcceptedDecisions');
            const elRejected = document.getElementById('metricRejectedDecisions');
            const elTbody = document.getElementById('auditLogsTableBody');
            const elPagination = document.getElementById('auditLogsPagination');
            const elBadge = document.getElementById('auditCountBadge');
            const refreshBtn = document.getElementById('manualRefreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');

            async function fetchLiveUpdate(forced = false) {
                if (isFetching) return;
                isFetching = true;

                if (refreshIcon) {
                    refreshIcon.style.transform = 'rotate(180deg)';
                }

                if (currentAbortCtrl) {
                    currentAbortCtrl.abort();
                }
                currentAbortCtrl = new AbortController();

                try {
                    const checkVer = forced ? '' : currentVersion;
                    const res = await fetch(`${liveUrl}?v=${encodeURIComponent(checkVer)}&page=${currentPage}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: currentAbortCtrl.signal
                    });

                    if (res.ok) {
                        const data = await res.json();
                        if (data.changed) {
                            currentVersion = data.version;
                            if (data.metrics) {
                                if (elTotal) elTotal.textContent = data.metrics.totalDecisions;
                                if (elAccepted) elAccepted.textContent = data.metrics.acceptedDecisions;
                                if (elRejected) elRejected.textContent = data.metrics.rejectedDecisions;
                            }
                            if (data.rowsHtml && elTbody) {
                                elTbody.innerHTML = data.rowsHtml;
                            }
                            if (data.paginationHtml && elPagination) {
                                elPagination.innerHTML = data.paginationHtml;
                            }
                        }
                    }
                } catch (err) {
                    // Ignore aborted requests
                } finally {
                    isFetching = false;
                    currentAbortCtrl = null;
                    if (refreshIcon) {
                        setTimeout(() => {
                            refreshIcon.style.transform = 'rotate(0deg)';
                        }, 300);
                    }
                }
            }

            // 1. Manual 1-click refresh button
            if (refreshBtn) {
                refreshBtn.addEventListener('click', () => fetchLiveUpdate(true));
            }

            // 2. Sync only when returning to this browser tab (Zero background polling)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    fetchLiveUpdate(false);
                }
            });

            // Clean up aborted fetches on exit
            window.addEventListener('pagehide', function() {
                if (currentAbortCtrl) currentAbortCtrl.abort();
            });
        })();
    </script>
</body>
</html>
