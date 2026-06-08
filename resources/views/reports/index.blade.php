@extends('layouts.app')
@section('title', 'Laporan Stok')

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon green"><i data-lucide="arrow-down-to-line"></i></div>
        <div>
            <div class="stat-value" style="background: linear-gradient(135deg, #6ee7b7, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ number_format($summary['total_in']) }}</div>
            <div class="stat-label">Total Masuk (Periode)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i data-lucide="arrow-up-from-line"></i></div>
        <div>
            <div class="stat-value" style="background: linear-gradient(135deg, #fca5a5, #ef4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ number_format($summary['total_out']) }}</div>
            <div class="stat-label">Total Keluar (Periode)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan"><i data-lucide="repeat"></i></div>
        <div>
            <div class="stat-value" style="background: linear-gradient(135deg, #67e8f9, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ number_format($summary['total_transactions']) }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    {{-- Filters & Table --}}
    <div style="grid-column:span 2;">
        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:12px; flex-wrap:wrap;">
                <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; flex:1;">
                    <div style="flex:1; min-width:140px;">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div style="flex:1; min-width:140px;">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to', $dateTo->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div style="flex:1; min-width:140px;">
                        <label class="form-label">Tipe Transaksi</label>
                        <select name="type" class="form-control">
                            <option value="">Semua</option>
                            <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>Masuk</option>
                            <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>Keluar</option>
                            <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>Penyesuaian</option>
                        </select>
                    </div>
                    <div style="display:flex; gap:8px; align-items:flex-end;">
                        <button type="submit" class="btn btn-primary"><i data-lucide="filter"></i> Filter</button>
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary"><i data-lucide="x"></i></a>
                    </div>
                </form>

                <div style="display:flex; gap:8px; align-items:flex-end;">
                    <form method="POST" action="{{ route('reports.send-telegram') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="date" value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}">
                        <button type="submit" class="btn btn-secondary" style="background:rgba(39,195,255,0.1); border-color:rgba(39,195,255,0.3); color:#67e8f9;">
                            <i data-lucide="send"></i> Kirim ke Telegram
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="bar-chart-2" style="width:20px;height:20px;"></i> Transaksi Periode {{ $dateFrom->format('d/m/Y') }} — {{ $dateTo->format('d/m/Y') }}
            </div>

            @if($movements->isEmpty())
                <div style="text-align:center; padding:48px; color:var(--text-muted);">Tidak ada transaksi pada periode ini</div>
            @else
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th>Qty</th>
                                <th>Stok Sesudah</th>
                                <th>Oleh</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $mv)
                                <tr>
                                    <td style="white-space:nowrap; color:var(--text-muted);">{{ $mv->created_at->format('d/m H:i') }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $mv->product->name }}</div>
                                        <div style="font-size:11px; color:var(--text-muted);">{{ $mv->product->sku }}</div>
                                    </td>
                                    <td style="color:var(--text-secondary);">{{ $mv->product->category->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $mv->type === 'IN' ? 'green' : ($mv->type === 'OUT' ? 'red' : 'blue') }}">
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
                                    <td style="font-weight:700; color:{{ $mv->type === 'IN' ? '#34d399' : ($mv->type === 'OUT' ? '#f87171' : '#60a5fa') }};">
                                        {{ $mv->type === 'OUT' ? '-' : '+' }}{{ $mv->quantity }}
                                    </td>
                                    <td>{{ $mv->stock_after }}</td>
                                    <td style="color:var(--text-secondary);">{{ $mv->user->name }}</td>
                                    <td style="font-size:12px; max-width:150px; overflow:hidden; text-overflow:ellipsis;">{{ $mv->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrapper">{{ $movements->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Low Stock Report --}}
@if($lowStockProducts->isNotEmpty())
<div class="card" style="margin-top:24px;">
    <div class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <i data-lucide="alert-triangle" style="width:20px;height:20px;color:#f59e0b;"></i> Produk Perlu Diperhatikan Saat Ini
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Stok Minimum</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $p)
                    <tr>
                        <td style="font-weight:600;">{{ $p->name }}</td>
                        <td><code style="background:rgba(99,102,241,0.1); padding:2px 6px; border-radius:4px; font-size:12px;">{{ $p->sku }}</code></td>
                        <td style="color:var(--text-secondary);">{{ $p->category->name }}</td>
                        <td style="font-weight:700; color:{{ $p->isOutOfStock() ? '#ef4444' : '#f59e0b' }};">{{ $p->current_stock }} {{ $p->unit }}</td>
                        <td style="color:var(--text-muted);">{{ $p->minimum_stock }} {{ $p->unit }}</td>
                        <td>
                            @if($p->isOutOfStock())
                                <span class="badge badge-red"><i data-lucide="circle-off"></i> Habis</span>
                            @else
                                <span class="badge badge-yellow"><i data-lucide="alert-triangle"></i> Menipis</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
