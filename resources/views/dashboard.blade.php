@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- ── Row 1: 4 stat cards ── --}}
<div class="grid grid-4" style="margin-bottom:16px;">
    <div class="stat-card teal">
        <div class="stat-top">
            <div>
                <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                <div class="stat-label">Total Produk Aktif</div>
            </div>
            <div class="stat-icon teal"><i data-lucide="package"></i></div>
        </div>
    </div>
    <div class="stat-card slate">
        <div class="stat-top">
            <div>
                <div class="stat-value">{{ number_format($stats['total_stock']) }}</div>
                <div class="stat-label">Total Stok Tersedia</div>
            </div>
            <div class="stat-icon slate"><i data-lucide="warehouse"></i></div>
        </div>
    </div>
    <div class="stat-card amber">
        <div class="stat-top">
            <div>
                <div class="stat-value" style="color:var(--warning);">{{ $stats['low_stock'] }}</div>
                <div class="stat-label">Stok Menipis</div>
            </div>
            <div class="stat-icon amber"><i data-lucide="alert-triangle"></i></div>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-top">
            <div>
                <div class="stat-value" style="color:var(--danger);">{{ $stats['out_of_stock'] }}</div>
                <div class="stat-label">Stok Habis</div>
            </div>
            <div class="stat-icon red"><i data-lucide="circle-off"></i></div>
        </div>
    </div>
</div>

{{-- ── Row 2: 3 today stats ── --}}
<div class="grid grid-3" style="margin-bottom:16px;">
    <div class="stat-card cyan">
        <div class="stat-top">
            <div>
                <div class="stat-value" style="color:var(--info);">{{ $stats['today_transactions'] }}</div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>
            <div class="stat-icon cyan"><i data-lucide="repeat-2"></i></div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-top">
            <div>
                <div class="stat-value" style="color:var(--success);">{{ $stats['today_in'] }}</div>
                <div class="stat-label">Barang Masuk Hari Ini</div>
            </div>
            <div class="stat-icon green"><i data-lucide="arrow-down-to-line"></i></div>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-top">
            <div>
                <div class="stat-value" style="color:var(--danger);">{{ $stats['today_out'] }}</div>
                <div class="stat-label">Barang Keluar Hari Ini</div>
            </div>
            <div class="stat-icon red"><i data-lucide="arrow-up-from-line"></i></div>
        </div>
    </div>
</div>

{{-- ── Row 3: Chart + Low stock ── --}}
<div class="grid grid-2" style="margin-bottom:16px;">

    {{-- Chart --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Pergerakan Stok 7 Hari</div>
                <div class="card-subtitle">{{ now()->subDays(6)->format('d M') }} — {{ now()->format('d M Y') }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--text-muted);font-weight:600;">
                    <span style="width:10px;height:10px;border-radius:3px;background:#0D9488;display:inline-block;"></span> Masuk
                </span>
                <span style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--text-muted);font-weight:600;">
                    <span style="width:10px;height:10px;border-radius:3px;background:#DC2626;display:inline-block;"></span> Keluar
                </span>
            </div>
        </div>
        <div style="position:relative; height:230px; width:100%;">
            <canvas id="stockChart"></canvas>
        </div>
    </div>

    {{-- Low stock alert --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Perlu Diperhatikan</div>
                <div class="card-subtitle">Stok menipis &amp; habis</div>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>

        @if($lowStockProducts->isEmpty())
            <div class="empty-state">
                <i data-lucide="check-circle" style="color:#16A34A;width:38px;height:38px;"></i>
                <p>Semua stok dalam kondisi aman 🎉</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:7px;">
                @foreach($lowStockProducts->take(7) as $p)
                <div style="
                    display:flex;align-items:center;justify-content:space-between;
                    padding:10px 12px;
                    background: {{ $p->isOutOfStock() ? '#FEF2F2' : '#FFFBEB' }};
                    border-radius:8px;
                    border-left:3px solid {{ $p->isOutOfStock() ? '#DC2626' : '#D97706' }};
                ">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $p->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:1px;">{{ $p->category->name }} · {{ $p->sku }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:16px;font-weight:800;color:{{ $p->isOutOfStock() ? '#DC2626' : '#D97706' }};">
                            {{ $p->current_stock }}
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);">min: {{ $p->minimum_stock }} {{ $p->unit }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Row 4: Recent transactions ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Transaksi Terbaru</div>
            <div class="card-subtitle">10 transaksi terakhir</div>
        </div>
        <a href="{{ route('stock.movements') }}" class="btn btn-outline btn-sm">
            <i data-lucide="list"></i> Lihat Semua
        </a>
    </div>

    @if($recentMovements->isEmpty())
        <div class="empty-state">
            <i data-lucide="inbox"></i>
            <p>Belum ada transaksi yang tercatat</p>
        </div>
    @else
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Stok Sesudah</th>
                    <th>Oleh</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentMovements as $mv)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $mv->product->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:1px;">{{ $mv->product->sku }}</div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $mv->type==='IN'?'teal':($mv->type==='OUT'?'red':'blue') }}">
                            {{ $mv->type_label }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:{{ $mv->type==='IN'?'#16A34A':($mv->type==='OUT'?'#DC2626':'#1D4ED8') }};">
                        {{ $mv->type==='OUT'?'−':'+' }}{{ $mv->quantity }} {{ $mv->product->unit }}
                    </td>
                    <td style="font-weight:600;">{{ $mv->stock_after }}</td>
                    <td style="color:var(--text-secondary);">{{ $mv->user->name }}</td>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $mv->created_at->diffForHumans() }}</td>
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
            {
                label: 'Masuk',
                data: chartData.map(d => d.in),
                backgroundColor: 'rgba(13,148,136,0.18)',
                borderColor: '#0D9488',
                borderWidth: 2,
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Keluar',
                data: chartData.map(d => d.out),
                backgroundColor: 'rgba(220,38,38,0.12)',
                borderColor: '#DC2626',
                borderWidth: 2,
                borderRadius: 5,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0F172A',
                titleColor: '#E2E8F0',
                bodyColor: '#94A3B8',
                borderColor: '#1E293B',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 8,
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#94A3B8', font: { size: 11, family: 'Plus Jakarta Sans' } },
                border: { display: false }
            },
            y: {
                grid: { color: '#F1F5F4', lineWidth: 1 },
                ticks: { color: '#94A3B8', font: { size: 11, family: 'Plus Jakarta Sans' }, padding: 6 },
                border: { display: false, dash: [4, 4] },
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
