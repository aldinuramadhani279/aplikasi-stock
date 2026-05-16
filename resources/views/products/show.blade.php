@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="grid grid-2" style="margin-bottom:24px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="package" style="width:20px;height:20px;"></i> {{ $product->name }}
            </span>
            <div style="display:flex; gap:8px;">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm"><i data-lucide="pencil"></i> Edit</a>
                @endif
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left"></i> Kembali</a>
            </div>
        </div>

        <div style="display:flex; gap:20px; align-items:flex-start;">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" style="width:100px; height:100px; border-radius:12px; object-fit:cover; flex-shrink:0;">
            @else
                <div style="width:100px; height:100px; background:rgba(99,102,241,0.15); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-lucide="package" style="width:40px;height:40px;color:#818cf8;"></i>
                </div>
            @endif
            <div style="flex:1;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">SKU</div>
                        <div style="font-weight:600; margin-top:3px;"><code style="background:rgba(99,102,241,0.1); padding:3px 8px; border-radius:4px;">{{ $product->sku }}</code></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Kategori</div>
                        <div style="font-weight:600; margin-top:3px;">{{ $product->category->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Satuan</div>
                        <div style="font-weight:600; margin-top:3px;">{{ $product->unit }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Status</div>
                        <div style="margin-top:3px;">
                            @if($product->isOutOfStock())
                                <span class="badge badge-red"><i data-lucide="circle-off"></i> Habis</span>
                            @elseif($product->isLowStock())
                                <span class="badge badge-yellow"><i data-lucide="alert-triangle"></i> Menipis</span>
                            @else
                                <span class="badge badge-green"><i data-lucide="check-circle"></i> Aman</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-top:24px;">
            <div style="text-align:center; padding:16px; background:rgba(99,102,241,0.08); border-radius:10px;">
                <div style="font-size:28px; font-weight:800; color:#a5b4fc;">{{ $product->current_stock }}</div>
                <div style="font-size:12px; color:var(--text-muted);">Stok Sekarang</div>
            </div>
            <div style="text-align:center; padding:16px; background:rgba(245,158,11,0.08); border-radius:10px;">
                <div style="font-size:28px; font-weight:800; color:#fbbf24;">{{ $product->minimum_stock }}</div>
                <div style="font-size:12px; color:var(--text-muted);">Stok Minimum</div>
            </div>
            <div style="text-align:center; padding:16px; background:rgba(16,185,129,0.08); border-radius:10px;">
                <div style="font-size:28px; font-weight:800; color:#34d399;">{{ $product->maximum_stock ?? '∞' }}</div>
                <div style="font-size:12px; color:var(--text-muted);">Stok Maksimum</div>
            </div>
        </div>

        @if($product->description)
            <div style="margin-top:20px; padding:14px; background:rgba(255,255,255,0.03); border-radius:8px; font-size:14px; color:var(--text-secondary);">
                {{ $product->description }}
            </div>
        @endif

        <div style="display:flex; gap:10px; margin-top:20px;">
            <a href="{{ route('stock.in') }}?product_id={{ $product->id }}" class="btn btn-success"><i data-lucide="arrow-down-to-line"></i> Stok Masuk</a>
            <a href="{{ route('stock.out') }}?product_id={{ $product->id }}" class="btn btn-danger"><i data-lucide="arrow-up-from-line"></i> Stok Keluar</a>
        </div>
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="clipboard-list" style="width:20px;height:20px;"></i> Riwayat Terbaru
        </div>
        @if($movements->isEmpty())
            <div style="text-align:center; padding:24px; color:var(--text-muted);">Belum ada transaksi</div>
        @else
            @foreach($movements as $mv)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div>
                        <span class="badge badge-{{ $mv->type === 'IN' ? 'green' : ($mv->type === 'OUT' ? 'red' : 'blue') }}">{{ $mv->type_label }}</span>
                        <span style="font-size:12px; color:var(--text-muted); margin-left:8px;">{{ $mv->user->name }}</span>
                        @if($mv->notes)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $mv->notes }}</div>
                        @endif
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700; color:{{ $mv->type === 'IN' ? '#34d399' : ($mv->type === 'OUT' ? '#f87171' : '#60a5fa') }};">
                            {{ $mv->type !== 'OUT' ? '+' : '-' }}{{ $mv->quantity }}
                        </div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $mv->created_at->format('d/m H:i') }}</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
