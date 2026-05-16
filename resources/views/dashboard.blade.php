@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<div class="grid grid-4" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon indigo"><i data-lucide="package"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label">Total Produk Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i data-lucide="warehouse"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_stock']) }}</div>
            <div class="stat-label">Total Stok Tersedia</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i data-lucide="alert-triangle"></i></div>
        <div>
            <div class="stat-value" style="background:linear-gradient(135deg,#fde68a,#f59e0b);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">{{ $stats['low_stock'] }}</div>
            <div class="stat-label">Stok Menipis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i data-lucide="circle-off"></i></div>
        <div>
            <div class="stat-value" style="background:linear-gradient(135deg,#fca5a5,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">{{ $stats['out_of_stock'] }}</div>
            <div class="stat-label">Stok Habis</div>
        </div>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon cyan"><i data-lucide="repeat-2"></i></div>
        <div>
            <div class="stat-value" style="background:linear-gradient(135deg,#67e8f9,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">{{ $stats['today_transactions'] }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i data-lucide="arrow-down-to-line"></i></div>
        <div>
            <div class="stat-value" style="background:linear-gradient(135deg,#6ee7b7,#10b981);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">{{ $stats['today_in'] }}</div>
            <div class="stat-label">Masuk Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i data-lucide="arrow-up-from-line"></i></div>
        <div>
            <div class="stat-value" style="background:linear-gradient(135deg,#fca5a5,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">{{ $stats['today_out'] }}</div>
            <div class="stat-label">Keluar Hari Ini</div>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:20px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Pergerakan Stok 7 Hari</span>
            <span style="font-size:11px;color:var(--text-muted);">{{ now()->subDays(6)->format('d M') }} — {{ now()->format('d M Y') }}</span>
        </div>
        <div style="position: relative; height: 250px; width: 100%;">
            <canvas id="stockChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Perlu Diperhatikan</span>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        @if($lowStockProducts->isEmpty())
            <div style="text-align:center;padding:32px 0;color:var(--text-muted);">
                <i data-lucide="check-circle" style="width:36px;height:36px;color:#34d399;margin-bottom:8px;display:block;margin-inline:auto;"></i>
                <div>Semua stok dalam kondisi aman</div>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($lowStockProducts->take(6) as $p)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:rgba(255,255,255,0.03);border-radius:8px;border-left:3px solid {{ $p->isOutOfStock() ? '#ef4444' : '#f59e0b' }};">
                    <div>
                        <div style="font-size:13px;font-weight:600;">{{ $p->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $p->category->name }} · {{ $p->sku }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:15px;font-weight:700;color:{{ $p->isOutOfStock() ? '#ef4444' : '#f59e0b' }};">{{ $p->current_stock }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">min: {{ $p->minimum_stock }} {{ $p->unit }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Transaksi Terbaru</span>
        <a href="{{ route('stock.movements') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    @if($recentMovements->isEmpty())
        <div style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada transaksi</div>
    @else
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr>
                <th>Produk</th><th>Tipe</th><th>Jumlah</th><th>Stok Sesudah</th><th>Oleh</th><th>Waktu</th>
            </tr></thead>
            <tbody>
                @foreach($recentMovements as $mv)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $mv->product->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $mv->product->sku }}</div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $mv->type==='IN'?'green':($mv->type==='OUT'?'red':'blue') }}">{{ $mv->type_label }}</span>
                    </td>
                    <td style="font-weight:600;color:{{ $mv->type==='IN'?'#34d399':($mv->type==='OUT'?'#f87171':'#60a5fa') }};">
                        {{ $mv->type==='OUT'?'-':'+' }}{{ $mv->quantity }} {{ $mv->product->unit }}
                    </td>
                    <td style="font-weight:600;">{{ $mv->stock_after }}</td>
                    <td style="color:var(--text-secondary);">{{ $mv->user->name }}</td>
                    <td style="color:var(--text-muted);">{{ $mv->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
lucide.createIcons();
const ctx = document.getElementById('stockChart').getContext('2d');
const chartData = @json($chartData);
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.date),
        datasets: [
            { label: 'Masuk', data: chartData.map(d => d.in), backgroundColor: 'rgba(16,185,129,0.7)', borderColor: '#10b981', borderWidth: 1, borderRadius: 4 },
            { label: 'Keluar', data: chartData.map(d => d.out), backgroundColor: 'rgba(239,68,68,0.6)', borderColor: '#ef4444', borderWidth: 1, borderRadius: 4 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#94a3b8', font: { size: 12 } } } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' }, beginAtZero: true }
        }
    }
});
</script>
@endsection
