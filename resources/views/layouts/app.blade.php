<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'WebGIS Kebencanaan') }}</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-w: 240px;
            --topbar-h: 56px;
            --brand-900: #0a1628;
            --brand-800: #112240;
            --brand-600: #1d3a6e;
            --accent:    #e84c1e;
            --accent-lt: #ff6b3d;
            --text-muted: #8892a4;
        }

        /* ── Layout shell ───────────────────────── */
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            color: #1a2332;
        }

        /* ── Sidebar ────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--brand-900);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
        }

        .sidebar-brand {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }
        .sidebar-brand .brand-icon {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff;
        }
        .sidebar-brand span {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: .9rem;
            color: #fff;
            line-height: 1.15;
        }
        .sidebar-brand small {
            display: block;
            font-size: .65rem;
            font-weight: 400;
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }
        .sidebar-nav .nav-label {
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: .75rem 1.25rem .25rem;
        }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .55rem 1.25rem;
            color: #a8b2c1;
            font-size: .875rem;
            font-weight: 500;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: background .15s, color .15s, border-color .15s;
            text-decoration: none;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,.05);
            color: #fff;
        }
        .sidebar-nav .nav-link.active {
            background: rgba(232,76,30,.12);
            color: #fff;
            border-left-color: var(--accent);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .sidebar-footer .user-info {
            display: flex; align-items: center; gap: .6rem;
        }
        .sidebar-footer .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--brand-600);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; color: #fff; font-weight: 600;
        }
        .sidebar-footer .user-name {
            font-size: .8rem; color: #fff; font-weight: 500;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-footer .user-role {
            font-size: .7rem; color: var(--text-muted);
        }

        /* ── Topbar ─────────────────────────────── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }
        #topbar .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--brand-900);
        }
        #topbar .topbar-actions {
            display: flex; align-items: center; gap: .75rem;
        }
        .btn-sidebar-toggle {
            border: none; background: none;
            font-size: 1.2rem; color: #64748b;
            cursor: pointer; padding: .25rem .5rem;
            display: none; /* hidden on desktop */
        }

        /* ── Main content ───────────────────────── */
        #main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            min-height: calc(100vh - var(--topbar-h));
            padding: 1.5rem;
            transition: margin-left .25s ease;
        }

        /* ── Alerts / Flash ─────────────────────── */
        .flash-wrap {
            position: fixed;
            top: calc(var(--topbar-h) + 1rem);
            right: 1rem;
            z-index: 2000;
            width: 320px;
        }

        /* ── Responsive ─────────────────────────── */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(calc(-1 * var(--sidebar-w)));
            }
            #sidebar.open {
                transform: translateX(0);
            }
            #topbar, #main-content {
                left: 0; margin-left: 0;
            }
            .btn-sidebar-toggle { display: block; }

            /* overlay */
            #sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.45);
                z-index: 1035;
            }
            #sidebar-overlay.show { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ --}}
<aside id="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-geo-alt-fill"></i></div>
        <span>
            CENTRA
            <small>Central Java Disaster Analytics</small>
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="{{ route('map.index') }}"
           class="nav-link {{ request()->routeIs('map.*') ? 'active' : '' }}">
            <i class="bi bi-map"></i> Peta Interaktif
        </a>

        {{-- <div class="nav-label mt-2">Data</div> --}}

        {{-- <a href="{{ route('disaster-events.index') }}"
           class="nav-link {{ request()->routeIs('disaster-events.*') ? 'active' : '' }}">
            <i class="bi bi-table"></i> Kejadian Bencana
        </a>

        <a href="{{ route('disaster-types.index') }}"
           class="nav-link {{ request()->routeIs('disaster-types.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Jenis Bencana
        </a> --}}

        {{-- <a href="{{ route('regions.index') }}"
           class="nav-link {{ request()->routeIs('regions.*') ? 'active' : '' }}">
            <i class="bi bi-pin-map"></i> Wilayah
        </a> --}}

        @can('admin')
        <div class="nav-label mt-2">Administrasi</div>

        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Pengguna
        </a>
        @endcan
    </nav>

    {{-- Footer / User --}}
    <div class="sidebar-footer">
        @auth
        <div class="user-info">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm p-0 border-0"
                        title="Logout"
                        style="color: var(--text-muted); background: none;">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
        @endauth
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebar-overlay"></div>

{{-- ══════════════════════════════════════════
     TOPBAR
══════════════════════════════════════════ --}}
<header id="topbar">
    <div class="d-flex align-items-center gap-2">
        <button class="btn-sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <span class="page-title">@yield('page-title', config('app.name', 'WebGIS Kebencanaan'))</span>
    </div>

    <div class="topbar-actions">
        {{-- Breadcrumb opsional --}}
        @hasSection('breadcrumb')
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0 small">
                @yield('breadcrumb')
            </ol>
        </nav>
        @endif

        {{-- Profile shortcut --}}
        @auth
        <a href="{{ route('profile.edit') }}"
           class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-person-circle"></i>
            <span class="d-none d-md-inline">Profil</span>
        </a>
        @endauth
    </div>
</header>

{{-- ══════════════════════════════════════════
     FLASH MESSAGES
══════════════════════════════════════════ --}}
<div class="flash-wrap">
    @foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $type)
        @if (session($key))
        <div class="alert alert-{{ $type }} alert-dismissible shadow-sm mb-2" role="alert">
            <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'x-circle' : 'info-circle') }} me-1"></i>
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    @endforeach
</div>

{{-- ══════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════ --}}
<main id="main-content">
    @yield('content')
</main>

{{-- ══════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle (mobile)
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const toggle   = document.getElementById('sidebarToggle');

    function openSidebar()  { sidebar.classList.add('open');  overlay.classList.add('show'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

    toggle?.addEventListener('click', () =>
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
    );
    overlay.addEventListener('click', closeSidebar);

    // Auto-dismiss flash alerts
    setTimeout(() => {
        document.querySelectorAll('.flash-wrap .alert').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el)?.close();
        });
    }, 5000);
</script>

@stack('scripts')

</body>
</html>
