<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — StokApp</title>
    <meta name="description" content="Sistem manajemen stok dan gudang terintegrasi Telegram Bot untuk UMKM">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-w: 64px;

            /* === Palette: Sage + Teal === */
            --primary:        #0D9488;
            --primary-hover:  #0F766E;
            --primary-light:  #F0FDFA;
            --primary-mid:    #CCFBF1;

            --bg-body:    #EFF4F2;
            --bg-sidebar: #FFFFFF;
            --bg-card:    #FFFFFF;
            --bg-topbar:  #FFFFFF;

            --border:       #E2EAE8;
            --border-light: #F1F5F4;

            --text-primary:   #0F172A;
            --text-secondary: #475569;
            --text-muted:     #94A3B8;

            --success:       #16A34A;
            --success-light: #F0FDF4;
            --warning:       #D97706;
            --warning-light: #FFFBEB;
            --danger:        #DC2626;
            --danger-light:  #FEF2F2;
            --info:          #0891B2;
            --info-light:    #F0F9FF;

            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.09), 0 4px 10px rgba(0,0,0,0.04);

            --radius:    12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            font-size: 13.5px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }

        body.sidebar-expanded {
            --sidebar-w: 220px;
        }

        /* ══════════════ SIDEBAR ══════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.sidebar-expanded .sidebar {
            align-items: stretch;
        }

        /* Sidebar toggle button (arrow) at the middle */
        .sidebar-toggle {
            position: absolute;
            top: 50%;
            right: -10px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: var(--bg-sidebar);
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-xs);
            z-index: 100;
            transition: all 0.2s ease;
            color: var(--text-secondary);
        }
        .sidebar-toggle:hover {
            background: var(--primary-light);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }
        .sidebar-toggle i, .sidebar-toggle svg {
            width: 12px;
            height: 12px;
            transition: transform 0.25s ease;
        }
        body.sidebar-expanded .sidebar-toggle i,
        body.sidebar-expanded .sidebar-toggle svg {
            transform: rotate(180deg);
        }

        /* Logo area */
        .sidebar-logo {
            width: 100%;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--border-light);
            flex-shrink: 0;
            transition: padding 0.25s;
            padding: 0 12px;
            gap: 10px;
        }
        body.sidebar-expanded .sidebar-logo {
            justify-content: flex-start;
            padding: 0 16px;
        }
        .logo-icon {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 3px 8px rgba(13,148,136,0.25);
            flex-shrink: 0;
        }
        .logo-icon i, .logo-icon svg { width: 16px; height: 16px; color: white; stroke: white; }
        
        .logo-text {
            font-size: 15px;
            font-weight: 800;
            color: var(--primary);
            display: none;
            white-space: nowrap;
            letter-spacing: -0.3px;
        }
        body.sidebar-expanded .logo-text {
            display: block;
        }

        /* Nav items */
        .sidebar-nav {
            flex: 1;
            display: flex; flex-direction: column; align-items: center;
            padding: 8px 0;
            gap: 2px;
            width: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
        body.sidebar-expanded .sidebar-nav {
            align-items: stretch;
            padding: 8px 10px;
        }

        .nav-divider {
            width: 20px; height: 1px;
            background: var(--border-light);
            margin: 4px 0;
            flex-shrink: 0;
            transition: width 0.25s;
        }
        body.sidebar-expanded .nav-divider {
            width: 100%;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 16px 0 6px 16px;
            display: none;
            align-self: flex-start;
        }
        body.sidebar-expanded .nav-section-title {
            display: block;
        }

        .nav-item {
            position: relative;
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            color: var(--text-muted);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        body.sidebar-expanded .nav-item {
            width: 100%;
            justify-content: flex-start;
            padding: 0 10px;
            gap: 10px;
            height: 38px;
        }
        .nav-item i, .nav-item svg { width: 17px; height: 17px; transition: color 0.18s; flex-shrink: 0; }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }
        .nav-item.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(13,148,136,0.28);
        }
        .nav-item.active i, .nav-item.active svg { color: white; }

        .nav-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            display: none;
            white-space: nowrap;
        }
        .nav-item:hover .nav-label {
            color: var(--primary);
        }
        .nav-item.active .nav-label {
            color: white;
        }
        body.sidebar-expanded .nav-label {
            display: block;
        }

        /* ══════════════ HINTS & TOOLTIPS ══════════════ */
        .hint-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            margin-left: 6px;
        }
        
        .hint-toggle {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--warning);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            animation: pulse-warning 2s infinite;
            transition: all 0.2s;
        }
        .hint-toggle:hover, .hint-toggle.active {
            background: var(--warning-light);
            color: var(--warning);
            border: 1px solid var(--warning);
            animation: none;
        }
        
        .hint-toggle i, .hint-toggle svg {
            width: 12px;
            height: 12px;
        }

        @keyframes pulse-warning {
            0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(217, 119, 6, 0); }
            100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
        }

        .hint-popover {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            background: white;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            padding: 14px 16px;
            border-radius: 8px;
            width: max-content;
            max-width: 320px;
            font-size: 13px;
            color: var(--text-secondary);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            white-space: normal;
            pointer-events: none;
        }
        
        /* Arrow */
        .hint-popover::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 14px;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent white transparent;
        }
        .hint-popover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 13px;
            border-width: 7px;
            border-style: solid;
            border-color: transparent transparent var(--border) transparent;
        }

        .hint-popover.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .hint-popover h4 {
            margin: 0 0 6px 0;
            color: var(--text-primary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .hint-popover ol {
            padding-left: 20px;
            margin: 0;
        }
        .hint-popover li {
            margin-bottom: 4px;
        }


        /* Tooltip */
        .nav-tooltip {
            position: absolute;
            left: calc(100% + 13px);
            top: 50%;
            transform: translateY(-50%) translateX(-6px);
            background: #0F172A;
            color: white;
            font-size: 12px; font-weight: 600;
            padding: 6px 11px;
            border-radius: var(--radius-sm);
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease, transform 0.15s ease;
            z-index: 200;
            line-height: 1.4;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .nav-tooltip::before {
            content: '';
            position: absolute;
            right: 100%; top: 50%; transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #0F172A;
        }
        .nav-item:hover .nav-tooltip {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
        body.sidebar-expanded .nav-tooltip {
            display: none !important;
        }

        /* Sidebar footer */
        .sidebar-footer {
            width: 100%; padding: 10px 0;
            border-top: 1px solid var(--border-light);
            display: flex; flex-direction: column; align-items: center; gap: 3px;
        }
        body.sidebar-expanded .sidebar-footer {
            padding: 12px;
            align-items: stretch;
            gap: 6px;
        }

        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary) 0%, #065F46 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px; color: white;
            cursor: default;
            box-shadow: 0 2px 6px rgba(13,148,136,0.2);
            flex-shrink: 0;
        }

        .user-info-wrap {
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        .user-info-name {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            line-height: 1.2;
        }
        .user-info-role {
            font-size: 10.5px;
            color: var(--text-muted);
            white-space: nowrap;
            margin-top: 1px;
        }
        body.sidebar-expanded .user-info-wrap {
            display: flex;
        }

        .logout-btn {
            width: 42px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            border: none; background: none; cursor: pointer;
            color: var(--text-muted);
            border-radius: var(--radius-sm);
            transition: all 0.18s;
            position: relative;
        }
        body.sidebar-expanded .logout-btn {
            width: 100%;
            justify-content: flex-start;
            padding: 0 12px;
            gap: 12px;
            height: 42px;
        }
        .logout-btn:hover { background: var(--danger-light); color: var(--danger); }
        .logout-btn i, .logout-btn svg { width: 17px; height: 17px; flex-shrink: 0; }
        
        .logout-label {
            font-size: 13px;
            font-weight: 600;
            display: none;
            white-space: nowrap;
        }
        body.sidebar-expanded .logout-label {
            display: block;
        }

        /* ══════════════ MAIN WRAPPER ══════════════ */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1; display: flex; flex-direction: column;
            min-height: 100vh; min-width: 0;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Topbar */
        .topbar {
            background: var(--bg-topbar);
            border-bottom: 1px solid var(--border);
            padding: 0 26px;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 40;
            box-shadow: var(--shadow-xs);
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .page-breadcrumb {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; color: var(--text-muted);
            margin-bottom: 1px;
        }
        .page-breadcrumb i { width: 10px; height: 10px; }
        .page-breadcrumb .sep { color: var(--border); }
        .page-title { font-size: 15px; font-weight: 700; color: var(--text-primary); line-height: 1.2; }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-chip {
            display: flex; align-items: center; gap: 5px;
            padding: 5px 11px; border-radius: 20px;
            background: var(--border-light);
            font-size: 11.5px; font-weight: 600; color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        .topbar-chip i { width: 11px; height: 11px; }

        .main-content { flex: 1; padding: 22px 26px; }

        /* ══════════════ CARDS ══════════════ */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s;
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px; padding-bottom: 13px;
            border-bottom: 1px solid var(--border-light);
        }
        .card-title { font-size: 13.5px; font-weight: 700; color: var(--text-primary); }
        .card-subtitle { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* ══════════════ STAT CARDS ══════════════ */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            border-radius: var(--radius) var(--radius) 0 0;
        }
        .stat-card.teal::after  { background: var(--primary); }
        .stat-card.green::after { background: var(--success); }
        .stat-card.amber::after { background: var(--warning); }
        .stat-card.red::after   { background: var(--danger); }
        .stat-card.cyan::after  { background: var(--info); }
        .stat-card.slate::after { background: #64748B; }

        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

        .stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-icon i, .stat-icon svg { width: 18px; height: 18px; }
        .stat-icon.teal  { background: #F0FDFA; color: var(--primary); }
        .stat-icon.green { background: #F0FDF4; color: var(--success); }
        .stat-icon.amber { background: #FFFBEB; color: var(--warning); }
        .stat-icon.red   { background: #FEF2F2; color: var(--danger); }
        .stat-icon.cyan  { background: #F0F9FF; color: var(--info); }
        .stat-icon.slate { background: #F8FAFC; color: #64748B; }

        .stat-value {
            font-size: 27px; font-weight: 800;
            color: var(--text-primary);
            line-height: 1; letter-spacing: -0.5px;
        }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }

        /* ══════════════ BUTTONS ══════════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 15px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.18s;
            white-space: nowrap;
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1;
        }
        .btn i, .btn svg { width: 14px; height: 14px; flex-shrink: 0; }

        .btn-primary {
            background: var(--primary); color: white;
            box-shadow: 0 2px 8px rgba(13,148,136,0.25);
        }
        .btn-primary:hover { background: var(--primary-hover); color: white; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(13,148,136,0.32); }

        .btn-success {
            background: var(--success); color: white;
            box-shadow: 0 2px 8px rgba(22,163,74,0.22);
        }
        .btn-success:hover { background: #15803D; color: white; transform: translateY(-1px); }

        .btn-danger {
            background: var(--danger); color: white;
            box-shadow: 0 2px 8px rgba(220,38,38,0.18);
        }
        .btn-danger:hover { background: #B91C1C; color: white; transform: translateY(-1px); }

        .btn-warning { background: var(--warning); color: white; }
        .btn-warning:hover { background: #B45309; color: white; }

        .btn-outline {
            background: transparent; color: var(--text-secondary);
            border: 1.5px solid var(--border);
        }
        .btn-outline:hover { background: var(--border-light); color: var(--text-primary); border-color: #C8D5D2; }

        .btn-ghost { background: transparent; color: var(--text-secondary); }
        .btn-ghost:hover { background: var(--border-light); color: var(--text-primary); }

        .btn-sm  { padding: 6px 11px; font-size: 12px; }
        .btn-sm i, .btn-sm svg { width: 13px; height: 13px; }
        .btn-lg  { padding: 11px 22px; font-size: 14px; }
        .btn-icon { padding: 8px; }
        .btn-icon i, .btn-icon svg { width: 16px; height: 16px; }

        /* ══════════════ TABLES ══════════════ */
        .table-wrapper { overflow-x: auto; border-radius: var(--radius-sm); }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table thead tr { background: #F8FAFB; }
        .data-table th {
            color: var(--text-muted); font-weight: 700;
            text-transform: uppercase; font-size: 10.5px; letter-spacing: 0.6px;
            padding: 10px 16px; text-align: left;
            border-bottom: 1px solid var(--border); white-space: nowrap;
        }
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-primary); vertical-align: middle;
        }
        .data-table tbody tr { transition: background 0.1s; }
        .data-table tbody tr:hover td { background: #F5FAF9; }
        .data-table tbody tr:last-child td { border-bottom: none; }

        /* ══════════════ BADGES ══════════════ */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 20px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.1px;
        }
        .badge i, .badge svg { width: 10px; height: 10px; }
        .badge-green  { background: #DCFCE7; color: #15803D; }
        .badge-yellow { background: #FEF9C3; color: #A16207; }
        .badge-red    { background: #FEE2E2; color: #B91C1C; }
        .badge-blue   { background: #DBEAFE; color: #1D4ED8; }
        .badge-teal   { background: #CCFBF1; color: #0F766E; }
        .badge-slate  { background: #F1F5F9; color: #475569; }

        /* ══════════════ FORMS ══════════════ */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 12.5px; font-weight: 600;
            color: var(--text-secondary); margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            background: #FAFBFB;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 9px 13px;
            color: var(--text-primary);
            font-size: 13.5px; font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-control:focus {
            outline: none; background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control { cursor: pointer; }

        /* ══════════════ ALERTS ══════════════ */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 11px 15px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 500; margin-bottom: 16px;
        }
        .alert i, .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; }
        .alert-error   { background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; }
        .alert-warning { background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; }
        .alert-info    { background: #F0F9FF; border: 1px solid #BAE6FD; color: #0C4A6E; }

        /* ══════════════ GRID ══════════════ */
        .grid   { display: grid; gap: 16px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        /* ══════════════ TOGGLE ══════════════ */
        .toggle { position: relative; width: 40px; height: 22px; cursor: pointer; display: inline-block; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; inset: 0; background: #CBD5E1; border-radius: 22px; transition: background 0.25s; }
        .toggle-slider:before {
            content: ''; position: absolute;
            width: 16px; height: 16px; background: white;
            border-radius: 50%; left: 3px; top: 3px;
            transition: transform 0.25s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .toggle input:checked + .toggle-slider { background: var(--primary); }
        .toggle input:checked + .toggle-slider:before { transform: translateX(18px); }

        /* ══════════════ MISC ══════════════ */
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .section-title { font-size: 14.5px; font-weight: 700; color: var(--text-primary); }
        .section-sub   { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .empty-state {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 38px; color: var(--text-muted); gap: 10px;
        }
        .empty-state i, .empty-state svg { width: 36px; height: 36px; color: #CBD5E1; }
        .empty-state p { font-size: 13px; }

        .divider { height: 1px; background: var(--border-light); margin: 16px 0; }
        .pagination-wrapper { display: flex; justify-content: flex-end; margin-top: 18px; }

        /* ══════════════ MOBILE ══════════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.28s cubic-bezier(.4,0,.2,1); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .main-content { padding: 14px 16px; }
            .topbar { padding: 0 16px; }
            .topbar-chip { display: none; }
        }

        @media (max-width: 480px) {
            .grid-4 { grid-template-columns: 1fr 1fr; }
        }

        /* ══════════════ ANIMATIONS ══════════════ */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: fadeInUp 0.28s ease forwards; }
    </style>
</head>
<body>

{{-- ════ SIDEBAR ════ --}}
<aside class="sidebar" id="sidebar">
    {{-- Sidebar Toggle Button --}}
    <button class="sidebar-toggle" id="sidebarCollapseToggle" aria-label="Toggle Sidebar">
        <i data-lucide="chevron-right"></i>
    </button>

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="logo-icon">
            <i data-lucide="package-2"></i>
        </div>
        <span class="logo-text">StokApp</span>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <div class="nav-section-title">Main Menu</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i>
            <span class="nav-label">Dashboard</span>
            <span class="nav-tooltip">Dashboard</span>
        </a>

        <div class="nav-divider"></div>

        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i data-lucide="package"></i>
            <span class="nav-label">Daftar Produk</span>
            <span class="nav-tooltip">Daftar Produk</span>
        </a>

        <div class="nav-divider"></div>

        <a href="{{ route('stock.in') }}" class="nav-item {{ request()->routeIs('stock.in*') ? 'active' : '' }}">
            <i data-lucide="arrow-down-to-line"></i>
            <span class="nav-label">Stok Masuk</span>
            <span class="nav-tooltip">Stok Masuk</span>
        </a>
        <a href="{{ route('stock.out') }}" class="nav-item {{ request()->routeIs('stock.out*') ? 'active' : '' }}">
            <i data-lucide="arrow-up-from-line"></i>
            <span class="nav-label">Stok Keluar</span>
            <span class="nav-tooltip">Stok Keluar</span>
        </a>
        <a href="{{ route('stock.movements') }}" class="nav-item {{ request()->routeIs('stock.movements*') ? 'active' : '' }}">
            <i data-lucide="history"></i>
            <span class="nav-label">Riwayat</span>
            <span class="nav-tooltip">Riwayat Pergerakan</span>
        </a>

        <div class="nav-divider"></div>

        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i data-lucide="bar-chart-2"></i>
            <span class="nav-label">Laporan</span>
            <span class="nav-tooltip">Laporan</span>
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-divider"></div>
        <div class="nav-section-title">Administrator</div>
        
        <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i data-lucide="tag"></i>
            <span class="nav-label">Kategori</span>
            <span class="nav-tooltip">Manajemen Kategori</span>
        </a>
        <a href="{{ route('stock.adjust') }}" class="nav-item {{ request()->routeIs('stock.adjust*') ? 'active' : '' }}">
            <i data-lucide="sliders"></i>
            <span class="nav-label">Penyesuaian Stok</span>
            <span class="nav-tooltip">Penyesuaian Stok</span>
        </a>
        <a href="{{ route('settings.telegram') }}" class="nav-item {{ request()->routeIs('settings.telegram*') ? 'active' : '' }}">
            <i data-lucide="send"></i>
            <span class="nav-label">Telegram Bot</span>
            <span class="nav-tooltip">Pengaturan Telegram Bot</span>
        </a>
        <a href="{{ route('settings.general') }}" class="nav-item {{ request()->routeIs('settings.general*') ? 'active' : '' }}">
            <i data-lucide="settings-2"></i>
            <span class="nav-label">Pengaturan Umum</span>
            <span class="nav-tooltip">Pengaturan Umum</span>
        </a>
        @endif
    </nav>
</aside>

{{-- ════ MAIN ════ --}}
<div class="main-wrapper">
    {{-- Topbar --}}
    <header class="topbar">
        <div class="topbar-left">
            <button id="sidebarToggle" style="display:none; background:none; border:none; color:var(--text-secondary); cursor:pointer; padding:6px; border-radius:6px;" onclick="toggleSidebar()">
                <i data-lucide="menu" style="width:20px;height:20px;"></i>
            </button>
            <div>
                <div class="page-breadcrumb">
                    <i data-lucide="home"></i>
                    <span>StokApp</span>
                    <span class="sep">/</span>
                    <span>@yield('title', 'Dashboard')</span>
                </div>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            </div>
        </div>
        <div class="topbar-actions">
            <div class="topbar-chip" style="border:none; background:transparent; padding:0; gap:16px;">
                <div style="display:flex; align-items:center; gap:6px; color:var(--text-primary);">
                    <i data-lucide="calendar" style="color:var(--primary); width:14px; height:14px;"></i>
                    <span id="topbar-date">-- --- ----</span>
                </div>
                <div style="display:flex; align-items:center; gap:6px; color:var(--text-secondary);">
                    <i data-lucide="clock" style="width:14px; height:14px;"></i>
                    <span id="topbar-time">--:--</span>
                </div>
            </div>
            
            {{-- User Profile Dropdown --}}
            <div class="user-dropdown-wrapper" style="position:relative; margin-left:8px;">
                <button class="user-dropdown-toggle" style="background:var(--primary-light); border:1px solid var(--border-light); border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:var(--primary); font-weight:700; font-size:14px; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.boxShadow='var(--shadow-sm)'; this.style.background='var(--primary-mid)';" onmouseout="this.style.boxShadow='none'; this.style.background='var(--primary-light)';">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </button>
                <div class="user-dropdown-menu" style="position:absolute; top:calc(100% + 10px); right:0; background:white; border:1px solid var(--border); box-shadow:var(--shadow-lg); border-radius:8px; width:220px; padding:8px; display:none; flex-direction:column; z-index:100; transform:translateY(-10px); opacity:0; transition:all 0.2s ease;">
                    <div style="padding:8px 12px; border-bottom:1px solid var(--border-light); margin-bottom:4px; display:flex; align-items:center; gap:12px;">
                        <div style="background:var(--primary); color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div style="overflow:hidden;">
                            <div style="font-weight:700; color:var(--text-primary); white-space:nowrap; text-overflow:ellipsis; overflow:hidden;">{{ auth()->user()->name }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="width:100%;">
                        @csrf
                        <button type="submit" style="width:100%; display:flex; align-items:center; gap:8px; padding:8px 12px; background:none; border:none; color:var(--danger); border-radius:6px; cursor:pointer; text-align:left; font-size:13px; font-weight:600; transition:all 0.15s;" onmouseover="this.style.background='var(--danger-light)'" onmouseout="this.style.background='none'">
                            <i data-lucide="log-out" style="width:16px;height:16px;"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Content --}}
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

{{-- Mobile overlay --}}
<div id="overlay" onclick="toggleSidebar()"
    style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.35); z-index:45; backdrop-filter:blur(2px);"></div>

<script>
    lucide.createIcons();

    // Live clock and date
    (function tick() {
        const elTime = document.getElementById('topbar-time');
        const elDate = document.getElementById('topbar-date');
        const now = new Date();
        
        if (elTime) {
            elTime.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        if (elDate) {
            elDate.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
        
        setTimeout(tick, 10000);
    })();

    // Mobile sidebar
    function toggleSidebar() {
        const s = document.getElementById('sidebar');
        const o = document.getElementById('overlay');
        s.classList.toggle('open');
        o.style.display = s.classList.contains('open') ? 'block' : 'none';
    }
    // Sidebar Expand/Collapse Toggle & Persistence
    (function initSidebar() {
        const toggleBtn = document.getElementById('sidebarCollapseToggle');
        const body = document.body;
        
        /* Global script */
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('sidebarToggle');
            if (toggle) {
                toggle.addEventListener('click', () => {
                    document.body.classList.toggle('sidebar-expanded');
                });
            }
            
            // Hint Popover Logic
            document.addEventListener('click', (e) => {
                // User Dropdown Logic
                const isUserToggle = e.target.closest('.user-dropdown-toggle');
                const userMenu = document.querySelector('.user-dropdown-menu');
                
                if (isUserToggle) {
                    const isActive = userMenu.style.display === 'flex';
                    if (isActive) {
                        userMenu.style.opacity = '0';
                        userMenu.style.transform = 'translateY(-10px)';
                        setTimeout(() => userMenu.style.display = 'none', 200);
                    } else {
                        userMenu.style.display = 'flex';
                        // Small delay to allow display:flex to apply before transition
                        setTimeout(() => {
                            userMenu.style.opacity = '1';
                            userMenu.style.transform = 'translateY(0)';
                        }, 10);
                    }
                } else if (userMenu && !e.target.closest('.user-dropdown-wrapper') && userMenu.style.display === 'flex') {
                    userMenu.style.opacity = '0';
                    userMenu.style.transform = 'translateY(-10px)';
                    setTimeout(() => userMenu.style.display = 'none', 200);
                }

                // Existing Hint Popover Logic
                const isHintToggle = e.target.closest('.hint-toggle');
                
                // Close all popovers first if clicking outside or clicking another toggle
                document.querySelectorAll('.hint-popover.show').forEach(popover => {
                    const wrapper = popover.closest('.hint-wrapper');
                    if (!wrapper.contains(e.target)) {
                        popover.classList.remove('show');
                        wrapper.querySelector('.hint-toggle').classList.remove('active');
                    }
                });

                // Toggle clicked popover
                if (isHintToggle) {
                    const wrapper = isHintToggle.closest('.hint-wrapper');
                    const popover = wrapper.querySelector('.hint-popover');
                    const isActive = popover.classList.contains('show');
                    
                    if (isActive) {
                        popover.classList.remove('show');
                        isHintToggle.classList.remove('active');
                    } else {
                        popover.classList.add('show');
                        isHintToggle.classList.add('active');
                    }
                }
            });
        });

        // Load initial state
        if (localStorage.getItem('sidebar-expanded') === 'true') {
            body.classList.add('sidebar-expanded');
        }
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                body.classList.toggle('sidebar-expanded');
                const isExpanded = body.classList.contains('sidebar-expanded');
                localStorage.setItem('sidebar-expanded', isExpanded);
                
                // Re-dispatch window resize to update chart layouts
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 250);
            });
        }
    })();

    window.addEventListener('resize', () => {
        const t = document.getElementById('sidebarToggle');
        if (t) t.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
    });
    window.dispatchEvent(new Event('resize'));

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity 0.4s, transform 0.4s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            setTimeout(() => el.remove(), 400);
        });
    }, 4500);
</script>
@yield('scripts')
</body>
</html>
