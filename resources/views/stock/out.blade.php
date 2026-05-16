@extends('layouts.app')
@section('title', 'Stok Keluar')

@section('content')
<div class="grid grid-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="arrow-up-from-line" style="width:20px;height:20px;color:#ef4444;"></i> Input Barang Keluar
            </span>
        </div>

        <form method="POST" action="{{ route('stock.out.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Produk *</label>
                <select name="product_id" id="product_select" class="form-control" required onchange="loadProductInfo(this.value)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->current_stock }} {{ $product->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Product Info --}}
            <div id="productInfo" style="display:none; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:14px; margin-bottom:20px;">
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; text-align:center;">
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">SKU</div>
                        <div id="pi_sku" style="font-weight:600; font-size:13px; font-family:monospace;"></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">Stok Tersedia</div>
                        <div id="pi_stock" style="font-weight:700; font-size:18px; color:#f87171;"></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">Min. Stok</div>
                        <div id="pi_min" style="font-weight:600; color:#fbbf24;"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah Keluar *</label>
                <input type="number" name="quantity" id="qty_input" value="{{ old('quantity') }}" class="form-control" min="1" required placeholder="0">
                <div id="stock_warning" style="display:none; color:#f87171; font-size:12px; margin-top:4px; display:flex; align-items:center; gap:4px;">
                    <i data-lucide="alert-triangle" style="width:14px;height:14px;"></i> Jumlah melebihi stok tersedia!
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Referensi</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="form-control" placeholder="SO-001, faktur, dll.">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Tujuan, keterangan, dll.">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-danger" style="width:100%; font-size:15px; padding:12px; justify-content:center;">
                <i data-lucide="arrow-up-from-line" style="width:18px;height:18px;"></i> Catat Barang Keluar
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="clipboard-list" style="width:20px;height:20px;"></i> Transaksi Keluar Hari Ini
        </div>
        @php
            $todayOut = \App\Models\StockMovement::with(['product','user'])
                ->where('type','OUT')->whereDate('created_at', today())->latest()->take(10)->get();
        @endphp

        @if($todayOut->isEmpty())
            <div style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada transaksi hari ini</div>
        @else
            @foreach($todayOut as $mv)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div>
                        <div style="font-weight:600; font-size:13px;">{{ $mv->product->name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $mv->user->name }} · {{ $mv->notes }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700; color:#f87171;">-{{ $mv->quantity }} {{ $mv->product->unit }}</div>
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
    let currentStock = 0;

    function loadProductInfo(productId) {
        if (!productId) {
            document.getElementById('productInfo').style.display = 'none';
            return;
        }
        fetch(`/stock/product/${productId}`)
            .then(r => r.json())
            .then(data => {
                currentStock = data.current_stock;
                document.getElementById('pi_sku').textContent = data.sku;
                document.getElementById('pi_stock').textContent = `${data.current_stock} ${data.unit}`;
                document.getElementById('pi_min').textContent = `${data.minimum_stock} ${data.unit}`;
                document.getElementById('productInfo').style.display = 'block';
                checkStockWarning();
            });
    }

    function checkStockWarning() {
        const qty = parseInt(document.getElementById('qty_input').value) || 0;
        const warn = document.getElementById('stock_warning');
        if (currentStock > 0 && qty > currentStock) {
            warn.style.display = 'flex';
        } else {
            warn.style.display = 'none';
        }
    }

    document.getElementById('qty_input').addEventListener('input', checkStockWarning);

    const select = document.getElementById('product_select');
    if (select.value) loadProductInfo(select.value);
</script>
@endsection
