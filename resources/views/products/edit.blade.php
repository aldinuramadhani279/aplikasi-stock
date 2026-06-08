@extends('layouts.app')
@section('title', 'Edit Produk')

@section('content')
<div style="max-width:700px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="pencil" style="width:20px;height:20px;"></i> Edit Produk
            </span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left"></i> Kembali</a>
        </div>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        SKU *
                        <div class="hint-wrapper">
                            <span class="hint-toggle"><i data-lucide="info"></i></span>
                            <div class="hint-popover">
                                <h4>Apa itu SKU?</h4>
                                <p style="margin:0;">SKU (Stock Keeping Unit) adalah kode unik untuk setiap produk agar mudah dilacak. Contoh: <strong>BRG-001</strong> atau <strong>Kopi-XL</strong>.</p>
                            </div>
                        </div>
                    </label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control" required>
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan *</label>
                    <select name="unit" class="form-control" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" {{ old('unit', $product->unit) === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="background:rgba(99,102,241,0.08); padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="info" style="width:18px;height:18px;color:#818cf8;"></i>
                <span>Stok saat ini: <strong>{{ $product->current_stock }} {{ $product->unit }}</strong>. Gunakan menu <a href="{{ route('stock.adjust') }}" style="color:#a5b4fc;text-decoration:underline;">Penyesuaian Stok</a> untuk mengubah jumlah stok.</span>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">
                        Stok Minimum *
                        <div class="hint-wrapper">
                            <span class="hint-toggle"><i data-lucide="info"></i></span>
                            <div class="hint-popover">
                                <h4>Fungsi Stok Minimum:</h4>
                                <p style="margin:0;">Batas peringatan (alert). Jika stok menyentuh atau kurang dari angka ini, produk akan masuk daftar "Stok Menipis".</p>
                            </div>
                        </div>
                    </label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Maksimum</label>
                    <input type="number" name="maximum_stock" value="{{ old('maximum_stock', $product->maximum_stock) }}" class="form-control" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Produk</label>
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" style="width:80px; height:80px; border-radius:8px; object-fit:cover; margin-bottom:8px; display:block;">
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label class="toggle-wrapper">
                    <label class="toggle">
                        <input type="checkbox" name="is_active" {{ $product->is_active ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="form-label" style="margin:0;">Produk Aktif</span>
                </label>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
