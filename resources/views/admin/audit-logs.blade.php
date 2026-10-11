<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Financial Transaction System') }} - Audit Logs</title>
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
            <div class="page-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h1 class="page-title" style="margin-bottom: 4px;">Audit Trail</h1>
                    <p class="text-sm text-muted" style="margin: 0;">Forensic record of all administrative transaction decisions and operator logs.</p>
                </div>
                <div class="live-badge" title="Live Auto-Refresh active (pauses when tab is inactive to eliminate overhead)">
                    <span class="live-dot"></span>
                    <span>Live Updates</span>
                </div>
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
                    <span class="badge" id="auditCountBadge" style="background: #eef2f7; color: #475569; font-size: 12px; font-weight: 600;">
                        {{ $auditLogs->total() }} Records
                    </span>
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
                            @include('admin.partials.audit-log-rows', ['auditLogs' => $auditLogs])
                        </tbody>
                    </table>
                </div>

                {{-- Always Visible Pagination with Arrows --}}
                <div id="auditLogsPagination">
                    @include('admin.partials.pagination', ['paginator' => $auditLogs])
                </div>
            </div>

        </main>
    </div>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.getElementById('sidebarToggle');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
            });
        }

        // --- Zero-Leak Live Auto-Refresh Controller ---
        (function() {
            let currentVersion = "{{ $initialVersion }}";
            const liveUrl = "{{ route('admin.audit-logs.live') }}";
            const currentPage = {{ $auditLogs->currentPage() }};
            const pollIntervalMs = 4000; // 4 seconds
            
            let pollTimeout = null;
            let currentAbortCtrl = null;

            const elTotal = document.getElementById('metricTotalDecisions');
            const elAccepted = document.getElementById('metricAcceptedDecisions');
            const elRejected = document.getElementById('metricRejectedDecisions');
            const elTbody = document.getElementById('auditLogsTableBody');
            const elPagination = document.getElementById('auditLogsPagination');
            const elBadge = document.getElementById('auditCountBadge');

            function scheduleNextPoll(delay) {
                if (document.hidden) return; // Zero overhead: pause when inactive
                if (pollTimeout) clearTimeout(pollTimeout);
                pollTimeout = setTimeout(fetchLiveUpdate, delay);
            }

            async function fetchLiveUpdate() {
                if (document.hidden) return;

                if (currentAbortCtrl) {
                    currentAbortCtrl.abort();
                }
                currentAbortCtrl = new AbortController();

                try {
                    const res = await fetch(`${liveUrl}?v=${encodeURIComponent(currentVersion)}&page=${currentPage}`, {
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
                    if (err.name !== 'AbortError') {
                        scheduleNextPoll(8000);
                        return;
                    }
                } finally {
                    currentAbortCtrl = null;
                }

                scheduleNextPoll(pollIntervalMs);
            }

            // Pause polling when tab is hidden, resume immediately when tab is visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (pollTimeout) {
                        clearTimeout(pollTimeout);
                        pollTimeout = null;
                    }
                    if (currentAbortCtrl) {
                        currentAbortCtrl.abort();
                        currentAbortCtrl = null;
                    }
                } else {
                    fetchLiveUpdate();
                }
            });

            // Clean up timers & pending fetches when navigating away
            window.addEventListener('pagehide', function() {
                if (pollTimeout) clearTimeout(pollTimeout);
                if (currentAbortCtrl) currentAbortCtrl.abort();
            });

            // Start polling loop
            scheduleNextPoll(pollIntervalMs);
        })();
    </script>
</body>
</html>
