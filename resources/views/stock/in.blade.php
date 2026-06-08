@extends('layouts.app')
@section('title', 'Stok Masuk')

@section('content')
<div class="grid grid-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="arrow-down-to-line" style="width:20px;height:20px;color:var(--success);"></i> Input Barang Masuk
            </span>
        </div>

        <form method="POST" action="{{ route('stock.in.store') }}" id="stockInForm">
            @csrf

            <div class="form-group">
                <label class="form-label">Produk *</label>
                <select name="product_id" id="product_select" class="form-control" required onchange="loadProductInfo(this.value)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ (old('product_id') == $product->id || request('product_id') == $product->id) ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->current_stock }} {{ $product->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Product Info Card --}}
            <div id="productInfo" style="display:none; background:#F0FDFA; border:1px solid #CCFBF1; border-radius:10px; padding:14px; margin-bottom:20px;">
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; text-align:center;">
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">SKU</div>
                        <div id="pi_sku" style="font-weight:600; font-size:13px; font-family:monospace;"></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">Stok Sekarang</div>
                        <div id="pi_stock" style="font-weight:700; font-size:18px; color:var(--primary);"></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">Min. Stok</div>
                        <div id="pi_min" style="font-weight:600; color:var(--warning);"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah Masuk *</label>
                <input type="number" name="quantity" value="{{ old('quantity') }}" class="form-control" min="1" required placeholder="0">
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Referensi</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="form-control" placeholder="PO-001, INV-001, dll.">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Sumber barang, keterangan, dll.">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-success" style="width:100%; font-size:15px; padding:12px; justify-content:center;">
                <i data-lucide="arrow-down-to-line" style="width:18px;height:18px;"></i> Catat Barang Masuk
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="clipboard-list" style="width:20px;height:20px;"></i> Transaksi Masuk Hari Ini
        </div>
        @php
            $todayIn = \App\Models\StockMovement::with(['product','user'])
                ->where('type','IN')->whereDate('created_at', today())->latest()->take(10)->get();
        @endphp

        @if($todayIn->isEmpty())
            <div style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada transaksi hari ini</div>
        @else
            @foreach($todayIn as $mv)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-light);">
                    <div>
                        <div style="font-weight:600; font-size:13px;">{{ $mv->product->name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $mv->user->name }} · {{ $mv->notes }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700; color:var(--success);">+{{ $mv->quantity }} {{ $mv->product->unit }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $mv->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    function loadProductInfo(productId) {
        if (!productId) {
            document.getElementById('productInfo').style.display = 'none';
            return;
        }
        fetch(`/stock/product/${productId}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('pi_sku').textContent = data.sku;
                document.getElementById('pi_stock').textContent = `${data.current_stock} ${data.unit}`;
                document.getElementById('pi_min').textContent = `${data.minimum_stock} ${data.unit}`;
                document.getElementById('productInfo').style.display = 'block';
            });
    }

    // Auto-load if product pre-selected
    const select = document.getElementById('product_select');
    if (select.value) loadProductInfo(select.value);
</script>
@endsection
