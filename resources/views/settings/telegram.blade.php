@extends('layouts.app')
@section('title', 'Konfigurasi Telegram Bot')

@section('content')
<div style="max-width:800px;">

    {{-- Status Info --}}
    @php
        $botToken = $settings['bot_token'] ?? '';
        $isConfigured = !empty($botToken);
    @endphp

    <div class="alert {{ $isConfigured ? 'alert-success' : 'alert-warning' }}" style="margin-bottom:24px;">
        @if($isConfigured)
            <i data-lucide="check-circle" style="width:20px;height:20px;"></i>
            <span>Bot Token sudah dikonfigurasi. Bot siap digunakan.</span>
        @else
            <i data-lucide="alert-triangle" style="width:20px;height:20px;"></i>
            <span>Bot Token belum diisi. Isi form di bawah untuk mengaktifkan Telegram Bot.</span>
        @endif
    </div>

    <form method="POST" action="{{ route('settings.telegram.update') }}">
        @csrf

        {{-- Bot Connection --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title" style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="send" style="width:20px;height:20px;"></i> Koneksi Bot Telegram
            </div>

            <div class="form-group">
                <label class="form-label">
                    Bot Token *
                    <div class="hint-wrapper">
                        <span class="hint-toggle"><i data-lucide="info"></i></span>
                        <div class="hint-popover">
                            <h4>Cara Mendapatkan Token:</h4>
                            <ol>
                                <li>Buka aplikasi Telegram, cari <strong>@BotFather</strong></li>
                                <li>Ketik pesan <code>/newbot</code> dan ikuti instruksi (nama bot & username)</li>
                                <li>Setelah selesai, BotFather akan memberikan tulisan panjang (Token). Salin teks tersebut.</li>
                                <li>Paste token tersebut ke kolom ini.</li>
                            </ol>
                        </div>
                    </div>
                </label>
                <input type="password" name="bot_token" value="{{ $settings['bot_token'] ?? '' }}" class="form-control" placeholder="1234567890:ABCdef...">
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">Dapatkan dari @BotFather di Telegram</div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Webhook URL
                    <div class="hint-wrapper">
                        <span class="hint-toggle"><i data-lucide="info"></i></span>
                        <div class="hint-popover">
                            <h4>Fungsi Webhook URL:</h4>
                            <p style="margin:0 0 6px 0;">Ini adalah jembatan agar Telegram bisa "ngobrol" dengan aplikasi Anda.</p>
                            <ol>
                                <li>Jika Anda pakai komputer lokal, jalankan Ngrok (contoh: <code>ngrok http 8000</code>).</li>
                                <li>Copy URL HTTPS yang diberikan Ngrok.</li>
                                <li>Paste di sini dan WAJIB ditambahkan <code>/api/telegram/webhook</code> di ujungnya.</li>
                            </ol>
                        </div>
                    </div>
                </label>
                <input type="url" name="webhook_url" value="{{ $settings['webhook_url'] ?? '' }}" class="form-control" placeholder="https://yourdomain.com/api/telegram/webhook">
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">Harus HTTPS. Gunakan ngrok untuk development lokal.</div>
            </div>
        </div>

        {{-- Chat IDs --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title" style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="message-square" style="width:20px;height:20px;"></i> Konfigurasi Chat ID
            </div>
            <div style="background:rgba(99,102,241,0.06); padding:12px 16px; border-radius:8px; font-size:13px; color:var(--text-secondary); margin-bottom:20px; display:flex; gap:8px;">
                <i data-lucide="lightbulb" style="width:18px;height:18px;color:#818cf8;flex-shrink:0;"></i>
                <span><strong>Cara mendapatkan Chat ID:</strong> Kirim pesan ke bot, lalu akses <code style="background:rgba(0,0,0,0.3); padding:2px 6px; border-radius:4px;">https://api.telegram.org/bot[TOKEN]/getUpdates</code></span>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Default Chat ID</label>
                    <input type="text" name="default_chat_id" value="{{ $settings['default_chat_id'] ?? '' }}" class="form-control" placeholder="-123456789">
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Notifikasi umum & transaksi</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Report Chat ID</label>
                    <input type="text" name="report_chat_id" value="{{ $settings['report_chat_id'] ?? '' }}" class="form-control" placeholder="-123456789">
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Laporan harian otomatis</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alert Chat ID</label>
                    <input type="text" name="alert_chat_id" value="{{ $settings['alert_chat_id'] ?? '' }}" class="form-control" placeholder="-123456789">
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Alert stok menipis/habis</div>
                </div>
            </div>
        </div>

        {{-- Notification Settings --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title" style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="bell" style="width:20px;height:20px;"></i> Pengaturan Notifikasi
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:rgba(255,255,255,0.03); border-radius:10px;">
                    <div>
                        <div style="font-weight:600; font-size:14px;">Notifikasi Stok Menipis</div>
                        <div style="font-size:12px; color:var(--text-muted);">Kirim alert otomatis ke Alert Chat ID saat stok ≤ minimum</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="low_stock_notification" {{ ($settings['low_stock_notification'] ?? '1') === '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:rgba(255,255,255,0.03); border-radius:10px;">
                    <div>
                        <div style="font-weight:600; font-size:14px;">Laporan Harian Otomatis</div>
                        <div style="font-size:12px; color:var(--text-muted);">Kirim laporan stok setiap hari ke Report Chat ID</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="daily_report_enabled" {{ ($settings['daily_report_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:rgba(255,255,255,0.03); border-radius:10px;">
                    <div>
                        <div style="font-weight:600; font-size:14px;">Konfirmasi Transaksi</div>
                        <div style="font-size:12px; color:var(--text-muted);">Kirim notifikasi setiap ada input stok masuk/keluar</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="transaction_notification" {{ ($settings['transaction_notification'] ?? '1') === '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-top:20px;">
                <label class="form-label">Jam Laporan Harian</label>
                <input type="time" name="daily_report_time" value="{{ $settings['daily_report_time'] ?? '08:00' }}" class="form-control" style="max-width:150px;">
            </div>
        </div>

        {{-- Welcome Message --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="message-circle" style="width:20px;height:20px;"></i> Pesan Sambutan Bot (/start)
            </div>
            <div class="form-group">
                <textarea name="welcome_message" class="form-control" rows="4" placeholder="Pesan yang ditampilkan saat pengguna mengetik /start...">{{ $settings['welcome_message'] ?? '' }}</textarea>
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">Mendukung HTML: &lt;b&gt;bold&lt;/b&gt;, &lt;i&gt;italic&lt;/i&gt;, &lt;code&gt;code&lt;/code&gt;</div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> Simpan Pengaturan
            </button>
        </div>
    </form>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;">
        {{-- Set Webhook --}}
        <form method="POST" action="{{ route('settings.telegram.webhook') }}">
            @csrf
            <button type="submit" class="btn btn-secondary" style="background:rgba(99,102,241,0.15); border-color:rgba(99,102,241,0.3); color:#a5b4fc;">
                <i data-lucide="link"></i> Set Webhook
            </button>
        </form>

        {{-- Test Bot --}}
        <form method="POST" action="{{ route('settings.telegram.test') }}">
            @csrf
            <button type="submit" class="btn btn-secondary" style="background:rgba(16,185,129,0.12); border-color:rgba(16,185,129,0.3); color:#34d399;">
                <i data-lucide="flask-conical"></i> Test Kirim Pesan
            </button>
        </form>
    </div>
</div>
@endsection
