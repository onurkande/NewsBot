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
        <div class="nav-label">Havuz Yonetimi</div>
        <a class="nav-link {{ request()->routeIs('admin.pool-settings.*') ? 'is-active' : '' }}" href="{{ route('admin.pool-settings.edit') }}">
            <svg viewBox="0 0 24 24">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <span>Havuz Ayarlari</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.pool-selection.*') ? 'is-active' : '' }}" href="{{ route('admin.pool-selection.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <span>Tweet Havuzu</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.pool-history.*') ? 'is-active' : '' }}" href="{{ route('admin.pool-history.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
            </svg>
            <span>Havuz Gecmisi</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">AI Yonetimi</div>
        <a class="nav-link {{ request()->routeIs('admin.ai-settings.*') ? 'is-active' : '' }}" href="{{ route('admin.ai-settings.edit') }}">
            <svg viewBox="0 0 24 24">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <span>AI Ayarlari</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.ai-queue.*') ? 'is-active' : '' }}" href="{{ route('admin.ai-queue.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
            </svg>
            <span>AI Kuyrugu</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.ai-generations.*') ? 'is-active' : '' }}" href="{{ route('admin.ai-generations.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
            </svg>
            <span>AI Uretimleri</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.prompts.*') ? 'is-active' : '' }}" href="{{ route('admin.prompts.index') }}">
            <svg viewBox="0 0 24 24">
                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z" />
                <line x1="9" y1="9" x2="10" y2="9" />
                <line x1="9" y1="13" x2="15" y2="13" />
                <line x1="9" y1="17" x2="15" y2="17" />
            </svg>
            <span>Prompt Yonetimi</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Yayin Yonetimi</div>
        <a class="nav-link {{ request()->routeIs('admin.publish-settings.*') ? 'is-active' : '' }}" href="{{ route('admin.publish-settings.edit') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            <span>Yayin Ayarlari</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.publish-test.*') ? 'is-active' : '' }}" href="{{ route('admin.publish-test.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
            <span>Yayin Testi</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.publish-accounts.*') ? 'is-active' : '' }}" href="{{ route('admin.publish-accounts.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Hesaplar</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.publish-queue.*') ? 'is-active' : '' }}" href="{{ route('admin.publish-queue.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>Yayin Kuyrugu</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.publish-history.*') ? 'is-active' : '' }}" href="{{ route('admin.publish-history.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Yayin Gecmisi</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Twscrape Yönetimi</div>
        <a class="nav-link {{ request()->routeIs('admin.twscrape.accounts.*') ? 'is-active' : '' }}" href="{{ route('admin.twscrape.accounts.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Hesaplar</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.twscrape.stats.*') ? 'is-active' : '' }}" href="{{ route('admin.twscrape.stats.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
            </svg>
            <span>Kullanım İstatistikleri</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.twscrape.commands.*') ? 'is-active' : '' }}" href="{{ route('admin.twscrape.commands.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="4 17 10 11 4 5"></polyline>
                <line x1="12" y1="19" x2="20" y2="19"></line>
            </svg>
            <span>Komutlar</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.twscrape.health.*') ? 'is-active' : '' }}" href="{{ route('admin.twscrape.health.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            <span>Sağlık Durumu</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.twscrape.logs.*') ? 'is-active' : '' }}" href="{{ route('admin.twscrape.logs.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>İşlem Logları</span>
        </a>
        <a class="nav-link {{ request()->routeIs('admin.twscrape.tweet-test.*') ? 'is-active' : '' }}" href="{{ route('admin.twscrape.tweet-test.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
            <span>Tweet Testi</span>
        </a>
    </nav>

    {{--
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
    --}}

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
