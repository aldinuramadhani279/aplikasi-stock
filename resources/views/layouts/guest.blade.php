<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'StokApp') }} — Masuk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #EFF4F2;
            -webkit-font-smoothing: antialiased;
        }

        .auth-split {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ── Left branding panel ── */
        .auth-left {
            flex: 0 0 400px;
            background: linear-gradient(150deg, #0D9488 0%, #0E7A6E 40%, #065F46 100%);
            display: flex;
            flex-direction: column;
            padding: 44px 40px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative blobs */
        .auth-left::before {
            content: '';
            position: absolute;
            top: -100px; right: -80px;
            width: 280px; height: 280px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -60px;
            width: 260px; height: 260px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .auth-blob-mid {
            position: absolute;
            top: 45%; left: 50%;
            transform: translate(-50%, -50%);
            width: 420px; height: 420px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Branding header */
        .auth-brand {
            display: flex; align-items: center; gap: 12px;
            position: relative; z-index: 1;
            margin-bottom: auto;
        }
        .auth-brand-icon {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.18);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
        }
        .auth-brand-icon i, .auth-brand-icon svg { width: 21px; height: 21px; color: white; stroke: white; }
        .auth-brand-name { font-size: 17px; font-weight: 800; color: white; letter-spacing: -0.3px; }
        .auth-brand-sub { font-size: 11.5px; color: rgba(255,255,255,0.65); font-weight: 400; margin-top: 1px; }

        /* Hero text */
        .auth-hero {
            flex: 1;
            display: flex; flex-direction: column; justify-content: center;
            position: relative; z-index: 1;
        }
        .auth-hero h2 {
            font-size: 28px; font-weight: 800; color: white;
            line-height: 1.25; margin-bottom: 14px;
            letter-spacing: -0.5px;
        }
        .auth-hero p {
            font-size: 13.5px; color: rgba(255,255,255,0.72);
            line-height: 1.75; max-width: 280px;
        }

        /* Feature list */
        .auth-features {
            display: flex; flex-direction: column; gap: 12px;
            margin-top: 32px;
            position: relative; z-index: 1;
        }
        .auth-feature { display: flex; align-items: center; gap: 11px; }
        .auth-feature-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.14);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .auth-feature-icon i, .auth-feature-icon svg { width: 14px; height: 14px; color: white; stroke: white; }
        .auth-feature-text { font-size: 12.5px; color: rgba(255,255,255,0.82); font-weight: 500; }

        /* Version badge at bottom */
        .auth-version {
            position: relative; z-index: 1;
            margin-top: 32px;
            font-size: 11px; color: rgba(255,255,255,0.4);
        }

        /* ── Right form panel ── */
        .auth-right {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 40px;
            background: #FFFFFF;
        }

        .auth-form-wrap { width: 100%; max-width: 360px; }

        /* Form header */
        .auth-form-header { margin-bottom: 32px; }
        .auth-form-header .greeting {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600;
            color: #0D9488;
            background: #F0FDFA;
            border: 1px solid #CCFBF1;
            padding: 4px 11px; border-radius: 20px;
            margin-bottom: 14px;
        }
        .auth-form-header .greeting i { width: 12px; height: 12px; }
        .auth-form-header h1 {
            font-size: 22px; font-weight: 800; color: #0F172A;
            margin-bottom: 7px; letter-spacing: -0.3px;
        }
        .auth-form-header p { font-size: 13.5px; color: #64748B; line-height: 1.55; }

        /* Form fields */
        .f-group { margin-bottom: 18px; }
        .f-label {
            display: block; font-size: 12px; font-weight: 700;
            color: #475569; margin-bottom: 6px; letter-spacing: 0.1px;
        }
        .f-input-wrap { position: relative; }
        .f-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: #94A3B8; pointer-events: none;
        }
        .f-icon i, .f-icon svg { width: 15px; height: 15px; }
        .f-input {
            width: 100%;
            border: 1.5px solid #E2E8F0;
            border-radius: 9px;
            padding: 10px 13px 10px 38px;
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0F172A;
            background: #FAFBFB;
            transition: all 0.18s;
        }
        .f-input:focus {
            outline: none;
            border-color: #0D9488;
            background: white;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        }
        .f-input::placeholder { color: #94A3B8; }

        .f-error {
            font-size: 11.5px; color: #DC2626; margin-top: 5px;
            display: flex; align-items: center; gap: 4px;
        }
        .f-error i { width: 12px; height: 12px; flex-shrink: 0; }

        /* Row: remember + forgot */
        .f-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }
        .f-check { display: flex; align-items: center; gap: 7px; cursor: pointer; }
        .f-check input {
            width: 15px; height: 15px;
            accent-color: #0D9488; cursor: pointer;
        }
        .f-check span { font-size: 12.5px; color: #64748B; cursor: pointer; }
        .f-forgot {
            font-size: 12.5px; color: #0D9488; font-weight: 600;
            text-decoration: none;
        }
        .f-forgot:hover { color: #0F766E; text-decoration: underline; }

        /* Submit button */
        .btn-login {
            width: 100%;
            background: #0D9488; color: white;
            border: none; border-radius: 9px;
            padding: 12px;
            font-size: 14px; font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all 0.18s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 16px rgba(13,148,136,0.28);
            letter-spacing: 0.1px;
        }
        .btn-login:hover {
            background: #0F766E;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13,148,136,0.35);
        }
        .btn-login i, .btn-login svg { width: 15px; height: 15px; }

        /* Footer note */
        .auth-note {
            margin-top: 28px; text-align: center;
            font-size: 11.5px; color: #94A3B8; line-height: 1.6;
        }

        /* Status / error alerts */
        .alert-auth {
            display: flex; align-items: flex-start; gap: 9px;
            padding: 11px 14px; border-radius: 9px;
            margin-bottom: 18px;
            font-size: 13px; font-weight: 500;
        }
        .alert-auth i, .alert-auth svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }
        .alert-auth.success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; }
        .alert-auth.error   { background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; }

        /* Responsive */
        @media (max-width: 860px) {
            .auth-left { display: none; }
        }
        @media (max-width: 480px) {
            .auth-right { padding: 32px 20px; background: #EFF4F2; }
            .auth-form-wrap {
                background: white; padding: 28px 22px;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            }
        }
    </style>
</head>
<body>
<div class="auth-split">
    {{-- Left branding --}}
    <div class="auth-left">
        <div class="auth-blob-mid"></div>

        <div class="auth-brand">
            <div class="auth-brand-icon">
                <i data-lucide="package-2"></i>
            </div>
            <div>
                <div class="auth-brand-name">StokApp</div>
                <div class="auth-brand-sub">Manajemen Gudang</div>
            </div>
        </div>

        <div class="auth-hero">
            <h2>Kelola Stok<br>Lebih Mudah<br>&amp; Efisien</h2>
            <p>Sistem manajemen stok terintegrasi Telegram Bot untuk UMKM dan bisnis kecil Anda.</p>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i data-lucide="zap"></i></div>
                    <div class="auth-feature-text">Notifikasi real-time via Telegram</div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i data-lucide="bar-chart-2"></i></div>
                    <div class="auth-feature-text">Laporan stok otomatis harian</div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i data-lucide="shield-check"></i></div>
                    <div class="auth-feature-text">Role admin &amp; staff terpisah</div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i data-lucide="file-spreadsheet"></i></div>
                    <div class="auth-feature-text">Export Excel &amp; PDF kapan saja</div>
                </div>
            </div>
        </div>

        <div class="auth-version">StokApp v1.0 · Laravel 10</div>
    </div>

    {{-- Right form --}}
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="auth-form-header">
                <div class="greeting">
                    <i data-lucide="sun"></i> Selamat datang kembali
                </div>
                <h1>Masuk ke Akun Anda</h1>
                <p>Gunakan email dan password yang terdaftar untuk melanjutkan.</p>
            </div>

            {{ $slot }}

            <div class="auth-note">
                Dengan masuk, Anda menyetujui kebijakan penggunaan sistem.<br>
                &copy; {{ date('Y') }} StokApp · Semua hak dilindungi.
            </div>
        </div>
    </div>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
