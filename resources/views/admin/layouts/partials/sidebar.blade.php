<aside class="d-sidebar">
    <div class="brand">
        <div class="brand-logo">
            <svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff"
                    d="M14.747 9.125c.527-1.426 1.736-2.573 3.317-2.573c1.643 0 2.792 1.085 3.318 2.573l6.077 16.867c.186.496.248.931.248 1.147c0 1.209-.992 2.046-2.139 2.046c-1.303 0-1.954-.682-2.264-1.611l-.931-2.915h-8.62l-.93 2.884c-.31.961-.961 1.642-2.232 1.642c-1.24 0-2.294-.93-2.294-2.17c0-.496.155-.868.217-1.023l6.233-16.867zm.34 11.256h5.891l-2.883-8.992h-.062l-2.946 8.992z" />
            </svg>
        </div>
        <div class="brand-text">
            <div class="brand-name">Adminator</div>
            <div class="brand-tag">v4.1.5 · preview</div>
        </div>
    </div>

    <nav class="nav-section">
        <div class="nav-label">Workspace</div>
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
            <svg viewBox="0 0 24 24">
                <path d="M3 12 12 3l9 9" />
                <path d="M5 10v10h14V10" />
            </svg>
            <span>Dashboard</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Haber Toplama</div>
        <a class="nav-link {{ request()->routeIs('admin.source-accounts.*') ? 'is-active' : '' }}" href="{{ route('admin.source-accounts.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M16 8a6 6 0 0 1 6 6v5h-4v-5a2 2 0 0 0-4 0v5h-4v-5a6 6 0 0 1 6-6z" />
                <path d="M2 9h4v10H2z" />
                <circle cx="4" cy="4" r="2" />
            </svg>
            <span>Kaynak Hesaplar</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.source-categories.*') ? 'is-active' : '' }}" href="{{ route('admin.source-categories.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M4 7h16M4 12h16M4 17h10" />
            </svg>
            <span>Kategoriler</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.raw-tweets.*') ? 'is-active' : '' }}" href="{{ route('admin.raw-tweets.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            <span>Ham Tweetler</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.story-clusters.*') ? 'is-active' : '' }}" href="{{ route('admin.story-clusters.index') }}">
            <svg viewBox="0 0 24 24">
                <circle cx="6" cy="12" r="3" />
                <circle cx="18" cy="6" r="3" />
                <circle cx="18" cy="18" r="3" />
                <path d="M8.7 10.7 15.3 7.3M8.7 13.3l6.6 3.4" />
            </svg>
            <span>Story Cluster</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.scan-histories.*') ? 'is-active' : '' }}" href="{{ route('admin.scan-histories.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M3 3v18h18" />
                <path d="M7 15l4-4 3 3 5-7" />
            </svg>
            <span>Tarama Geçmişi</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Örnek Başlıklar</div>
        <a class="nav-link" href="charts.html">
            <svg viewBox="0 0 24 24">
                <path d="M3 20V4M7 20v-6M11 20v-10M15 20v-4M19 20V8" />
            </svg>
            <span>Charts</span>
            <span class="nav-badge new">NEW</span>
        </a>

        <div class="nav-item-group">
            <a class="nav-link" href="javascript:void(0)" data-nav-toggle>
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="16" rx="2" />
                    <path d="M3 10h18M3 16h18M9 4v16" />
                </svg>
                <span>Tables</span>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </a>
            <div class="nav-submenu">
                <a href="basic-table.html">Basic Table</a>
                <a href="datatable.html">Data Table</a>
            </div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="workspace">
            <div class="workspace-avatar">JD</div>
            <div class="workspace-text">
                <div class="workspace-name">John Doe</div>
                <div class="workspace-role">admin</div>
            </div>
            <svg class="workspace-chev" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8">
                <path d="m7 9 5-5 5 5" />
                <path d="m7 15 5 5 5-5" />
            </svg>
        </div>
    </div>
</aside>
