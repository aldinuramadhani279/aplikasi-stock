<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'low'      => $query->whereColumn('current_stock', '<=', 'minimum_stock')->where('current_stock', '>', 0),
                'out'      => $query->where('current_stock', 0),
                'active'   => $query->where('is_active', true),
                'inactive' => $query->where('is_active', false),
                default    => null,
            };
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = ['pcs', 'kg', 'liter', 'box', 'lusin', 'meter', 'set', 'lembar'];
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products',
            'unit'          => 'required|in:pcs,kg,liter,box,lusin,meter,set,lembar',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            // BUG-08: maximum_stock must be >= minimum_stock if provided
            'maximum_stock' => 'nullable|integer|min:0|gte:minimum_stock',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'maximum_stock.gte' => 'Stok maksimum harus lebih besar atau sama dengan stok minimum.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // BUG-07: consistent is_active handling (checkbox = not sent when unchecked)
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $movements = $product->stockMovements()->with('user')->latest()->limit(20)->get();
        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = ['pcs', 'kg', 'liter', 'box', 'lusin', 'meter', 'set', 'lembar'];
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products,sku,' . $product->id,
            'unit'          => 'required|in:pcs,kg,liter,box,lusin,meter,set,lembar',
            'minimum_stock' => 'required|integer|min:0',
            // BUG-08: maximum_stock must be >= minimum_stock if provided
            'maximum_stock' => 'nullable|integer|min:0|gte:minimum_stock',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'maximum_stock.gte' => 'Stok maksimum harus lebih besar atau sama dengan stok minimum.',
        ]);

        if ($request->hasFile('image')) {
            // BUG-05: Delete old image from storage before uploading new one
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // BUG-07: consistent is_active handling
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // BUG-12: Prevent deleting product that still has active stock
        if ($product->current_stock > 0) {
            return back()->with('error', "Produk <strong>{$product->name}</strong> tidak dapat dihapus karena masih memiliki stok ({$product->current_stock} {$product->unit}). Keluarkan atau sesuaikan stok terlebih dahulu.");
        }

        // BUG-12: Prevent deleting product with transaction history (would orphan records)
        if ($product->stockMovements()->exists()) {
            return back()->with('error', "Produk <strong>{$product->name}</strong> tidak dapat dihapus karena memiliki riwayat transaksi. Nonaktifkan produk jika tidak ingin ditampilkan.");
        }

        // BUG-04: Delete product image from storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
