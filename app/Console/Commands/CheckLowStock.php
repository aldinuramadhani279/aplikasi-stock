<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\StockAlert;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check-low';
    protected $description = 'Cek stok menipis dan kirim notifikasi Telegram';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Mengecek stok menipis...');

        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();

        foreach ($lowStockProducts as $product) {
            $existingAlert = StockAlert::where('product_id', $product->id)
                ->where('is_resolved', false)
                ->whereNull('notified_at')
                ->first();

            if (!$existingAlert) {
                continue; // Already notified
            }

            if ($product->isOutOfStock()) {
                $telegram->sendStockAlert($product);
            } else {
                $telegram->sendLowStockAlert($product);
            }

            $existingAlert->update(['notified_at' => now()]);
        }

        $this->info("✅ Selesai. {$lowStockProducts->count()} produk diperiksa.");
        return Command::SUCCESS;
    }
}
