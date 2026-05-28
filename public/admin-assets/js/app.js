/**
 * 2026 Shell renderer.
 *
 * Each page provides:
 *   <body data-active="dashboard" data-crumbs="Workspace | Dashboard">
 *     <div class="shell">
 *       <div data-shell-sidebar></div>
 *       <div class="main">
 *         <div data-shell-topbar></div>
 *         <main class="content"> ...page content... </main>
 *         <div data-shell-footer></div>
 *       </div>
 *     </div>
 *   </body>
 *
 * mountShell() fills the three placeholder divs with the shared chrome,
 * marking the active sidebar item and writing the breadcrumbs.
 *
 * NAV is the single source of truth — adding a page is one entry here.
 */
const NAV = [
  {
    label: 'Workspace',
    items: [
      { key: 'dashboard', text: 'Dashboard', href: 'index.html',
        icon: '<path d="M3 12 12 3l9 9"/><path d="M5 10v10h14V10"/>' },
      { key: 'docs', text: 'Documentation', href: 'https://adminator.colorlib.com/docs/', badge: { kind: 'new', text: 'DOCS' },
        icon: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>' },
      { key: 'pro', text: 'Go Pro', href: '#', badge: { kind: 'pro', text: 'PRO' },
        icon: '<path d="M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z"/>' },
    ],
  },
  {
    label: 'Communications',
    items: [
      { key: 'email', text: 'Email', href: 'email.html',
        icon: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>' },
      { key: 'compose', text: 'Compose', href: 'compose.html',
        icon: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/>' },
      { key: 'calendar', text: 'Calendar', href: 'calendar.html',
        icon: '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>' },
      { key: 'chat', text: 'Chat', href: 'chat.html',
        icon: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>' },
    ],
  },
  {
    label: 'Components',
    items: [
      { key: 'charts', text: 'Charts', href: 'charts.html', badge: { kind: 'new', text: 'NEW' },
        icon: '<path d="M3 20V4M7 20v-6M11 20v-10M15 20v-4M19 20V8"/>' },
      { key: 'forms', text: 'Forms', href: 'forms.html',
        icon: '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 10h10M7 14h7"/>' },
      { key: 'ui', text: 'UI Elements', href: 'ui.html',
        icon: '<circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/>' },
      { key: 'buttons', text: 'Buttons', href: 'buttons.html',
        icon: '<rect x="3" y="8" width="18" height="8" rx="4"/>' },
      { key: 'tables', text: 'Tables',
        icon: '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M3 16h18M9 4v16"/>',
        children: [
          { key: 'basic-table', text: 'Basic Table', href: 'basic-table.html' },
          { key: 'datatable',   text: 'Data Table',  href: 'datatable.html' },
        ],
      },
      { key: 'maps', text: 'Maps',
        icon: '<path d="M9 20V4l6 4v16z"/><path d="M3 7l6-3v16l-6 3z"/><path d="M15 8l6-3v16l-6 3"/>',
        children: [
          { key: 'google-maps', text: 'Google Map', href: 'google-maps.html' },
          { key: 'vector-maps', text: 'Vector Map', href: 'vector-maps.html' },
        ],
      },
      { key: 'pages', text: 'Pages',
        icon: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
        children: [
          { key: 'blank',   text: 'Blank',   href: 'blank.html' },
          { key: '404',     text: '404',     href: '404.html' },
          { key: '500',     text: '500',     href: '500.html' },
          { key: 'signin',  text: 'Sign In', href: 'signin.html' },
          { key: 'signup',  text: 'Sign Up', href: 'signup.html' },
        ],
      },
    ],
  },
];

const BRAND_LOGO = `<svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
  <path fill="#ffffff" d="M14.747 9.125c.527-1.426 1.736-2.573 3.317-2.573c1.643 0 2.792 1.085 3.318 2.573l6.077 16.867c.186.496.248.931.248 1.147c0 1.209-.992 2.046-2.139 2.046c-1.303 0-1.954-.682-2.264-1.611l-.931-2.915h-8.62l-.93 2.884c-.31.961-.961 1.642-2.232 1.642c-1.24 0-2.294-.93-2.294-2.17c0-.496.155-.868.217-1.023l6.233-16.867zm.34 11.256h5.891l-2.883-8.992h-.062l-2.946 8.992z"/>
</svg>`;

const CHEV = '<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"/></svg>';

function renderNavLink(item, activeKey) {
  const active = item.key === activeKey ? ' is-active' : '';
  const badge = item.badge
    ? `<span class="nav-badge ${item.badge.kind}">${item.badge.text}</span>`
    : '';
  const external = /^https?:\/\//.test(item.href) ? ' target="_blank" rel="noopener noreferrer"' : '';
  return `
    <a class="nav-link${active}" href="${item.href}"${external}>
      <svg viewBox="0 0 24 24">${item.icon}</svg>
      <span>${item.text}</span>
      ${badge}
    </a>`;
}

function renderNavGroup(item, activeKey) {
  const open = item.children.some((c) => c.key === activeKey) ? ' is-open' : '';
  const submenu = item.children
    .map((c) => `<a href="${c.href}">${c.text}</a>`)
    .join('');
  return `
    <div class="nav-item-group${open}" data-nav-group>
      <a class="nav-link" href="javascript:void(0)" data-nav-toggle>
        <svg viewBox="0 0 24 24">${item.icon}</svg>
        <span>${item.text}</span>
        ${CHEV}
      </a>
      <div class="nav-submenu">${submenu}</div>
    </div>`;
}

function renderSection(section, activeKey) {
  const items = section.items.map((item) => (
    item.children ? renderNavGroup(item, activeKey) : renderNavLink(item, activeKey)
  )).join('');
  return `
    <nav class="nav-section">
      <div class="nav-label">${section.label}</div>
      ${items}
    </nav>`;
}

function renderSidebar(activeKey) {
  const sections = NAV.map((s) => renderSection(s, activeKey)).join('');
  return `
    <aside class="d-sidebar">
      <div class="brand">
        <div class="brand-logo">${BRAND_LOGO}</div>
        <div class="brand-text">
          <div class="brand-name">Adminator</div>
          <div class="brand-tag">v4.1.5 · preview</div>
        </div>
      </div>
      ${sections}
      <div class="sidebar-footer">
        <div class="workspace">
          <div class="workspace-avatar">JD</div>
          <div class="workspace-text">
            <div class="workspace-name">John Doe</div>
            <div class="workspace-role">admin</div>
          </div>
          <svg class="workspace-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="m7 9 5-5 5 5"/><path d="m7 15 5 5 5-5"/>
          </svg>
        </div>
      </div>
    </aside>`;
}

function renderCrumbs(crumbsAttr) {
  if (!crumbsAttr) return '';
  const parts = crumbsAttr.split('|').map((p) => p.trim()).filter(Boolean);
  const sep = '<svg class="sep" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>';
  return parts.map((p, i) => {
    const cls = i === parts.length - 1 ? ' class="current"' : '';
    return `${i > 0 ? sep : ''}<span${cls}>${p}</span>`;
  }).join('');
}

function renderTopbar(crumbsAttr) {
  return `
    <header class="d-topbar">
      <div class="crumbs">
        <button class="hamburger" data-drawer-open aria-label="Open navigation">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        ${renderCrumbs(crumbsAttr)}
      </div>
      <div class="topbar-actions">
        <button class="cmd" data-palette-open>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
          <span>Search...</span>
          <kbd class="kbd">⌘K</kbd>
        </button>

        <div class="dd-wrap">
          <button class="icon-btn" data-dropdown aria-label="Notifications">
            <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="count danger">3</span>
          </button>
          <div class="dd-menu" role="menu">
            <div class="dd-head">
              <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
              Notifications
            </div>
            <div class="dd-list">
              <a class="dd-item" href="#">
                <div class="dd-avatar a1">JD</div>
                <div class="dd-body">
                  <div class="dd-text"><strong>John Doe</strong> liked your <em>post</em></div>
                  <div class="dd-time">5 MIN AGO</div>
                </div>
              </a>
              <a class="dd-item" href="#">
                <div class="dd-avatar a2">MD</div>
                <div class="dd-body">
                  <div class="dd-text"><strong>Moo Doe</strong> liked your <em>cover image</em></div>
                  <div class="dd-time">7 MIN AGO</div>
                </div>
              </a>
              <a class="dd-item" href="#">
                <div class="dd-avatar a3">LD</div>
                <div class="dd-body">
                  <div class="dd-text"><strong>Lee Doe</strong> commented on your <em>video</em></div>
                  <div class="dd-time">10 MIN AGO</div>
                </div>
              </a>
            </div>
            <a class="dd-footer" href="#">View all notifications →</a>
          </div>
        </div>

        <div class="dd-wrap">
          <button class="icon-btn" data-dropdown aria-label="Messages">
            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            <span class="count info">3</span>
          </button>
          <div class="dd-menu" role="menu">
            <div class="dd-head">
              <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
              Messages
            </div>
            <div class="dd-list">
              <a class="dd-item" href="#">
                <div class="dd-avatar a1">JD</div>
                <div class="dd-body">
                  <div class="dd-row-head"><strong>John Doe</strong><span class="dd-time">5 MIN</span></div>
                  <div class="dd-preview">Want to create your own customized data generator for your app…</div>
                </div>
              </a>
              <a class="dd-item" href="#">
                <div class="dd-avatar a2">MD</div>
                <div class="dd-body">
                  <div class="dd-row-head"><strong>Moo Doe</strong><span class="dd-time">15 MIN</span></div>
                  <div class="dd-preview">Want to create your own customized data generator for your app…</div>
                </div>
              </a>
              <a class="dd-item" href="#">
                <div class="dd-avatar a3">LD</div>
                <div class="dd-body">
                  <div class="dd-row-head"><strong>Lee Doe</strong><span class="dd-time">25 MIN</span></div>
                  <div class="dd-preview">Want to create your own customized data generator for your app…</div>
                </div>
              </a>
            </div>
            <a class="dd-footer" href="#">View all messages →</a>
          </div>
        </div>

        <button class="icon-btn" id="themeToggle" aria-label="Toggle theme"></button>

        <div class="dd-wrap">
          <div class="avatar" data-dropdown tabindex="0" role="button" aria-label="Account menu">JD</div>
          <div class="dd-menu dd-profile" role="menu">
            <div class="dd-profile-head">
              <div class="dd-profile-name">John Doe</div>
              <div class="dd-profile-email">john@adminator.app</div>
            </div>
            <a class="dd-menu-item" href="#">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
              Settings
            </a>
            <a class="dd-menu-item" href="#">
              <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Profile
            </a>
            <a class="dd-menu-item" href="email.html">
              <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
              Messages
            </a>
            <div class="dd-divider"></div>
            <a class="dd-menu-item danger" href="#">
              <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
              Logout
            </a>
          </div>
        </div>
      </div>
    </header>`;
}

function renderFooter() {
  return `
    <footer class="d-footer">
      <div>© 2026 · Designed by <a href="https://colorlib.com" target="_blank" rel="nofollow noopener noreferrer">Colorlib</a></div>
      <div class="d-footer-meta">
        <span>v4.1.5</span>
        <span>preview build</span>
      </div>
    </footer>`;
}
function mountShell() {
  const body = document.body;
  const activeKey = body.getAttribute('data-active') || '';
  const crumbs = body.getAttribute('data-crumbs') || '';

  const sidebarHost = document.querySelector('[data-shell-sidebar]');
  const topbarHost  = document.querySelector('[data-shell-topbar]');
  const footerHost  = document.querySelector('[data-shell-footer]');

  if (sidebarHost) sidebarHost.outerHTML = renderSidebar(activeKey);
  if (topbarHost)  topbarHost.outerHTML  = renderTopbar(crumbs);
  if (footerHost)  footerHost.outerHTML  = renderFooter();
}

/**
 * 2026 Shell behaviors:
 *  - theme toggle (icon swap + persistence)
 *  - dropdown open/close
 *  - sidebar nav-group expand/collapse
 *  - hero date population
 *  - todo checkbox state
 *
 * The early-paint <script> in each page body sets the initial data-theme
 * attribute to avoid a flash; this module only handles runtime toggles.
 */

const STORE_KEY = 'dash26-theme';

const SUN_ICON  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>';
const MOON_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>';

function initThemeToggle() {
  const root = document.documentElement;
  const toggle = document.getElementById('themeToggle');
  if (!toggle) return;

  const update = () => {
    toggle.innerHTML = root.getAttribute('data-theme') === 'dark' ? SUN_ICON : MOON_ICON;
  };
  update();

  toggle.addEventListener('click', () => {
    const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    try { localStorage.setItem(STORE_KEY, next); } catch { /* localStorage may be unavailable */ }
    update();
    window.dispatchEvent(new CustomEvent('dash-theme-change', { detail: { theme: next } }));
  });
}

function initHeroDate() {
  const el = document.getElementById('heroDate');
  if (!el) return;
  const fmt = new Intl.DateTimeFormat('en-US', {
    weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
  }).format(new Date());
  const parts = fmt.replace(/,/g, '').split(' ');
  el.textContent = `${parts[0]} · ${parts[1]} ${parts[2]} · ${parts[3]}`;
}

function initNavGroups() {
  // Rail mode (sidebar collapsed to 72px) renders the submenu as a flyout,
  // so only one should be open at a time and outside-clicks should dismiss it.
  // The flyout is position:fixed (see _responsive.scss) — we position it here
  // via getBoundingClientRect so it survives a scrolling sidebar and clamps to
  // the viewport bottom on short screens (e.g. 750×590).
  const railMQ = window.matchMedia('(min-width: 721px) and (max-width: 1100px)');

  const positionFlyout = (group) => {
    const trigger = group.querySelector('[data-nav-toggle]');
    const submenu = group.querySelector('.nav-submenu');
    if (!trigger || !submenu) return;
    const r = trigger.getBoundingClientRect();
    const h = submenu.offsetHeight;            // natural content height (max-height: 400 clamp)
    let top = Math.round(r.top);
    const maxTop = window.innerHeight - h - 8;
    if (top > maxTop) top = Math.max(8, maxTop);
    submenu.style.left = `${Math.round(r.right + 10)}px`;
    submenu.style.top = `${top}px`;
  };

  document.querySelectorAll('[data-nav-toggle]').forEach((a) => {
    a.addEventListener('click', (e) => {
      const group = a.closest('[data-nav-group]');
      if (!group) return;
      e.stopPropagation();
      const willOpen = !group.classList.contains('is-open');
      if (railMQ.matches) {
        document.querySelectorAll('[data-nav-group].is-open').forEach((g) => {
          if (g !== group) g.classList.remove('is-open');
        });
      }
      group.classList.toggle('is-open', willOpen);
      if (willOpen && railMQ.matches) positionFlyout(group);
    });
  });

  document.addEventListener('click', (e) => {
    if (!railMQ.matches) return;
    if (e.target.closest('[data-nav-group]')) return;
    document.querySelectorAll('[data-nav-group].is-open').forEach((g) => g.classList.remove('is-open'));
  });

  // Keep the open flyout pinned to its trigger when the sidebar scrolls or the viewport resizes.
  const reposition = () => {
    if (!railMQ.matches) return;
    document.querySelectorAll('[data-nav-group].is-open').forEach(positionFlyout);
  };
  const sidebar = document.querySelector('.d-sidebar');
  if (sidebar) sidebar.addEventListener('scroll', reposition, { passive: true });
  window.addEventListener('resize', reposition);
}

function initDropdowns() {
  const closeAll = (except) => {
    document.querySelectorAll('.dd-wrap.is-open').forEach((w) => {
      if (w !== except) w.classList.remove('is-open');
    });
  };

  document.querySelectorAll('[data-dropdown]').forEach((trigger) => {
    const wrap = trigger.closest('.dd-wrap');
    if (!wrap) return;
    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      const willOpen = !wrap.classList.contains('is-open');
      closeAll(wrap);
      wrap.classList.toggle('is-open', willOpen);
    });
  });

  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dd-wrap')) closeAll();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAll();
  });
}

function initTodos() {
  document.querySelectorAll('.todo-check').forEach((cb) => {
    cb.addEventListener('change', () => {
      const item = cb.closest('.todo-item');
      if (item) item.classList.toggle('is-done', cb.checked);
    });
  });
}

function initAccordions() {
  document.querySelectorAll('[data-accordion-trigger]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const item = trigger.closest('[data-accordion]');
      if (item) item.classList.toggle('is-open');
    });
  });
}

function initTabGroups() {
  // Tabs that opt in by sharing a [data-tab-group] container.
  // Each .tab inside gets click → toggles is-active on siblings and
  // matches a sibling .tab-panel by data-tab-target.
  document.querySelectorAll('[data-tab-group]').forEach((group) => {
    const tabs = group.querySelectorAll('.tab');
    const panels = group.querySelectorAll('.tab-panel');
    tabs.forEach((tab) => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        const target = tab.getAttribute('data-tab-target');
        tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
        panels.forEach((p) => p.classList.toggle('is-active', p.getAttribute('data-tab-id') === target));
      });
    });
  });
}

function initMobileDrawer() {
  // Mobile: hamburger button (rendered in topbar at ≤720px) opens an off-canvas
  // sidebar drawer. Closes on backdrop click, Esc, or selecting a nav link.
  const body = document.body;
  if (!body) return;

  // Inject a backdrop element once (used by CSS via body.has-drawer-open).
  if (!document.querySelector('.drawer-backdrop')) {
    const backdrop = document.createElement('div');
    backdrop.className = 'drawer-backdrop';
    backdrop.setAttribute('aria-hidden', 'true');
    body.appendChild(backdrop);
    backdrop.addEventListener('click', closeDrawer);
  }

  function openDrawer() {
    body.classList.add('has-drawer-open');
  }
  function closeDrawer() {
    body.classList.remove('has-drawer-open');
  }

  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-drawer-open]');
    if (trigger) { e.preventDefault(); openDrawer(); return; }
    // Auto-close when a sidebar nav link is selected (so the next page opens cleanly).
    const linkInDrawer = e.target.closest('.d-sidebar a[href]:not([href^="#"]):not([href="javascript:void(0)"])');
    if (body.classList.contains('has-drawer-open') && linkInDrawer) {
      closeDrawer();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && body.classList.contains('has-drawer-open')) closeDrawer();
  });
}
function initShellBehaviors() {
  initThemeToggle();
  initHeroDate();
  initNavGroups();
  initDropdowns();
  initTodos();
  initAccordions();
  initTabGroups();
  initMobileDrawer();
}


function initAlertDismissals() {
  document.querySelectorAll('.alert').forEach((alert) => {
    if (alert.dataset.autodismissInitialized === '1') return;
    alert.dataset.autodismissInitialized = '1';

    const close = alert.querySelector('.close');

    const fadeOut = () => {
      if (!alert.isConnected) return;
      alert.style.transition = 'opacity 220ms ease, transform 220ms ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-6px)';

      window.setTimeout(() => {
        if (alert.isConnected) {
          alert.remove();
        }
      }, 220);
    };

    const timeoutId = window.setTimeout(fadeOut, 4500);

    if (close) {
      close.addEventListener('click', () => {
        window.clearTimeout(timeoutId);
        fadeOut();
      });
    }
  });
}

function initLiteTabs()
 {
  document.querySelectorAll('.tabs').forEach((tabs) => {
    const items = Array.from(tabs.querySelectorAll('.tab'));
    if (!items.length) return;
    items.forEach((tab) => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        items.forEach((t) => t.classList.toggle('is-active', t === tab));
      });
    });
  });
}

function initModalDemo() {
  document.querySelectorAll('.modal-demo .mail-tool').forEach((btn) => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.modal-demo');
      if (!card) return;
      card.classList.toggle('is-collapsed');
    });
  });
}


function initPersistentSelectionTables() {
  document.querySelectorAll('[data-selection-key]').forEach((card) => {
    const key = card.getAttribute('data-selection-key');
    if (!key) return;

    const storageKey = `dash26-selection:${key}`;
    const table = card.querySelector('[data-selection-table]');
    const master = card.querySelector('[data-master-checkbox]');
    const selectedCount = card.querySelector('[data-selected-count]');
    const bulkTrigger = card.querySelector('[data-bulk-delete-trigger]');
    const bulkForm = card.querySelector('[data-bulk-delete-form]');
    const bulkInputs = card.querySelector('[data-bulk-delete-inputs]');

    if (!table) return;

    const readSelection = () => {
      try {
        const raw = localStorage.getItem(storageKey);
        const parsed = raw ? JSON.parse(raw) : [];
        return new Set(Array.isArray(parsed) ? parsed.map(String) : []);
      } catch (error) {
        return new Set();
      }
    };

    const writeSelection = (selection) => {
      try {
        localStorage.setItem(storageKey, JSON.stringify([...selection]));
      } catch (error) {
        // ignore storage errors
      }
    };

    let selection = readSelection();

    const rowCheckboxes = () => Array.from(table.querySelectorAll('tbody [data-row-checkbox]'));

    const syncRows = () => {
      const boxes = rowCheckboxes();
      let visibleSelected = 0;

      boxes.forEach((box) => {
        const rowId = String(box.getAttribute('data-row-id') || '');
        const checked = selection.has(rowId);
        box.checked = checked;

        const row = box.closest('tr');
        if (row) row.classList.toggle('is-selected', checked);

        if (checked) visibleSelected += 1;
      });

      if (master) {
        master.checked = boxes.length > 0 && visibleSelected === boxes.length;
        master.indeterminate = visibleSelected > 0 && visibleSelected < boxes.length;
      }

      if (selectedCount) {
        selectedCount.textContent = `${selection.size} seçili`;
      }

      if (bulkTrigger) {
        bulkTrigger.disabled = selection.size === 0;
      }
    };

    const persistAndSync = () => {
      writeSelection(selection);
      syncRows();
    };

    rowCheckboxes().forEach((box) => {
      box.addEventListener('change', () => {
        const rowId = String(box.getAttribute('data-row-id') || '');
        if (!rowId) return;

        if (box.checked) {
          selection.add(rowId);
        } else {
          selection.delete(rowId);
        }

        persistAndSync();
      });
    });

    if (master) {
      master.addEventListener('change', () => {
        rowCheckboxes().forEach((box) => {
          const rowId = String(box.getAttribute('data-row-id') || '');
          if (!rowId) return;

          box.checked = master.checked;
          if (master.checked) {
            selection.add(rowId);
          } else {
            selection.delete(rowId);
          }

          const row = box.closest('tr');
          if (row) row.classList.toggle('is-selected', master.checked);
        });

        persistAndSync();
      });
    }

    if (bulkTrigger && bulkForm && bulkInputs) {
      bulkTrigger.addEventListener('click', () => {
        if (selection.size === 0) return;

        const confirmed = window.confirm('Seçili kategoriler silinsin mi?');
        if (!confirmed) return;

        bulkInputs.innerHTML = '';
        selection.forEach((id) => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ids[]';
          input.value = id;
          bulkInputs.appendChild(input);
        });

        try {
          localStorage.removeItem(storageKey);
        } catch (error) {
          // ignore storage errors
        }

        bulkForm.submit();
      });
    }

    syncRows();
  });
}

function initExtraInteractions() {

  initAlertDismissals();
  initLiteTabs();
  initModalDemo();
  initDataTables();
  initCharts();
}


function cssVar(name) {
  return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

function parseColor(value) {
  const tmp = document.createElement('div');
  tmp.style.color = value;
  document.body.appendChild(tmp);
  const rgb = getComputedStyle(tmp).color;
  tmp.remove();
  return rgb;
}

function canvasRatio(canvas) {
  const rect = canvas.getBoundingClientRect();
  const dpr = window.devicePixelRatio || 1;
  const width = Math.max(1, Math.floor(rect.width * dpr));
  const height = Math.max(1, Math.floor(rect.height * dpr));
  if (canvas.width !== width || canvas.height !== height) {
    canvas.width = width;
    canvas.height = height;
  }
  const ctx = canvas.getContext('2d');
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  return { ctx, width: rect.width, height: rect.height, dpr };
}

function clearCanvas(ctx, w, h) {
  ctx.clearRect(0, 0, w, h);
  ctx.fillStyle = cssVar('--bg-card') || '#fff';
  ctx.fillRect(0, 0, w, h);
}

function drawGrid(ctx, w, h, padding, rows, cols) {
  ctx.save();
  ctx.strokeStyle = cssVar('--border-soft') || 'rgba(148,163,184,.18)';
  ctx.lineWidth = 1;
  ctx.beginPath();
  for (let i = 0; i <= rows; i++) {
    const y = padding.top + ((h - padding.top - padding.bottom) / rows) * i;
    ctx.moveTo(padding.left, y);
    ctx.lineTo(w - padding.right, y);
  }
  for (let i = 0; i <= cols; i++) {
    const x = padding.left + ((w - padding.left - padding.right) / cols) * i;
    ctx.moveTo(x, padding.top);
    ctx.lineTo(x, h - padding.bottom);
  }
  ctx.stroke();
  ctx.restore();
}

function drawLineChart(canvas, data, opts = {}) {
  const { ctx, width: w, height: h } = canvasRatio(canvas);
  clearCanvas(ctx, w, h);
  const padding = opts.padding || { top: 18, right: 18, bottom: 28, left: 28 };
  const min = opts.min ?? Math.min(...data) * 0.9;
  const max = opts.max ?? Math.max(...data) * 1.08;
  const xStep = (w - padding.left - padding.right) / Math.max(1, data.length - 1);
  const y = (v) => padding.top + (h - padding.top - padding.bottom) * (1 - (v - min) / (max - min));

  drawGrid(ctx, w, h, padding, opts.gridRows ?? 4, Math.min(8, data.length - 1));

  ctx.save();
  ctx.lineCap = 'round';
  ctx.lineJoin = 'round';
  ctx.strokeStyle = opts.stroke || cssVar('--primary');
  ctx.lineWidth = 2.5;

  const grad = ctx.createLinearGradient(0, padding.top, 0, h - padding.bottom);
  grad.addColorStop(0, (opts.fillTop || cssVar('--primary-soft') || 'rgba(37,99,235,.14)'));
  grad.addColorStop(1, 'rgba(0,0,0,0)');

  ctx.beginPath();
  data.forEach((v, i) => {
    const x = padding.left + xStep * i;
    const yy = y(v);
    if (i === 0) ctx.moveTo(x, yy);
    else ctx.lineTo(x, yy);
  });

  const lastX = padding.left + xStep * (data.length - 1);
  ctx.lineTo(lastX, h - padding.bottom);
  ctx.lineTo(padding.left, h - padding.bottom);
  ctx.closePath();
  ctx.fillStyle = grad;
  ctx.fill();
  ctx.restore();

  ctx.save();
  ctx.beginPath();
  data.forEach((v, i) => {
    const x = padding.left + xStep * i;
    const yy = y(v);
    if (i === 0) ctx.moveTo(x, yy);
    else ctx.lineTo(x, yy);
  });
  ctx.stroke();
  ctx.restore();

  ctx.save();
  ctx.fillStyle = opts.pointFill || cssVar('--bg-card');
  ctx.strokeStyle = opts.stroke || cssVar('--primary');
  data.forEach((v, i) => {
    const x = padding.left + xStep * i;
    const yy = y(v);
    ctx.beginPath();
    ctx.arc(x, yy, 3.4, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
  });
  ctx.restore();
}

function drawBarChart(canvas, data, labels, opts = {}) {
  const { ctx, width: w, height: h } = canvasRatio(canvas);
  clearCanvas(ctx, w, h);
  const padding = opts.padding || { top: 20, right: 18, bottom: 36, left: 32 };
  const max = opts.max ?? Math.max(...data) * 1.15;
  const plotH = h - padding.top - padding.bottom;
  const plotW = w - padding.left - padding.right;
  const barGap = opts.barGap ?? 18;
  const barW = Math.max(16, (plotW - barGap * (data.length - 1)) / data.length);

  drawGrid(ctx, w, h, padding, opts.gridRows ?? 4, Math.min(8, data.length));

  ctx.save();
  ctx.fillStyle = opts.barColor || cssVar('--primary');
  data.forEach((v, i) => {
    const barH = Math.max(2, plotH * (v / max));
    const x = padding.left + i * (barW + barGap);
    const y = padding.top + plotH - barH;
    const radius = 6;
    roundRect(ctx, x, y, barW, barH, radius);
    ctx.fill();
  });
  ctx.restore();

  if (labels?.length) {
    ctx.save();
    ctx.fillStyle = cssVar('--t-muted') || '#64748b';
    ctx.font = '10px JetBrains Mono, monospace';
    ctx.textAlign = 'center';
    labels.forEach((label, i) => {
      const x = padding.left + i * (barW + barGap) + barW / 2;
      ctx.fillText(label, x, h - 14);
    });
    ctx.restore();
  }
}

function roundRect(ctx, x, y, w, h, r) {
  const radius = Math.min(r, w / 2, h / 2);
  ctx.beginPath();
  ctx.moveTo(x + radius, y);
  ctx.arcTo(x + w, y, x + w, y + h, radius);
  ctx.arcTo(x + w, y + h, x, y + h, radius);
  ctx.arcTo(x, y + h, x, y, radius);
  ctx.arcTo(x, y, x + w, y, radius);
  ctx.closePath();
}

function drawDoughnutChart(canvas, values, labels, opts = {}) {
  const { ctx, width: w, height: h } = canvasRatio(canvas);
  clearCanvas(ctx, w, h);
  const cx = w / 2;
  const cy = h / 2 - 8;
  const outer = Math.min(w, h) * 0.34;
  const inner = outer * 0.66;
  const total = values.reduce((a, b) => a + b, 0) || 1;
  let start = -Math.PI / 2;
  const colors = opts.colors || [
    cssVar('--primary'),
    cssVar('--success'),
    cssVar('--warning'),
    cssVar('--danger'),
  ];

  ctx.save();
  ctx.lineWidth = outer - inner;
  ctx.lineCap = 'round';
  values.forEach((v, i) => {
    const end = start + (v / total) * Math.PI * 2;
    ctx.beginPath();
    ctx.strokeStyle = colors[i % colors.length];
    ctx.arc(cx, cy, (outer + inner) / 2, start, end);
    ctx.stroke();
    start = end + 0.02;
  });
  ctx.restore();

  ctx.save();
  ctx.textAlign = 'center';
  ctx.fillStyle = cssVar('--t-base');
  ctx.font = '700 28px Inter, sans-serif';
  ctx.fillText(`${Math.round(total)}K`, cx, cy + 8);
  ctx.fillStyle = cssVar('--t-muted');
  ctx.font = '11px Inter, sans-serif';
  ctx.fillText(opts.caption || 'Sessions', cx, cy + 28);
  ctx.restore();

  if (labels?.length) {
    ctx.save();
    ctx.font = '11px Inter, sans-serif';
    ctx.fillStyle = cssVar('--t-muted');
    let y = h - 38;
    labels.forEach((label, i) => {
      const x = 18 + (i % 2) * (w / 2 - 24);
      if (i % 2 === 0 && i !== 0) y += 18;
      ctx.fillStyle = colors[i % colors.length];
      ctx.fillRect(x, y - 8, 8, 8);
      ctx.fillStyle = cssVar('--t-muted');
      ctx.fillText(label, x + 14, y);
    });
    ctx.restore();
  }
}

function drawRadarChart(canvas, series, labels, opts = {}) {
  const { ctx, width: w, height: h } = canvasRatio(canvas);
  clearCanvas(ctx, w, h);
  const cx = w / 2;
  const cy = h / 2;
  const radius = Math.min(w, h) * 0.34;
  const rings = opts.rings ?? 4;
  const totalPoints = labels.length;

  ctx.save();
  ctx.strokeStyle = cssVar('--border-soft');
  for (let r = 1; r <= rings; r++) {
    const rr = (radius / rings) * r;
    ctx.beginPath();
    for (let i = 0; i < totalPoints; i++) {
      const angle = -Math.PI / 2 + (i / totalPoints) * Math.PI * 2;
      const x = cx + Math.cos(angle) * rr;
      const y = cy + Math.sin(angle) * rr;
      if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    }
    ctx.closePath();
    ctx.stroke();
  }
  ctx.restore();

  // spokes + labels
  ctx.save();
  ctx.strokeStyle = cssVar('--border-soft');
  ctx.fillStyle = cssVar('--t-muted');
  ctx.font = '10px JetBrains Mono, monospace';
  ctx.textAlign = 'center';
  for (let i = 0; i < totalPoints; i++) {
    const angle = -Math.PI / 2 + (i / totalPoints) * Math.PI * 2;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.lineTo(cx + Math.cos(angle) * radius, cy + Math.sin(angle) * radius);
    ctx.stroke();
    const lx = cx + Math.cos(angle) * (radius + 18);
    const ly = cy + Math.sin(angle) * (radius + 18);
    ctx.fillText(labels[i], lx, ly + 3);
  }
  ctx.restore();

  const colors = opts.colors || [cssVar('--primary'), cssVar('--success')];
  series.forEach((values, sIdx) => {
    ctx.save();
    ctx.beginPath();
    values.forEach((v, i) => {
      const rr = (v / 100) * radius;
      const angle = -Math.PI / 2 + (i / totalPoints) * Math.PI * 2;
      const x = cx + Math.cos(angle) * rr;
      const y = cy + Math.sin(angle) * rr;
      if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    });
    ctx.closePath();
    ctx.fillStyle = colors[sIdx % colors.length] + '22';
    ctx.strokeStyle = colors[sIdx % colors.length];
    ctx.lineWidth = 2;
    ctx.fill();
    ctx.stroke();
    ctx.restore();
  });
}

function drawStackedBarChart(canvas, stacks, labels, opts = {}) {
  const { ctx, width: w, height: h } = canvasRatio(canvas);
  clearCanvas(ctx, w, h);
  const padding = opts.padding || { top: 20, right: 18, bottom: 34, left: 28 };
  const plotH = h - padding.top - padding.bottom;
  const plotW = w - padding.left - padding.right;
  const max = opts.max ?? Math.max(...stacks.map((arr) => arr.reduce((a, b) => a + b, 0))) * 1.15;
  const gap = opts.gap ?? 16;
  const barW = Math.max(18, (plotW - gap * (stacks.length - 1)) / stacks.length);
  drawGrid(ctx, w, h, padding, opts.gridRows ?? 4, Math.min(8, stacks.length));

  const colors = opts.colors || [cssVar('--primary'), cssVar('--success'), cssVar('--warning')];
  stacks.forEach((parts, i) => {
    let bottom = padding.top + plotH;
    const x = padding.left + i * (barW + gap);
    parts.forEach((part, j) => {
      const partH = Math.max(3, plotH * (part / max));
      const y = bottom - partH;
      ctx.fillStyle = colors[j % colors.length];
      roundRect(ctx, x, y, barW, partH, 6);
      ctx.fill();
      bottom = y - 2;
    });
    ctx.fillStyle = cssVar('--t-muted');
    ctx.font = '10px JetBrains Mono, monospace';
    ctx.textAlign = 'center';
    ctx.fillText(labels[i], x + barW / 2, h - 12);
  });
}

function initCharts() {
  const canvases = Array.from(document.querySelectorAll('canvas[data-chart-key]'));
  if (!canvases.length) return;

  const configs = {
    'dashboard-monthly': () => drawLineChart(
      document.querySelector('canvas[data-chart-key="dashboard-monthly"]'),
      [42, 38, 58, 50, 74, 69, 92, 88, 106, 117, 149, 172],
      { stroke: cssVar('--primary') }
    ),
    'revenue-line': () => drawLineChart(
      document.querySelector('canvas[data-chart-key="revenue-line"]'),
      [28, 44, 38, 52, 49, 66, 60, 79, 88, 101, 118, 132],
      { stroke: cssVar('--primary') }
    ),
    'sessions-area': () => drawLineChart(
      document.querySelector('canvas[data-chart-key="sessions-area"]'),
      [18, 24, 30, 28, 42, 46, 44, 58, 66, 61, 74, 81],
      { stroke: cssVar('--success') }
    ),
    'channels-bar': () => drawBarChart(
      document.querySelector('canvas[data-chart-key="channels-bar"]'),
      [68, 52, 81, 39, 64],
      ['Org', 'Ads', 'SEO', 'Ref', 'Email'],
      { barColor: cssVar('--primary') }
    ),
    'devices-doughnut': () => drawDoughnutChart(
      document.querySelector('canvas[data-chart-key="devices-doughnut"]'),
      [42, 31, 16, 11],
      ['Desktop', 'Mobile', 'Tablet', 'Other'],
      { caption: 'Devices' }
    ),
    'sources-radar': () => drawRadarChart(
      document.querySelector('canvas[data-chart-key="sources-radar"]'),
      [[82, 66, 74, 63, 88, 70], [58, 71, 62, 77, 66, 61]],
      ['Reach', 'CTR', 'Conv', 'AOV', 'LTV', 'Ret.'],
    ),
    'mrr-stacked': () => drawStackedBarChart(
      document.querySelector('canvas[data-chart-key="mrr-stacked"]'),
      [[18, 10, 6], [20, 12, 8], [22, 11, 9], [24, 13, 10], [26, 15, 11], [28, 16, 12]],
      ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    ),
  };

  const renderAll = () => {
    canvases.forEach((canvas) => {
      const key = canvas.getAttribute('data-chart-key');
      const fn = configs[key];
      if (fn) fn();
    });
  };

  const ro = 'ResizeObserver' in window ? new ResizeObserver(() => renderAll()) : null;
  canvases.forEach((canvas) => {
    if (ro) ro.observe(canvas.parentElement || canvas);
  });
  window.addEventListener('resize', renderAll);
  window.addEventListener('dash-theme-change', renderAll);
  renderAll();
}

function getDataTableValue(record, key) {
  return {
    user: record.user.toLowerCase(),
    role: record.role.toLowerCase(),
    department: record.department.toLowerCase(),
    status: record.status.toLowerCase(),
    id: record.id.toLowerCase(),
    last: record.lastSort,
  }[key] ?? '';
}

function buildDataTableRows() {
  const firstNames = ['Sara','Leo','Mira','Anya','Diego','Hannah','Omar','Iris','Tom','Nina','Ethan','Maya','Lucas','Zoe','Arda','Elif','Can','Aylin','Kerem','Bora'];
  const lastNames = ['Kim','Reyes','Doe','Trent','Pinto','Bell','Saleh','Larsen','Park','Yilmaz','Carter','Stone','Brown','Adams','Demir','Aydin','Kaya','Sahin','Korkmaz','Ozturk'];
  const roleSeq = ['Admin', 'Editor', 'Editor', 'Viewer', 'Admin', 'Editor', 'Viewer', 'Editor'];
  const deptSeq = ['Engineering', 'Design', 'Marketing', 'Sales', 'Product', 'Support'];
  const statusSeq = ['Active', 'Active', 'Pending', 'Inactive', 'Active', 'Active', 'Active', 'Pending'];
  const avatars = ['ma-1','ma-2','ma-3','ma-4','ma-5','ma-6'];
  const rows = [];
  for (let i = 0; i < 142; i++) {
    const fn = firstNames[i % firstNames.length];
    const ln = lastNames[(i * 3) % lastNames.length];
    const user = `${fn} ${ln}`;
    const emailLocal = `${fn}.${ln}`.toLowerCase().replace(/[^a-z0-9.]/g, '');
    const domain = i % 3 === 0 ? 'northwind.com' : 'adminator.app';
    const email = `${emailLocal}@${domain}`;
    const role = roleSeq[i % roleSeq.length];
    const department = deptSeq[(i + 1) % deptSeq.length];
    const status = statusSeq[i % statusSeq.length];
    const id = `USR-${String(1042 - i).padStart(4, '0')}`;
    const day = ((i * 2) % 28) + 1;
    const hour = String(8 + (i % 10)).padStart(2, '0');
    const minute = String((i * 7) % 60).padStart(2, '0');
    const last = `Apr ${day} · ${hour}:${minute}`;
    const lastSort = (day * 10000) + (8 + (i % 10)) * 100 + ((i * 7) % 60);
    rows.push({
      id,
      user,
      email,
      role,
      department,
      status,
      last,
      lastSort,
      avatar: avatars[i % avatars.length],
      initials: `${fn[0]}${ln[0]}`.toUpperCase(),
      selected: i === 0,
    });
  }
  return rows;
}

function initDataTables() {
  document.querySelectorAll('[data-demo-datatable]').forEach((table) => {
    const host = table.closest('.card') || table.parentElement;
    const body = table.querySelector('tbody');
    if (!body) return;

    const toolbar = table.closest('.card')?.querySelector('.data-toolbar');
    const toolbarInputs = toolbar ? toolbar.querySelectorAll('input, select, button') : [];
    const searchInput = toolbar ? toolbar.querySelector('input[type="search"]') : null;
    const toolbarSelects = toolbar ? Array.from(toolbar.querySelectorAll('.data-toolbar-right .select')) : [];
    const roleSelect = toolbarSelects[0] || null;
    const statusSelect = toolbarSelects[1] || null;
    const pageSizeSelect = table.closest('.card')?.querySelector('.data-foot .select') || null;
    const pager = table.closest('.card')?.querySelector('.pager') || null;
    const footerInfo = table.closest('.card')?.querySelector('.data-foot-info span') || null;
    const masterCheckbox = table.querySelector('thead input[type="checkbox"]');

    const data = buildDataTableRows();
    let state = {
      search: '',
      role: 'All roles',
      status: 'All status',
      page: 1,
      pageSize: pageSizeSelect ? parseInt(pageSizeSelect.value, 10) || 15 : 15,
      sortKey: 'user',
      sortDir: 'asc',
    };

    const roleBadgeClass = (role) => ({ Admin: 'primary', Editor: 'success', Viewer: 'info' }[role] || 'primary');
    const statusBadgeClass = (status) => ({ Active: 'success', Pending: 'warning', Inactive: 'danger' }[status] || 'success');

    function renderRow(record, index) {
      return `
        <tr class="data-row${record.selected ? ' is-selected' : ''}" data-row-id="${record.id}">
          <td><label class="check"><input type="checkbox"${record.selected ? ' checked' : ''}><span class="box"></span></label></td>
          <td>
            <div class="data-cell-user">
              <div class="av ${record.avatar}">${record.initials}</div>
              <div class="data-cell-user-meta">
                <div class="data-cell-user-name">${record.user}</div>
                <div class="data-cell-user-email">${record.email}</div>
              </div>
            </div>
          </td>
          <td><span class="badge ${roleBadgeClass(record.role)}">${record.role}</span></td>
          <td>${record.department}</td>
          <td><span class="badge ${statusBadgeClass(record.status)} dot">${record.status}</span></td>
          <td><span class="data-cell-mono">${record.id}</span></td>
          <td><span class="data-cell-mono">${record.last}</span></td>
          <td>
            <div class="data-cell-actions">
              <button class="btn--icon" aria-label="View"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
              <button class="btn--icon" aria-label="Edit"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg></button>
              <button class="btn--icon" aria-label="More"><svg viewBox="0 0 24 24"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg></button>
            </div>
          </td>
        </tr>`;
    }

    function filteredData() {
      const q = state.search.trim().toLowerCase();
      return data.filter((record) => {
        const matchesSearch = !q || `${record.user} ${record.email} ${record.id} ${record.department} ${record.role} ${record.status}`.toLowerCase().includes(q);
        const matchesRole = state.role === 'All roles' || record.role === state.role;
        const matchesStatus = state.status === 'All status' || record.status === state.status;
        return matchesSearch && matchesRole && matchesStatus;
      }).sort((a, b) => {
        const av = getDataTableValue(a, state.sortKey);
        const bv = getDataTableValue(b, state.sortKey);
        if (av === bv) return 0;
        if (av > bv) return state.sortDir === 'asc' ? 1 : -1;
        return state.sortDir === 'asc' ? -1 : 1;
      });
    }

    function updateHeaderSort() {
      const ths = table.querySelectorAll('thead th');
      ths.forEach((th) => th.classList.remove('sorted-asc', 'sorted-desc'));
      const map = { user: 1, role: 2, department: 3, status: 4, id: 5, last: 6 };
      const activeTh = ths[map[state.sortKey]];
      if (activeTh) activeTh.classList.add(state.sortDir === 'asc' ? 'sorted-asc' : 'sorted-desc');
    }

    function updateFooter(total, start, end, pageCount) {
      if (footerInfo) {
        footerInfo.innerHTML = `Showing <strong style="color: var(--t-base);">${total ? start : 0}–${end}</strong> of <strong style="color: var(--t-base);">${total}</strong>`;
      }
      if (pager) {
        const pageButtons = [];
        const pushBtn = (label, page, aria) => {
          const disabled = page < 1 || page > pageCount;
          const active = page === state.page;
          pageButtons.push(`<button class="pager-btn${active ? ' is-active' : ''}"${disabled ? ' disabled' : ''}${aria ? ` aria-label="${aria}"` : ''} data-page="${page}">${label}</button>`);
        };
        pushBtn('<svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>', state.page - 1, 'Previous');
        const pages = [];
        if (pageCount <= 7) {
          for (let i = 1; i <= pageCount; i++) pages.push(i);
        } else {
          pages.push(1);
          if (state.page > 4) pages.push('…');
          const startPage = Math.max(2, state.page - 1);
          const endPage = Math.min(pageCount - 1, state.page + 1);
          for (let i = startPage; i <= endPage; i++) pages.push(i);
          if (state.page < pageCount - 3) pages.push('…');
          pages.push(pageCount);
        }
        pages.forEach((p) => {
          if (p === '…') {
            pageButtons.push('<button class="pager-btn" disabled>…</button>');
          } else {
            pushBtn(String(p), p);
          }
        });
        pushBtn('<svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>', state.page + 1, 'Next');
        pager.innerHTML = pageButtons.join('');
        pager.querySelectorAll('[data-page]').forEach((btn) => {
          btn.addEventListener('click', () => {
            const page = parseInt(btn.getAttribute('data-page'), 10);
            if (!Number.isNaN(page)) {
              state.page = page;
              render();
            }
          });
        });
      }
    }

    function syncSelectAll(visibleRows) {
      if (!masterCheckbox) return;
      if (!visibleRows.length) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
        return;
      }
      const selected = visibleRows.filter((r) => r.selected).length;
      masterCheckbox.checked = selected === visibleRows.length;
      masterCheckbox.indeterminate = selected > 0 && selected < visibleRows.length;
    }

    function bindRowEvents(rows) {
      rows.forEach((tr) => {
        const id = tr.getAttribute('data-row-id');
        const record = data.find((d) => d.id === id);
        const checkbox = tr.querySelector('input[type="checkbox"]');
        if (!record || !checkbox) return;
        checkbox.addEventListener('change', () => {
          record.selected = checkbox.checked;
          tr.classList.toggle('is-selected', record.selected);
          syncSelectAll(rows.map((row) => data.find((d) => d.id === row.getAttribute('data-row-id'))).filter(Boolean));
        });
      });
    }

    function render() {
      const filtered = filteredData();
      const pageCount = Math.max(1, Math.ceil(filtered.length / state.pageSize));
      if (state.page > pageCount) state.page = pageCount;
      if (state.page < 1) state.page = 1;
      const start = (state.page - 1) * state.pageSize;
      const pageRows = filtered.slice(start, start + state.pageSize);
      body.innerHTML = pageRows.map(renderRow).join('');
      updateHeaderSort();
      const rows = Array.from(body.querySelectorAll('tr'));
      bindRowEvents(rows);
      syncSelectAll(pageRows);
      const end = Math.min(filtered.length, start + pageRows.length);
      updateFooter(filtered.length, start + 1, end, pageCount);
    }

    // column sort interactions
    const sortableMap = { 1: 'user', 2: 'role', 3: 'department', 4: 'status', 5: 'id', 6: 'last' };
    table.querySelectorAll('thead th').forEach((th, idx) => {
      if (!sortableMap[idx]) return;
      th.style.cursor = 'pointer';
      th.addEventListener('click', () => {
        const key = sortableMap[idx];
        if (state.sortKey === key) state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
        else {
          state.sortKey = key;
          state.sortDir = 'asc';
        }
        render();
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', () => {
        state.search = searchInput.value;
        state.page = 1;
        render();
      });
    }
    if (roleSelect) {
      roleSelect.addEventListener('change', () => {
        state.role = roleSelect.value;
        state.page = 1;
        render();
      });
    }
    if (statusSelect) {
      statusSelect.addEventListener('change', () => {
        state.status = statusSelect.value;
        state.page = 1;
        render();
      });
    }
    if (pageSizeSelect) {
      pageSizeSelect.addEventListener('change', () => {
        state.pageSize = parseInt(pageSizeSelect.value, 10) || 15;
        state.page = 1;
        render();
      });
    }
    if (masterCheckbox) {
      masterCheckbox.addEventListener('change', () => {
        const checked = masterCheckbox.checked;
        const filtered = filteredData();
        const start = (state.page - 1) * state.pageSize;
        const pageRows = filtered.slice(start, start + state.pageSize);
        pageRows.forEach((record) => { record.selected = checked; });
        render();
      });
    }

    // initial render
    render();
  });
}

function startApp() {
  mountShell();
  initShellBehaviors();
  initExtraInteractions();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startApp);
} else {
  startApp();
}
