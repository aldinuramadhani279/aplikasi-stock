<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(protected TelegramService $telegram)
    {
    }

    /**
     * Add stock (type IN).
     */
    public function addStock(Product $product, int $quantity, string $notes = '', string $reference = ''): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $notes, $reference) {
            // Lock the row to prevent race conditions
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $stockBefore = $product->current_stock;
            $product->increment('current_stock', $quantity);
            $product->refresh();

            $movement = StockMovement::create([
                'product_id'       => $product->id,
                'user_id'          => Auth::id(),
                'type'             => 'IN',
                'quantity'         => $quantity,
                'stock_before'     => $stockBefore,
                'stock_after'      => $product->current_stock,
                'notes'            => $notes,
                'reference_number' => $reference,
            ]);

            // Resolve any existing alerts if stock is now above minimum
            if ($product->current_stock > $product->minimum_stock) {
                StockAlert::where('product_id', $product->id)
                    ->where('is_resolved', false)
                    ->update(['is_resolved' => true, 'resolved_at' => now()]);
            }

            // Send Telegram notification
            $this->telegram->sendTransactionConfirmation($movement->load('product', 'user'));

            return $movement;
        });
    }

    /**
     * Remove stock (type OUT).
     */
    public function removeStock(Product $product, int $quantity, string $notes = '', string $reference = ''): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $notes, $reference) {
            // Lock the row to prevent race conditions
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($product->current_stock < $quantity) {
                throw new \Exception("Stok tidak mencukupi. Stok tersedia: {$product->current_stock} {$product->unit}");
            }

            $stockBefore = $product->current_stock;
            $product->decrement('current_stock', $quantity);
            $product->refresh();

            $movement = StockMovement::create([
                'product_id'       => $product->id,
                'user_id'          => Auth::id(),
                'type'             => 'OUT',
                'quantity'         => $quantity,
                'stock_before'     => $stockBefore,
                'stock_after'      => $product->current_stock,
                'notes'            => $notes,
                'reference_number' => $reference,
            ]);

            // Check alerts
            $this->checkAndCreateAlert($product);

            // Send Telegram notification
            $this->telegram->sendTransactionConfirmation($movement->load('product', 'user'));

            return $movement;
        });
    }

    /**
     * Adjust stock (type ADJUSTMENT).
     */
    public function adjustStock(Product $product, int $newStock, string $notes = ''): StockMovement
    {
        return DB::transaction(function () use ($product, $newStock, $notes) {
            // Lock the row to prevent race conditions
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $stockBefore = $product->current_stock;
            $product->update(['current_stock' => $newStock]);
            $product->refresh();

            $movement = StockMovement::create([
                'product_id'       => $product->id,
                'user_id'          => Auth::id(),
                'type'             => 'ADJUSTMENT',
                'quantity'         => abs($newStock - $stockBefore),
                'stock_before'     => $stockBefore,
                'stock_after'      => $product->current_stock,
                'notes'            => $notes,
                'reference_number' => 'ADJ-' . now()->format('YmdHis'),
            ]);

            $this->checkAndCreateAlert($product);

            return $movement;
        });
    }

    /**
     * Check stock and create alert if needed.
     */
    protected function checkAndCreateAlert(Product $product): void
    {
        $existingAlert = StockAlert::where('product_id', $product->id)
            ->where('is_resolved', false)
            ->first();

        if ($product->isOutOfStock()) {
            if (!$existingAlert || $existingAlert->type !== 'OUT_OF_STOCK') {
                StockAlert::updateOrCreate(
                    ['product_id' => $product->id, 'is_resolved' => false],
                    ['type' => 'OUT_OF_STOCK', 'notified_at' => now()]
                );
                $this->telegram->sendStockAlert($product);
            }
        } elseif ($product->isLowStock()) {
            if (!$existingAlert) {
                StockAlert::create([
                    'product_id' => $product->id,
                    'type' => 'LOW_STOCK',
                    'notified_at' => now(),
                ]);
                $this->telegram->sendLowStockAlert($product);
            }
        }
    }
}
