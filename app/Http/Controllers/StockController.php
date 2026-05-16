<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(protected StockService $stockService)
    {
    }

    // ─────────── STOCK IN ───────────
    public function inForm()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.in', compact('products'));
    }

    public function inStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ], [
            'product_id.required' => 'Pilih produk terlebih dahulu.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            $this->stockService->addStock(
                $product,
                $validated['quantity'],
                $validated['notes'] ?? '',
                $validated['reference_number'] ?? ''
            );

            return redirect()->route('stock.in')
                ->with('success', "Stok masuk {$validated['quantity']} {$product->unit} untuk <strong>{$product->name}</strong> berhasil dicatat.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ─────────── STOCK OUT ───────────
    public function outForm()
    {
        $products = Product::where('is_active', true)->where('current_stock', '>', 0)->orderBy('name')->get();
        return view('stock.out', compact('products'));
    }

    public function outStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            $this->stockService->removeStock(
                $product,
                $validated['quantity'],
                $validated['notes'] ?? '',
                $validated['reference_number'] ?? ''
            );

            return redirect()->route('stock.out')
                ->with('success', "Stok keluar {$validated['quantity']} {$product->unit} untuk <strong>{$product->name}</strong> berhasil dicatat.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ─────────── MOVEMENTS ───────────
    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->latest()->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('stock.movements', compact('movements', 'products'));
    }

    // ─────────── ADJUSTMENT ───────────
    public function adjustForm()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.adjust', compact('products'));
    }

    public function adjustStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_stock' => 'required|integer|min:0',
            'notes' => 'required|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            $this->stockService->adjustStock(
                $product,
                $validated['new_stock'],
                $validated['notes']
            );

            return redirect()->route('stock.movements')
                ->with('success', "Penyesuaian stok produk <strong>{$product->name}</strong> berhasil.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Get product info for AJAX.
     */
    public function getProduct(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit' => $product->unit,
            'current_stock' => $product->current_stock,
            'minimum_stock' => $product->minimum_stock,
        ]);
    }
}
