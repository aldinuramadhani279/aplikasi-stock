<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\TelegramSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@stockapp.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@stockapp.com'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // ── Categories ─────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Makanan & Minuman', 'description' => 'Produk konsumsi sehari-hari'],
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan aksesoris'],
            ['name' => 'Alat Tulis Kantor', 'description' => 'Perlengkapan kantor dan sekolah'],
            ['name' => 'Produk Kebersihan', 'description' => 'Sabun, deterjen, dan produk kebersihan lainnya'],
            ['name' => 'Peralatan Rumah Tangga', 'description' => 'Peralatan dan perlengkapan rumah tangga'],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[] = Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // ── Products ───────────────────────────────────────────────────────
        $products = [
            [
                'category_id' => $createdCategories[0]->id,
                'name' => 'Beras Premium 5kg',
                'sku' => 'MKN-001',
                'unit' => 'kg',
                'current_stock' => 250,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'description' => 'Beras premium kualitas terbaik',
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'name' => 'Minyak Goreng 2L',
                'sku' => 'MKN-002',
                'unit' => 'liter',
                'current_stock' => 8,
                'minimum_stock' => 20,
                'maximum_stock' => 100,
                'description' => 'Minyak goreng kemasan 2 liter',
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'name' => 'Gula Pasir 1kg',
                'sku' => 'MKN-003',
                'unit' => 'kg',
                'current_stock' => 0,
                'minimum_stock' => 15,
                'maximum_stock' => 100,
                'description' => 'Gula pasir kemasan 1 kg',
            ],
            [
                'category_id' => $createdCategories[1]->id,
                'name' => 'Lampu LED 10W',
                'sku' => 'ELK-001',
                'unit' => 'pcs',
                'current_stock' => 45,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'description' => 'Lampu LED hemat energi 10 Watt',
            ],
            [
                'category_id' => $createdCategories[1]->id,
                'name' => 'Kabel USB Type-C 1m',
                'sku' => 'ELK-002',
                'unit' => 'pcs',
                'current_stock' => 3,
                'minimum_stock' => 15,
                'description' => 'Kabel data dan charging USB Type-C',
            ],
            [
                'category_id' => $createdCategories[2]->id,
                'name' => 'Pulpen Ballpoint',
                'sku' => 'ATK-001',
                'unit' => 'lusin',
                'current_stock' => 25,
                'minimum_stock' => 5,
                'description' => 'Pulpen ballpoint hitam 1 lusin',
            ],
            [
                'category_id' => $createdCategories[2]->id,
                'name' => 'Kertas A4 70gr',
                'sku' => 'ATK-002',
                'unit' => 'box',
                'current_stock' => 5,
                'minimum_stock' => 10,
                'description' => 'Kertas HVS A4 70gr isi 500 lembar per rim',
            ],
            [
                'category_id' => $createdCategories[3]->id,
                'name' => 'Sabun Cuci Tangan 500ml',
                'sku' => 'KBR-001',
                'unit' => 'pcs',
                'current_stock' => 60,
                'minimum_stock' => 20,
                'description' => 'Sabun cuci tangan antibakteri 500ml',
            ],
            [
                'category_id' => $createdCategories[3]->id,
                'name' => 'Deterjen Bubuk 1kg',
                'sku' => 'KBR-002',
                'unit' => 'kg',
                'current_stock' => 12,
                'minimum_stock' => 15,
                'description' => 'Deterjen bubuk untuk mesin cuci',
            ],
            [
                'category_id' => $createdCategories[4]->id,
                'name' => 'Sapu Lantai',
                'sku' => 'RMH-001',
                'unit' => 'pcs',
                'current_stock' => 8,
                'minimum_stock' => 5,
                'description' => 'Sapu lantai dengan gagang panjang',
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['sku' => $prod['sku']], $prod);
        }

        // ── Telegram Settings ──────────────────────────────────────────────
        $telegramSettings = [
            ['key' => 'bot_token', 'value' => '', 'description' => 'Token bot dari @BotFather'],
            ['key' => 'webhook_url', 'value' => '', 'description' => 'URL webhook publik (harus HTTPS)'],
            ['key' => 'default_chat_id', 'value' => '', 'description' => 'Chat ID untuk notifikasi umum'],
            ['key' => 'report_chat_id', 'value' => '', 'description' => 'Chat ID untuk laporan harian'],
            ['key' => 'alert_chat_id', 'value' => '', 'description' => 'Chat ID untuk alert stok menipis'],
            ['key' => 'is_active', 'value' => '1', 'description' => 'Aktifkan/nonaktifkan bot (1=aktif, 0=nonaktif)'],
            ['key' => 'daily_report_time', 'value' => '08:00', 'description' => 'Jam kirim laporan harian (HH:MM)'],
            ['key' => 'low_stock_notification', 'value' => '1', 'description' => 'Kirim notifikasi stok menipis'],
            ['key' => 'daily_report_enabled', 'value' => '1', 'description' => 'Aktifkan laporan harian otomatis'],
            ['key' => 'transaction_notification', 'value' => '1', 'description' => 'Kirim konfirmasi setiap transaksi'],
            ['key' => 'welcome_message', 'value' => "Halo! Selamat datang di <b>Bot Manajemen Stok</b> 📦\n\nGunakan tombol di bawah atau ketik perintah untuk mulai.", 'description' => 'Pesan sambutan bot'],
        ];

        foreach ($telegramSettings as $setting) {
            \App\Models\TelegramSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
