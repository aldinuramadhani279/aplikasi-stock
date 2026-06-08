<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TelegramSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        // Prioritize DB settings over .env
        $this->token = TelegramSetting::getValue('bot_token', config('telegram.bot_token', ''));
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send a message to a specific chat ID.
     */
    public function sendMessage(string $chatId, string $message, array $keyboard = []): void
    {
        if (empty($this->token) || empty($chatId)) {
            Log::warning('TelegramService: bot_token or chat_id is empty.');
            return;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        if (!empty($keyboard)) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $keyboard,
            ]);
        }

        try {
            $response = Http::post("{$this->baseUrl}/sendMessage", $payload);
            if (!$response->successful()) {
                Log::error('Telegram API error in sendMessage: ' . $response->body());
            } else {
                Log::info('Telegram sendMessage success.');
            }
        } catch (\Exception $e) {
            Log::error('TelegramService sendMessage failed: ' . $e->getMessage());
        }
    }

    /**
     * Send out-of-stock alert.
     */
    public function sendStockAlert(Product $product): void
    {
        $chatId = TelegramSetting::getValue('alert_chat_id', config('telegram.alert_chat_id', ''));
        if (empty($chatId)) return;

        $message = "🔴 <b>STOK HABIS!</b>\n\n"
            . "📦 Produk: <b>{$product->name}</b>\n"
            . "🔖 SKU: <code>{$product->sku}</code>\n"
            . "📊 Stok: <b>0 {$product->unit}</b>\n"
            . "⏰ Waktu: " . now()->format('d/m/Y H:i');

        $this->sendMessage($chatId, $message);
    }

    /**
     * Send low stock alert.
     */
    public function sendLowStockAlert(Product $product): void
    {
        $chatId = TelegramSetting::getValue('alert_chat_id', config('telegram.alert_chat_id', ''));
        if (empty($chatId)) return;

        $message = "⚠️ <b>STOK MENIPIS!</b>\n\n"
            . "📦 Produk: <b>{$product->name}</b>\n"
            . "🔖 SKU: <code>{$product->sku}</code>\n"
            . "📊 Stok Saat Ini: <b>{$product->current_stock} {$product->unit}</b>\n"
            . "⚡ Stok Minimum: <b>{$product->minimum_stock} {$product->unit}</b>\n"
            . "⏰ Waktu: " . now()->format('d/m/Y H:i');

        $this->sendMessage($chatId, $message);
    }

    /**
     * Send transaction confirmation.
     */
    public function sendTransactionConfirmation(StockMovement $movement): void
    {
        $isActive = TelegramSetting::getValue('transaction_notification', '1');
        if (!$isActive) return;

        $chatId = TelegramSetting::getValue('default_chat_id', config('telegram.default_chat_id', ''));
        if (empty($chatId)) return;

        $typeEmoji = $movement->type === 'IN' ? '➕' : ($movement->type === 'OUT' ? '➖' : '🔄');
        $typeLabel = $movement->type_label;

        $message = "{$typeEmoji} <b>Transaksi Stok {$typeLabel}</b>\n\n"
            . "📦 Produk: <b>{$movement->product->name}</b>\n"
            . "🔖 SKU: <code>{$movement->product->sku}</code>\n"
            . "📊 Jumlah: <b>{$movement->quantity} {$movement->product->unit}</b>\n"
            . "📉 Stok Sebelum: {$movement->stock_before}\n"
            . "📈 Stok Sesudah: <b>{$movement->stock_after}</b>\n"
            . "👤 Oleh: {$movement->user->name}\n"
            . ($movement->notes ? "📝 Catatan: {$movement->notes}\n" : '')
            . "⏰ Waktu: " . $movement->created_at->format('d/m/Y H:i');

        $this->sendMessage($chatId, $message);
    }

    /**
     * Send daily stock report.
     */
    public function sendDailyReport(): void
    {
        $chatId = TelegramSetting::getValue('report_chat_id', config('telegram.report_chat_id', ''));
        if (empty($chatId)) return;

        $message = $this->formatReport(Carbon::today());
        $this->sendMessage($chatId, $message);
    }

    /**
     * Set webhook with Telegram API.
     */
    public function setWebhook(): bool
    {
        $webhookUrl = TelegramSetting::getValue('webhook_url', config('telegram.webhook_url', ''));

        if (empty($webhookUrl) || empty($this->token)) {
            return false;
        }

        try {
            $response = Http::post("{$this->baseUrl}/setWebhook", [
                'url' => $webhookUrl,
            ]);

            return $response->json('ok', false);
        } catch (\Exception $e) {
            Log::error('TelegramService setWebhook failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test bot connection by sending a test message.
     */
    public function testConnection(string $chatId): bool
    {
        if (empty($this->token) || empty($chatId)) {
            return false;
        }

        try {
            $response = Http::post("{$this->baseUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "✅ <b>Test Koneksi Berhasil!</b>\n\nAplikasi Stok berhasil terhubung dengan Telegram Bot.\n⏰ " . now()->format('d/m/Y H:i:s'),
                'parse_mode' => 'HTML',
            ]);

            return $response->json('ok', false);
        } catch (\Exception $e) {
            Log::error('TelegramService testConnection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format all products stock as string.
     */
    public function formatStockList(): string
    {
        $products = \App\Models\Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return '📦 Tidak ada produk yang terdaftar.';
        }

        $text = "📦 <b>RINGKASAN STOK</b>\n";
        $text .= "📅 " . now()->format('d/m/Y H:i') . "\n";
        $text .= str_repeat('─', 25) . "\n";

        foreach ($products as $product) {
            $statusEmoji = $product->isOutOfStock() ? '🔴' : ($product->isLowStock() ? '⚠️' : '🟢');
            $text .= "{$statusEmoji} <b>{$product->name}</b>\n";
            $text .= "   Stok: {$product->current_stock} {$product->unit}\n";
        }

        return $text;
    }

    /**
     * Format daily report for a given date.
     */
    public function formatReport(Carbon $date): string
    {
        $movements = StockMovement::whereDate('created_at', $date)
            ->with('product')
            ->get();

        $totalIn = $movements->where('type', 'IN')->sum('quantity');
        $totalOut = $movements->where('type', 'OUT')->sum('quantity');
        // BUG-09: Exclude out-of-stock products from lowStockCount to avoid double-counting
        $lowStockCount = \App\Models\Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();
        $outOfStockCount = \App\Models\Product::where('is_active', true)
            ->where('current_stock', 0)
            ->count();

        $text = "📊 <b>LAPORAN STOK HARIAN</b>\n";
        $text .= "📅 " . $date->format('d/m/Y') . "\n";
        $text .= str_repeat('─', 25) . "\n";
        $text .= "➕ Total Masuk: <b>{$totalIn}</b>\n";
        $text .= "➖ Total Keluar: <b>{$totalOut}</b>\n";
        $text .= "🔄 Total Transaksi: <b>{$movements->count()}</b>\n";
        $text .= str_repeat('─', 25) . "\n";
        $text .= "⚠️ Stok Menipis: <b>{$lowStockCount} produk</b>\n";
        $text .= "🔴 Stok Habis: <b>{$outOfStockCount} produk</b>\n";

        return $text;
    }
}
