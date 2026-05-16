# PROMPT: Bangun Aplikasi Manajemen Stok & Gudang dengan Laravel + Telegram Bot

## 🎯 OVERVIEW PROYEK

Bangun sebuah aplikasi web **manajemen stok dan gudang** berbasis **Laravel 11** yang terintegrasi penuh dengan **Telegram Bot** sebagai channel notifikasi, perintah cepat, dan laporan otomatis. Aplikasi ini ditujukan untuk **UMKM dan perusahaan kecil** yang baru memulai dan membutuhkan sistem stok yang simpel, terjangkau, dan mudah digunakan tanpa perlu install aplikasi tambahan (cukup Telegram).

---

## 🏗️ TECH STACK

| Layer | Teknologi |
|---|---|
| Backend Framework | Laravel 11 |
| Database | MySQL 8.x |
| Frontend | Blade + TailwindCSS + Alpine.js |
| Telegram Integration | `irazasyed/telegram-bot-sdk` |
| Queue/Scheduler | Laravel Queue + Laravel Scheduler |
| Authentication | Laravel Breeze (session-based) |
| Storage | Laravel Filesystem (local) |
| Server | VPS Linux / Shared Hosting (PHP 8.2+) |

---

## 📁 STRUKTUR DIREKTORI YANG DIHARAPKAN

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── StockController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── ReportController.php
│   │   ├── TelegramWebhookController.php
│   │   └── SettingController.php
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── StockMovement.php
│   ├── StockAlert.php
│   └── TelegramSetting.php
├── Services/
│   ├── TelegramService.php
│   ├── StockService.php
│   └── ReportService.php
├── Jobs/
│   ├── SendTelegramNotification.php
│   └── SendDailyStockReport.php
└── Console/
    └── Commands/
        ├── SendDailyReport.php
        └── CheckLowStock.php

config/
└── telegram.php  ← FILE KONFIGURASI UTAMA TELEGRAM

routes/
├── web.php
├── api.php
└── telegram.php  ← route khusus webhook telegram
```

---

## 🗄️ DATABASE SCHEMA

### Tabel: `users`
```sql
id, name, email, password, role (admin|staff), telegram_chat_id, created_at, updated_at
```

### Tabel: `categories`
```sql
id, name, description, created_at, updated_at
```

### Tabel: `products`
```sql
id, category_id, name, sku, unit (pcs|kg|liter|box|dll),
current_stock (integer), minimum_stock (integer, untuk alert),
maximum_stock (integer, opsional),
description, image, is_active (boolean),
created_at, updated_at
```

### Tabel: `stock_movements`
```sql
id, product_id, user_id, type (IN|OUT|ADJUSTMENT),
quantity, stock_before, stock_after,
notes, reference_number,
created_at, updated_at
```

### Tabel: `stock_alerts`
```sql
id, product_id, type (LOW_STOCK|OUT_OF_STOCK),
is_resolved (boolean), notified_at, resolved_at,
created_at, updated_at
```

### Tabel: `telegram_settings`
```sql
id, key, value, description, created_at, updated_at
```
**Isi default tabel telegram_settings:**
- `bot_token` → Token bot dari @BotFather
- `webhook_url` → URL webhook publik aplikasi
- `default_chat_id` → Chat ID grup/personal default untuk notifikasi
- `report_chat_id` → Chat ID khusus laporan harian
- `alert_chat_id` → Chat ID khusus alert stok menipis
- `is_active` → 1/0, aktifkan/matikan bot
- `daily_report_time` → Format HH:MM, jam kirim laporan harian (default: 08:00)
- `low_stock_notification` → 1/0
- `welcome_message` → Pesan sambutan bot

---

## ⚙️ KONFIGURASI TELEGRAM — DETAIL LENGKAP

### File: `config/telegram.php`

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    | Token dari @BotFather di Telegram. Wajib diisi di .env
    | TELEGRAM_BOT_TOKEN=your_bot_token_here
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    | URL publik yang akan menerima update dari Telegram.
    | Harus HTTPS. Set di .env:
    | TELEGRAM_WEBHOOK_URL=https://yourdomain.com/telegram/webhook
    */
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Chat ID
    |--------------------------------------------------------------------------
    | Chat ID default untuk notifikasi umum.
    | Bisa berupa personal chat ID atau group chat ID (angka negatif untuk grup)
    | TELEGRAM_DEFAULT_CHAT_ID=123456789
    */
    'default_chat_id' => env('TELEGRAM_DEFAULT_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Report Chat ID
    |--------------------------------------------------------------------------
    | Chat ID khusus untuk laporan harian otomatis.
    | TELEGRAM_REPORT_CHAT_ID=123456789
    */
    'report_chat_id' => env('TELEGRAM_REPORT_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Alert Chat ID
    |--------------------------------------------------------------------------
    | Chat ID khusus untuk notifikasi stok menipis/habis.
    | TELEGRAM_ALERT_CHAT_ID=123456789
    */
    'alert_chat_id' => env('TELEGRAM_ALERT_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Async Request
    |--------------------------------------------------------------------------
    | Kirim request ke Telegram secara async menggunakan Queue
    */
    'async_requests' => env('TELEGRAM_ASYNC', true),

    /*
    |--------------------------------------------------------------------------
    | Parse Mode Default
    |--------------------------------------------------------------------------
    | Mode parsing teks: HTML atau Markdown
    */
    'parse_mode' => 'HTML',
];
```

### File: `.env` (tambahkan variabel ini)

```env
# ============================================
# TELEGRAM BOT CONFIGURATION
# ============================================
TELEGRAM_BOT_TOKEN=               # Token dari @BotFather
TELEGRAM_WEBHOOK_URL=             # https://yourdomain.com/telegram/webhook
TELEGRAM_DEFAULT_CHAT_ID=        # Chat ID notifikasi umum
TELEGRAM_REPORT_CHAT_ID=         # Chat ID laporan harian
TELEGRAM_ALERT_CHAT_ID=          # Chat ID alert stok menipis
TELEGRAM_ASYNC=true              # Gunakan queue untuk kirim pesan
```

---

## 🤖 FITUR TELEGRAM BOT

### Commands yang harus tersedia:

| Command | Fungsi |
|---|---|
| `/start` | Sambutan + tampilkan menu utama dengan inline keyboard |
| `/stok` | Tampilkan ringkasan stok semua produk |
| `/cek [nama_produk]` | Cek stok produk spesifik |
| `/masuk [sku] [qty] [catatan]` | Input barang masuk |
| `/keluar [sku] [qty] [catatan]` | Input barang keluar |
| `/laporan` | Laporan stok hari ini |
| `/menipis` | Daftar produk yang stoknya di bawah minimum |
| `/bantuan` | Tampilkan panduan lengkap commands |

### Inline Keyboard Buttons (setelah /start):

```
[ 📦 Cek Stok ]    [ 📊 Laporan Hari Ini ]
[ ⚠️ Stok Menipis ] [ ➕ Barang Masuk     ]
[ ➖ Barang Keluar ] [ 🔗 Buka Dashboard   ]
```

### Notifikasi Otomatis:

1. **Alert Stok Menipis** — dikirim ke `alert_chat_id` saat stok produk ≤ `minimum_stock`
2. **Alert Stok Habis** — dikirim saat stok = 0
3. **Laporan Harian Pagi** — dikirim ke `report_chat_id` sesuai `daily_report_time`
4. **Konfirmasi Transaksi** — dikirim ke `default_chat_id` setiap ada input masuk/keluar

---

## 🌐 FITUR WEB DASHBOARD

### Halaman yang harus dibangun:

#### 1. Dashboard (/)
- Kartu ringkasan: Total Produk, Total Stok, Produk Menipis, Transaksi Hari Ini
- Grafik pergerakan stok 7 hari terakhir
- Tabel produk stok menipis (highlight merah)
- Tabel transaksi terbaru

#### 2. Produk (/products)
- CRUD produk (nama, SKU, kategori, satuan, stok minimum, stok maksimum)
- Filter & search
- Import produk via Excel/CSV
- Export ke Excel/PDF

#### 3. Kategori (/categories)
- CRUD kategori produk

#### 4. Stok Masuk (/stock/in)
- Form input barang masuk
- Pilih produk (searchable dropdown)
- Input qty, catatan, nomor referensi
- Setelah submit → otomatis kirim notif ke Telegram

#### 5. Stok Keluar (/stock/out)
- Form input barang keluar
- Validasi tidak boleh melebihi stok tersedia

#### 6. Riwayat Pergerakan (/stock/movements)
- Tabel semua transaksi IN/OUT/ADJUSTMENT
- Filter by: produk, kategori, tanggal, tipe, user
- Export ke Excel/PDF

#### 7. Laporan (/reports)
- Laporan stok saat ini
- Laporan pergerakan per periode
- Laporan produk menipis
- Semua bisa di-export dan dikirim manual ke Telegram

#### 8. Pengaturan Telegram (/settings/telegram) ← HALAMAN PENTING
Halaman konfigurasi Telegram yang user-friendly dengan form:

```
┌─────────────────────────────────────────────┐
│  ⚙️  KONFIGURASI TELEGRAM BOT                │
├─────────────────────────────────────────────┤
│  Bot Token         [_______________________] │
│  Webhook URL       [_______________________] │
│  Default Chat ID   [_______________________] │
│  Report Chat ID    [_______________________] │
│  Alert Chat ID    [_______________________]  │
│  Jam Laporan Harian [08:00]                  │
│                                              │
│  Notifikasi Stok Menipis  [ON/OFF toggle]    │
│  Laporan Harian Otomatis  [ON/OFF toggle]    │
│  Konfirmasi Transaksi     [ON/OFF toggle]    │
│                                              │
│  [💾 Simpan] [🔗 Set Webhook] [🧪 Test Bot]  │
└─────────────────────────────────────────────┘
```

Tombol **Set Webhook** → otomatis panggil Telegram API untuk register webhook URL.
Tombol **Test Bot** → kirim pesan test ke default_chat_id untuk verifikasi koneksi.

#### 9. Pengaturan Umum (/settings)
- Nama perusahaan
- Logo
- Manajemen user & role
- Zona waktu

---

## 👥 ROLE & PERMISSION

| Role | Akses |
|---|---|
| `admin` | Semua fitur termasuk settings & hapus data |
| `staff` | Input stok masuk/keluar, lihat dashboard & laporan |

---

## 🔔 CARA KERJA NOTIFIKASI (DETAIL TEKNIS)

### TelegramService.php — Method yang wajib ada:

```php
class TelegramService {
    public function sendMessage(string $chatId, string $message, array $keyboard = []): void
    public function sendStockAlert(Product $product): void
    public function sendLowStockAlert(Product $product): void
    public function sendTransactionConfirmation(StockMovement $movement): void
    public function sendDailyReport(): void
    public function setWebhook(): bool
    public function testConnection(string $chatId): bool
    public function formatStockList(): string
    public function formatReport(Carbon $date): string
}
```

### Webhook Flow:

```
POST /telegram/webhook
→ TelegramWebhookController@handle
→ Parse command/callback_query
→ Route ke handler yang sesuai
→ Reply ke user via Telegram API
```

---

## 📅 SCHEDULER (CRONJOB)

Tambahkan di `app/Console/Kernel.php` atau `routes/console.php` (Laravel 11):

```php
// Laporan harian — waktu sesuai setting di database
Schedule::job(new SendDailyStockReport)->dailyAt(
    TelegramSetting::getValue('daily_report_time', '08:00')
);

// Cek stok menipis setiap jam
Schedule::job(new CheckLowStock)->hourly();
```

---

## 📦 PACKAGE YANG DIBUTUHKAN

```bash
composer require irazasyed/telegram-bot-sdk
composer require maatwebsite/excel          # Import/export Excel
composer require barryvdh/laravel-dompdf    # Export PDF
composer require spatie/laravel-permission  # Role & permission
```

---

## 🚀 LANGKAH SETUP AWAL (untuk developer yang menerima proyek ini)

```bash
# 1. Clone & install
git clone [repo]
cd [project]
composer install
npm install && npm run build

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi .env
# - DB_* (database)
# - TELEGRAM_BOT_TOKEN
# - TELEGRAM_WEBHOOK_URL
# - TELEGRAM_DEFAULT_CHAT_ID

# 4. Database
php artisan migrate
php artisan db:seed  # seed kategori & user admin default

# 5. Register webhook Telegram
php artisan telegram:set-webhook
# ATAU lewat UI: Settings > Telegram > Klik "Set Webhook"

# 6. Queue worker (untuk notifikasi async)
php artisan queue:work

# 7. Scheduler (tambahkan ke crontab)
# * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔑 ARTISAN COMMANDS CUSTOM

```bash
php artisan telegram:set-webhook      # Register webhook URL ke Telegram
php artisan telegram:send-report      # Kirim laporan manual
php artisan telegram:test [chat_id]   # Test koneksi bot
php artisan stock:check-low           # Cek & kirim alert stok menipis
```

---

## ✅ ACCEPTANCE CRITERIA / DEFINITION OF DONE

- [ ] Login/logout berjalan
- [ ] CRUD produk & kategori berjalan
- [ ] Input stok masuk & keluar berjalan + update stok realtime
- [ ] Riwayat pergerakan stok tercatat lengkap
- [ ] Dashboard menampilkan data akurat
- [ ] Telegram bot merespons semua commands
- [ ] Notifikasi stok menipis terkirim otomatis
- [ ] Laporan harian terkirim otomatis sesuai jadwal
- [ ] Halaman settings Telegram bisa simpan config & test koneksi
- [ ] Export laporan ke Excel & PDF berjalan
- [ ] Role admin & staff berfungsi dengan benar
- [ ] Semua konfigurasi Telegram bisa diubah dari UI (tidak perlu edit .env manual)

---

## 📝 CATATAN PENTING UNTUK AI/DEVELOPER

1. **Semua konfigurasi Telegram harus bisa diubah dari UI** (halaman `/settings/telegram`), tidak hanya dari `.env`. Data disimpan di tabel `telegram_settings` di database.

2. **`.env` hanya untuk konfigurasi awal/default.** Setelah app berjalan, settings diambil dari database.

3. **Webhook harus HTTPS** — gunakan ngrok untuk development lokal.

4. **Gunakan Laravel Queue** untuk semua pengiriman Telegram agar tidak blocking request.

5. **Telegram Chat ID** bisa berbeda untuk tiap jenis notifikasi (umum, laporan, alert) — ini disengaja agar perusahaan bisa pisahkan grup Telegram.

6. **Bahasa UI: Bahasa Indonesia** untuk semua label, tombol, dan pesan.

7. **Seeding default:** Buat 1 user admin (admin@stockapp.com / password), 5 kategori contoh, 10 produk contoh dengan berbagai kondisi stok.

8. **Mobile-friendly:** Dashboard harus responsive karena staff kemungkinan akses dari HP.

---

*Prompt ini dibuat sebagai handover document lengkap untuk membangun aplikasi stok dengan Laravel + Telegram dari nol.*
