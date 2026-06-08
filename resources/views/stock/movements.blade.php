@extends('layouts.app')
@section('title', 'Riwayat Pergerakan Stok')

@section('content')
{{-- Filters --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
        <div style="flex:2; min-width:180px;">
            <label class="form-label">Produk</label>
            <select name="product_id" class="form-control">
                <option value="">Semua Produk</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1; min-width:120px;">
            <label class="form-label">Tipe</label>
            <select name="type" class="form-control">
                <option value="">Semua Tipe</option>
                <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>Masuk</option>
                <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>Keluar</option>
                <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>Penyesuaian</option>
            </select>
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-primary"><i data-lucide="filter"></i> Filter</button>
            <a href="{{ route('stock.movements') }}" class="btn btn-ghost btn-sm btn-icon"><i data-lucide="x"></i></a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title" style="display:flex; align-items:center; gap:8px;">
            <i data-lucide="history" style="width:20px;height:20px;"></i> Riwayat Pergerakan <span style="font-size:13px; color:var(--text-muted); font-weight:400;">({{ $movements->total() }} transaksi)</span>
        </span>
    </div>

    @if($movements->isEmpty())
        <div style="text-align:center; padding:48px; color:var(--text-muted);">
            <i data-lucide="inbox" style="width:40px;height:40px;opacity:0.3;margin:0 auto 12px;display:block;"></i>
            <div>Tidak ada transaksi ditemukan</div>
        </div>
    @else
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Tipe</th>
                        <th>Qty</th>
                        <th>Stok Sebelum</th>
                        <th>Stok Sesudah</th>
                        <th>Referensi</th>
                        <th>Catatan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $mv)
                        <tr>
                            <td style="color:var(--text-muted);">{{ $mv->id }}</td>
                            <td style="white-space:nowrap; color:var(--text-secondary);">{{ $mv->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('products.show', $mv->product_id) }}" style="color:var(--primary); text-decoration:none; font-weight:600;">{{ $mv->product->name }}</a>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $mv->product->sku }}</div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $mv->type === 'IN' ? 'teal' : ($mv->type === 'OUT' ? 'red' : 'blue') }}">
                                    @if($mv->type === 'IN')
                                        <i data-lucide="arrow-down-to-line"></i>
                                    @elseif($mv->type === 'OUT')
                                        <i data-lucide="arrow-up-from-line"></i>
                                    @else
                                        <i data-lucide="sliders"></i>
                                    @endif
                                    {{ $mv->type_label }}
                                </span>
                            </td>
                            <td style="font-weight:700; color:{{ $mv->type === 'IN' ? 'var(--success)' : ($mv->type === 'OUT' ? 'var(--danger)' : 'var(--info)') }};">
                                {{ $mv->type === 'OUT' ? '-' : '+' }}{{ $mv->quantity }}
                            </td>
                            <td style="color:var(--text-muted);">{{ $mv->stock_before }}</td>
                            <td style="font-weight:600;">{{ $mv->stock_after }}</td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $mv->reference_number ?? '-' }}</td>
                            <td style="font-size:12px; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $mv->notes ?? '-' }}</td>
                            <td style="color:var(--text-secondary);">{{ $mv->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $movements->links() }}</div>
    @endif
</div>
@endsection
