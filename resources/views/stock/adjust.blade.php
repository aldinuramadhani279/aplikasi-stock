@extends('layouts.app')
@section('title', 'Penyesuaian Stok')

@section('content')
<div style="max-width:700px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="sliders" style="width:20px;height:20px;"></i> Penyesuaian Stok
            </span>
        </div>

        <div class="alert alert-warning">
            <i data-lucide="alert-triangle"></i>
            <div>⚠️ Fitur ini mengubah jumlah stok secara langsung. Gunakan dengan hati-hati dan selalu isi catatan alasan penyesuaian.</div>
        </div>

        <form method="POST" action="{{ route('stock.adjust.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Produk *</label>
                <select name="product_id" class="form-control" required onchange="loadProductInfo(this.value)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->current_stock }} {{ $product->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="productInfo" style="display:none; background:rgba(99,102,241,0.08); border-radius:10px; padding:14px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>SKU: <span id="pi_sku" style="font-family:monospace; font-weight:600;"></span></div>
                    <div>Stok Saat Ini: <span id="pi_stock" style="font-weight:700; font-size:18px; color:#a5b4fc;"></span></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Stok Baru (Aktual) *</label>
                <input type="number" name="new_stock" value="{{ old('new_stock') }}" class="form-control" min="0" required placeholder="Jumlah stok yang sebenarnya">
            </div>

            <div class="form-group">
                <label class="form-label">Alasan Penyesuaian *</label>
                <textarea name="notes" class="form-control" rows="3" required placeholder="Contoh: Hasil stock opname, kerusakan barang, dll.">{{ old('notes') }}</textarea>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('stock.movements') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-warning"><i data-lucide="save"></i> Simpan Penyesuaian</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function loadProductInfo(productId) {
        if (!productId) { document.getElementById('productInfo').style.display = 'none'; return; }
        fetch(`/stock/product/${productId}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('pi_sku').textContent = data.sku;
                document.getElementById('pi_stock').textContent = `${data.current_stock} ${data.unit}`;
                document.getElementById('productInfo').style.display = 'block';
            });
    }
</script>
@endsection
