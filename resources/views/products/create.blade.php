@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('content')
<div style="max-width:700px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="package-plus" style="width:20px;height:20px;"></i> Tambah Produk Baru
            </span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left"></i> Kembali</a>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Nama produk..." required>
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
                    <input type="text" name="sku" value="{{ old('sku') }}" class="form-control" placeholder="ABC-001" required>
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan *</label>
                    <select name="unit" class="form-control" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" {{ old('unit') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Stok Awal *</label>
                    <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" class="form-control" min="0" required>
                </div>
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
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 10) }}" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Maksimum</label>
                    <input type="number" name="maximum_stock" value="{{ old('maximum_stock') }}" class="form-control" min="0" placeholder="Opsional">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Keterangan produk...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Produk</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label class="toggle-wrapper">
                    <label class="toggle">
                        <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="form-label" style="margin:0;">Produk Aktif</span>
                </label>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection
