<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TelegramSetting;
use App\Services\StockService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramService $telegram,
        protected StockService $stockService,
    ) {
    }

    public function handle(Request $request)
    {
        $update = $request->all();
        Log::info('Telegram Webhook received', $update);

        // Check if bot is active
        $isActive = TelegramSetting::getValue('is_active', '1');
        if (!$isActive) {
            return response()->json(['ok' => true]);
        }

        // Handle regular messages (commands)
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        // Handle callback queries (inline keyboard)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }

        return response()->json(['ok' => true]);
    }

    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        if (!str_starts_with($text, '/')) {
            return;
        }

        $parts = explode(' ', $text);
        $command = strtolower($parts[0]);

        match (true) {
            str_starts_with($command, '/start') => $this->cmdStart($chatId, $message),
            str_starts_with($command, '/stok') => $this->cmdStok($chatId),
            str_starts_with($command, '/cek') => $this->cmdCek($chatId, $parts),
            str_starts_with($command, '/masuk') => $this->cmdMasuk($chatId, $parts),
            str_starts_with($command, '/keluar') => $this->cmdKeluar($chatId, $parts),
            str_starts_with($command, '/laporan') => $this->cmdLaporan($chatId),
            str_starts_with($command, '/menipis') => $this->cmdMenipis($chatId),
            str_starts_with($command, '/bantuan') => $this->cmdBantuan($chatId),
            default => $this->telegram->sendMessage($chatId, "❓ Perintah tidak dikenal. Ketik /bantuan untuk melihat daftar perintah."),
        };
    }

    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'] ?? '';

        if (str_starts_with($data, 'stok')) {
            if ($data === 'stok') {
                $this->cmdStokCategoryMenu($chatId);
            } else {
                $catId = str_replace('stok_cat_', '', $data);
                $this->cmdStokForCategory($chatId, $catId);
            }
            return;
        }

        match ($data) {
            'laporan' => $this->cmdLaporan($chatId),
            'menipis' => $this->cmdMenipis($chatId),
            'bantuan' => $this->cmdBantuan($chatId),
            default => null,
        };
    }

    protected function cmdStart(string $chatId, array $message): void
    {
        $welcomeMsg = TelegramSetting::getValue('welcome_message');
        if (empty(trim($welcomeMsg))) {
            $welcomeMsg = "Halo! Selamat datang di <b>Bot Manajemen Stok</b> 📦\n\nSilakan pilih menu di bawah ini:";
        }

        $keyboard = [
            [
                ['text' => '📦 Cek Stok per Kategori', 'callback_data' => 'stok'],
            ],
            [
                ['text' => '📊 Laporan Hari Ini', 'callback_data' => 'laporan'],
                ['text' => '⚠️ Stok Menipis', 'callback_data' => 'menipis'],
            ],
            [
                ['text' => '❓ Bantuan', 'callback_data' => 'bantuan'],
            ]
        ];

        $this->telegram->sendMessage($chatId, $welcomeMsg, $keyboard);
    }

    protected function cmdStok(string $chatId): void
    {
        $this->cmdStokCategoryMenu($chatId);
    }

    protected function cmdStokCategoryMenu(string $chatId): void
    {
        $categories = \App\Models\Category::all();
        
        if ($categories->isEmpty()) {
            $this->telegram->sendMessage($chatId, "❌ Belum ada kategori yang terdaftar.");
            return;
        }

        $keyboard = [];
        // Susun tombol 2 per baris
        $row = [];
        foreach ($categories as $cat) {
            $row[] = ['text' => '📂 ' . $cat->name, 'callback_data' => 'stok_cat_' . $cat->id];
            if (count($row) === 2) {
                $keyboard[] = $row;
                $row = [];
            }
        }
        if (!empty($row)) {
            $keyboard[] = $row; // Tambahkan sisa tombol yang ganjil
        }

        $this->telegram->sendMessage($chatId, "Pilih kategori untuk melihat stok:", $keyboard);
    }

    protected function cmdStokForCategory(string $chatId, $categoryId): void
    {
        $category = \App\Models\Category::find($categoryId);
        if (!$category) {
            $this->telegram->sendMessage($chatId, "❌ Kategori tidak ditemukan.");
            return;
        }

        $products = Product::where('category_id', $category->id)->where('is_active', true)->orderBy('name')->get();

        if ($products->isEmpty()) {
            $this->telegram->sendMessage($chatId, "📦 Kategori <b>{$category->name}</b> kosong.");
            return;
        }

        $text = "📦 <b>Stok Kategori: {$category->name}</b>\n\n";
        foreach ($products as $p) {
            $statusEmoji = $p->isOutOfStock() ? '🔴' : ($p->isLowStock() ? '⚠️' : '🟢');
            $text .= "{$statusEmoji} <b>{$p->name}</b>\n";
            $text .= "   Stok: <b>{$p->current_stock} {$p->unit}</b> (Min: {$p->minimum_stock})\n";
        }

        // Tambahkan tombol kembali ke kategori
        $keyboard = [
            [
                ['text' => '🔙 Kembali ke Daftar Kategori', 'callback_data' => 'stok']
            ]
        ];

        $this->telegram->sendMessage($chatId, $text, $keyboard);
    }

    protected function cmdCek(string $chatId, array $parts): void
    {
        if (count($parts) < 2) {
            $this->telegram->sendMessage($chatId, "❌ Format: /cek [nama_produk]\nContoh: /cek Beras");
            return;
        }

        $keyword = implode(' ', array_slice($parts, 1));
        $products = Product::where('name', 'like', "%{$keyword}%")
            ->orWhere('sku', 'like', "%{$keyword}%")
            ->get();

        if ($products->isEmpty()) {
            $this->telegram->sendMessage($chatId, "🔍 Produk '<b>{$keyword}</b>' tidak ditemukan.");
            return;
        }

        $text = "🔍 <b>Hasil Pencarian: {$keyword}</b>\n\n";
        foreach ($products as $p) {
            $statusEmoji = $p->isOutOfStock() ? '🔴' : ($p->isLowStock() ? '⚠️' : '🟢');
            $text .= "{$statusEmoji} <b>{$p->name}</b>\n";
            $text .= "   SKU: <code>{$p->sku}</code>\n";
            $text .= "   Stok: <b>{$p->current_stock} {$p->unit}</b>\n";
            $text .= "   Min: {$p->minimum_stock} {$p->unit}\n\n";
        }

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function cmdMasuk(string $chatId, array $parts): void
    {
        if (count($parts) < 3) {
            $this->telegram->sendMessage($chatId, "❌ Format: /masuk [SKU] [qty] [catatan]\nContoh: /masuk ABC-001 50 Pembelian rutin");
            return;
        }

        $sku = strtoupper($parts[1]);
        $qty = intval($parts[2]);
        $notes = count($parts) > 3 ? implode(' ', array_slice($parts, 3)) : 'Via Telegram Bot';

        $product = Product::where('sku', $sku)->where('is_active', true)->first();

        if (!$product) {
            $this->telegram->sendMessage($chatId, "❌ Produk dengan SKU <code>{$sku}</code> tidak ditemukan.");
            return;
        }

        if ($qty <= 0) {
            $this->telegram->sendMessage($chatId, "❌ Jumlah harus lebih dari 0.");
            return;
        }

        try {
            $this->stockService->addStock($product, $qty, $notes);
            $product->refresh();
            $this->telegram->sendMessage($chatId,
                "✅ <b>Stok Masuk Berhasil!</b>\n\n"
                . "📦 Produk: {$product->name}\n"
                . "➕ Jumlah Masuk: {$qty} {$product->unit}\n"
                . "📊 Stok Sekarang: <b>{$product->current_stock} {$product->unit}</b>"
            );
        } catch (\Exception $e) {
            $this->telegram->sendMessage($chatId, "❌ Gagal: " . $e->getMessage());
        }
    }

    protected function cmdKeluar(string $chatId, array $parts): void
    {
        if (count($parts) < 3) {
            $this->telegram->sendMessage($chatId, "❌ Format: /keluar [SKU] [qty] [catatan]\nContoh: /keluar ABC-001 10 Penjualan");
            return;
        }

        $sku = strtoupper($parts[1]);
        $qty = intval($parts[2]);
        $notes = count($parts) > 3 ? implode(' ', array_slice($parts, 3)) : 'Via Telegram Bot';

        $product = Product::where('sku', $sku)->where('is_active', true)->first();

        if (!$product) {
            $this->telegram->sendMessage($chatId, "❌ Produk dengan SKU <code>{$sku}</code> tidak ditemukan atau tidak aktif.");
            return;
        }

        if ($qty <= 0) {
            $this->telegram->sendMessage($chatId, "❌ Jumlah harus lebih dari 0.");
            return;
        }

        try {
            $this->stockService->removeStock($product, $qty, $notes);
            $product->refresh();
            $this->telegram->sendMessage($chatId,
                "✅ <b>Stok Keluar Berhasil!</b>\n\n"
                . "📦 Produk: {$product->name}\n"
                . "➖ Jumlah Keluar: {$qty} {$product->unit}\n"
                . "📊 Stok Sekarang: <b>{$product->current_stock} {$product->unit}</b>"
            );
        } catch (\Exception $e) {
            $this->telegram->sendMessage($chatId, "❌ " . $e->getMessage());
        }
    }

    protected function cmdLaporan(string $chatId): void
    {
        $text = $this->telegram->formatReport(\Carbon\Carbon::today());
        $this->telegram->sendMessage($chatId, $text);
    }

    protected function cmdMenipis(string $chatId): void
    {
        $products = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock')
            ->get();

        if ($products->isEmpty()) {
            $this->telegram->sendMessage($chatId, "✅ Semua stok dalam kondisi aman!");
            return;
        }

        $text = "⚠️ <b>DAFTAR STOK MENIPIS/HABIS</b>\n\n";
        foreach ($products as $p) {
            $emoji = $p->isOutOfStock() ? '🔴' : '⚠️';
            $text .= "{$emoji} <b>{$p->name}</b>\n";
            $text .= "   SKU: <code>{$p->sku}</code>\n";
            $text .= "   Stok: {$p->current_stock}/{$p->minimum_stock} {$p->unit}\n\n";
        }

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function cmdBantuan(string $chatId): void
    {
        $text = "📖 <b>PANDUAN PERINTAH BOT</b>\n\n"
            . "📦 <b>/stok</b> — Lihat semua stok\n"
            . "🔍 <b>/cek [nama]</b> — Cari produk\n"
            . "➕ <b>/masuk [SKU] [qty]</b> — Input barang masuk\n"
            . "➖ <b>/keluar [SKU] [qty]</b> — Input barang keluar\n"
            . "📊 <b>/laporan</b> — Laporan hari ini\n"
            . "⚠️ <b>/menipis</b> — Daftar stok menipis\n"
            . "❓ <b>/bantuan</b> — Tampilkan panduan ini\n\n"
            . "💡 <i>Tip: Format SKU harus sama persis dengan yang terdaftar di sistem.</i>";

        $this->telegram->sendMessage($chatId, $text);
    }
}
