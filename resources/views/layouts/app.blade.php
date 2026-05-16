<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Aplikasi Stok</title>
    <meta name="description" content="Sistem manajemen stok dan gudang terintegrasi Telegram Bot untuk UMKM">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark-bg: #0f0f1a;
            --dark-sidebar: #13132b;
            --dark-card: #1a1a35;
            --dark-card2: #1e1e3a;
            --dark-border: #2d2d5a;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--dark-bg); color: var(--text-primary); min-height: 100vh; display: flex; overflow-x: hidden; }

        /* SIDEBAR */
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: var(--dark-sidebar); border-right: 1px solid var(--dark-border); display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 50; transition: transform 0.3s ease; }
        .sidebar-logo { padding: 22px 20px; border-bottom: 1px solid var(--dark-border); display: flex; align-items: center; gap: 12px; }
        .sidebar-logo .logo-icon { width: 38px; height: 38px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sidebar-logo .logo-icon svg { width: 20px; height: 20px; color: white; stroke: white; }
        .sidebar-logo .logo-text { font-weight: 700; font-size: 15px; background: linear-gradient(135deg, #e0e7ff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1.2; }
        .sidebar-logo .logo-sub { font-size: 11px; color: var(--text-muted); font-weight: 400; -webkit-text-fill-color: var(--text-muted); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; padding: 8px 8px 6px; margin-top: 8px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: var(--text-secondary); text-decoration: none; font-size: 13.5px; font-weight: 500; transition: all 0.2s ease; margin-bottom: 2px; }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(99,102,241,0.12); color: #c4b5fd; transform: translateX(2px); }
        .nav-item.active { background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(139,92,246,0.15)); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.3); }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--dark-border); }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); }
        .user-avatar { width: 34px; height: 34px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; color: white; flex-shrink: 0; }
        .logout-btn { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 6px; display: flex; align-items: center; border-radius: 6px; transition: all 0.2s; }
        .logout-btn:hover { color: #f87171; background: rgba(239,68,68,0.1); }
        .logout-btn svg { width: 15px; height: 15px; }

        /* MAIN */
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: rgba(15,15,26,0.95); backdrop-filter: blur(20px); border-bottom: 1px solid var(--dark-border); padding: 0 28px; height: 62px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 40; }
        .page-title { font-size: 17px; font-weight: 700; color: var(--text-primary); }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .main-content { flex: 1; padding: 26px; }

        /* CARDS */
        .card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 14px; padding: 22px; transition: border-color 0.2s, box-shadow 0.2s; }
        .card:hover { border-color: rgba(99,102,241,0.3); box-shadow: 0 4px 20px rgba(99,102,241,0.08); }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .card-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }

        /* STAT CARDS */
        .stat-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 14px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.indigo { background: rgba(99,102,241,0.15); color: #818cf8; }
        .stat-icon.purple { background: rgba(139,92,246,0.15); color: #a78bfa; }
        .stat-icon.amber { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .stat-icon.red { background: rgba(239,68,68,0.15); color: #f87171; }
        .stat-icon.green { background: rgba(16,185,129,0.15); color: #34d399; }
        .stat-icon.cyan { background: rgba(6,182,212,0.15); color: #22d3ee; }
        .stat-value { font-size: 26px; font-weight: 800; background: linear-gradient(135deg, #e0e7ff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }

        /* BUTTONS */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); color: white; }
        .btn-success { background: linear-gradient(135deg, #059669, #10b981); color: white; box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
        .btn-success:hover { color: white; transform: translateY(-1px); }
        .btn-danger { background: linear-gradient(135deg, #dc2626, #ef4444); color: white; box-shadow: 0 4px 12px rgba(239,68,68,0.25); }
        .btn-danger:hover { color: white; transform: translateY(-1px); }
        .btn-secondary { background: rgba(255,255,255,0.07); color: var(--text-secondary); border: 1px solid var(--dark-border); }
        .btn-secondary:hover { background: rgba(255,255,255,0.12); color: var(--text-primary); }
        .btn-warning { background: linear-gradient(135deg, #d97706, #f59e0b); color: white; }
        .btn-warning:hover { color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-sm svg { width: 13px; height: 13px; }
        .btn-icon { padding: 7px; }

        /* TABLES */
        .table-wrapper { overflow-x: auto; border-radius: 10px; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .data-table th { background: rgba(99,102,241,0.08); color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; padding: 11px 16px; text-align: left; border-bottom: 1px solid var(--dark-border); }
        .data-table td { padding: 13px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); color: var(--text-primary); vertical-align: middle; }
        .data-table tr:hover td { background: rgba(99,102,241,0.04); }
        .data-table tr:last-child td { border-bottom: none; }

        /* BADGES */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 50px; font-size: 11px; font-weight: 600; letter-spacing: 0.3px; }
        .badge svg { width: 10px; height: 10px; }
        .badge-green { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
        .badge-yellow { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
        .badge-red { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
        .badge-blue { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }
        .badge-purple { background: rgba(139,92,246,0.15); color: #a78bfa; border: 1px solid rgba(139,92,246,0.25); }

        /* FORMS */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 7px; }
        .form-control { width: 100%; background: rgba(255,255,255,0.06); border: 1px solid var(--dark-border); border-radius: 8px; padding: 10px 14px; color: var(--text-primary); font-size: 13.5px; font-family: 'Inter', sans-serif; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control option { background: #1a1a35; color: var(--text-primary); }

        /* ALERTS */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 13px 16px; border-radius: 10px; font-size: 13.5px; margin-bottom: 18px; }
        .alert svg { width: 17px; height: 17px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; }
        .alert-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #fca5a5; }
        .alert-warning { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); color: #fde68a; }
        .alert-info { background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.25); color: #67e8f9; }

        /* GRID */
        .grid { display: grid; gap: 18px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        /* TOGGLE */
        .toggle { position: relative; width: 44px; height: 24px; cursor: pointer; display: inline-block; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; inset: 0; background: rgba(255,255,255,0.15); border-radius: 24px; transition: background 0.3s; }
        .toggle-slider:before { content: ''; position: absolute; width: 18px; height: 18px; background: white; border-radius: 50%; left: 3px; top: 3px; transition: transform 0.3s; }
        .toggle input:checked + .toggle-slider { background: var(--primary); }
        .toggle input:checked + .toggle-slider:before { transform: translateX(20px); }

        /* PAGINATION */
        .pagination-wrapper { display: flex; justify-content: center; margin-top: 22px; }

        /* MOBILE */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .main-content { padding: 16px; }
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.35s ease forwards; }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <i data-lucide="package-2"></i>
        </div>
        <div>
            <div class="logo-text">Aplikasi Stok</div>
            <div class="logo-sub">Manajemen Gudang</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a>

        <div class="nav-section-title">Produk</div>
        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i data-lucide="package"></i> Daftar Produk
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i data-lucide="tag"></i> Kategori
        </a>
        @endif

        <div class="nav-section-title">Stok</div>
        <a href="{{ route('stock.in') }}" class="nav-item {{ request()->routeIs('stock.in*') ? 'active' : '' }}">
            <i data-lucide="arrow-down-to-line"></i> Stok Masuk
        </a>
        <a href="{{ route('stock.out') }}" class="nav-item {{ request()->routeIs('stock.out*') ? 'active' : '' }}">
            <i data-lucide="arrow-up-from-line"></i> Stok Keluar
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('stock.adjust') }}" class="nav-item {{ request()->routeIs('stock.adjust*') ? 'active' : '' }}">
            <i data-lucide="sliders-horizontal"></i> Penyesuaian
        </a>
        @endif
        <a href="{{ route('stock.movements') }}" class="nav-item {{ request()->routeIs('stock.movements*') ? 'active' : '' }}">
            <i data-lucide="list"></i> Riwayat Pergerakan
        </a>

        <div class="nav-section-title">Laporan</div>
        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i data-lucide="bar-chart-2"></i> Laporan
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section-title">Pengaturan</div>
        <a href="{{ route('settings.telegram') }}" class="nav-item {{ request()->routeIs('settings.telegram*') ? 'active' : '' }}">
            <i data-lucide="send"></i> Telegram Bot
        </a>
        <a href="{{ route('settings.general') }}" class="nav-item {{ request()->routeIs('settings.general*') ? 'active' : '' }}">
            <i data-lucide="settings"></i> Pengaturan Umum
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size:11px; color:var(--text-muted);">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i data-lucide="log-out"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <div style="display:flex; align-items:center; gap:14px;">
            <button id="sidebarToggle" style="display:none; background:none; border:none; color:var(--text-secondary); cursor:pointer; padding:6px; border-radius:6px;" onclick="toggleSidebar()">
                <i data-lucide="menu" style="width:20px;height:20px;"></i>
            </button>
            <h1 class="page-title">@yield('title', 'Dashboard')</h1>
        </div>
        <div class="topbar-actions">
            <a href="{{ route('stock.in') }}" class="btn btn-success btn-sm">
                <i data-lucide="arrow-down-to-line"></i> Stok Masuk
            </a>
            <a href="{{ route('stock.out') }}" class="btn btn-danger btn-sm">
                <i data-lucide="arrow-up-from-line"></i> Stok Keluar
            </a>
        </div>
    </header>

    <main class="main-content fade-in">
        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle-2"></i>
                <span>{!! session('success') !!}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i data-lucide="x-circle"></i>
                <span>{!! session('error') !!}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <i data-lucide="alert-triangle"></i>
                <div>
                    <strong>Terdapat kesalahan:</strong>
                    <ul style="margin-top:5px; padding-left:16px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<div id="overlay" onclick="toggleSidebar()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:40;"></div>

<script>
    lucide.createIcons();

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('open');
        overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    }
    window.addEventListener('resize', () => {
        const t = document.getElementById('sidebarToggle');
        if (t) t.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
    });
    window.dispatchEvent(new Event('resize'));

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);
</script>
@yield('scripts')
</body>
</html>
