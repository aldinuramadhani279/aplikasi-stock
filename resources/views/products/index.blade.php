@extends('layouts.app')
@section('title', 'Daftar Produk')

@section('content')
<div class="card" style="margin-bottom:18px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:2;min-width:200px;">
            <label class="form-label">Cari Produk</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau SKU..." class="form-control">
        </div>
        <div style="flex:1;min-width:150px;">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:150px;">
            <label class="form-label">Status Stok</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="low" {{ request('status')==='low'?'selected':'' }}>Menipis</option>
                <option value="out" {{ request('status')==='out'?'selected':'' }}>Habis</option>
                <option value="active" {{ request('status')==='active'?'selected':'' }}>Aktif</option>
                <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Nonaktif</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary"><i data-lucide="search"></i> Cari</button>
            <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm btn-icon"><i data-lucide="x"></i></a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Produk <span style="font-size:12px;color:var(--text-muted);font-weight:400;">({{ $products->total() }})</span></span>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> Tambah Produk</a>
        @endif
    </div>

    @if($products->isEmpty())
        <div style="text-align:center;padding:48px;color:var(--text-muted);">
            <i data-lucide="package" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:0.3;"></i>
            <div>Belum ada produk</div>
            <a href="{{ route('products.create') }}" class="btn btn-primary" style="margin-top:12px;">Tambah Produk Pertama</a>
        </div>
    @else
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr>
                <th>Produk</th><th>SKU</th><th>Kategori</th><th>Stok</th><th>Min. Stok</th><th>Status</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" style="width:34px;height:34px;border-radius:8px;object-fit:cover;">
                            @else
                                <div style="width:34px;height:34px;background:var(--primary-light);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i data-lucide="package" style="width:16px;height:16px;color:var(--primary);"></i>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:600;">{{ $product->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $product->unit }}</div>
                            </div>
                        </div>
                    </td>
                    <td><code style="background:#F0FDFA;color:var(--primary);padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600;">{{ $product->sku }}</code></td>
                    <td style="color:var(--text-secondary);">{{ $product->category->name }}</td>
                    <td>
                        <span style="font-weight:700;font-size:15px;color:{{ $product->isOutOfStock()?'var(--danger)':($product->isLowStock()?'var(--warning)':'var(--success)') }};">{{ $product->current_stock }}</span>
                        <span style="color:var(--text-muted);font-size:12px;"> {{ $product->unit }}</span>
                    </td>
                    <td style="color:var(--text-muted);">{{ $product->minimum_stock }} {{ $product->unit }}</td>
                    <td>
                        @if($product->isOutOfStock())
                            <span class="badge badge-red"><i data-lucide="circle-off"></i> Habis</span>
                        @elseif($product->isLowStock())
                            <span class="badge badge-yellow"><i data-lucide="alert-triangle"></i> Menipis</span>
                        @else
                            <span class="badge badge-green"><i data-lucide="check-circle"></i> Aman</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm btn-icon" title="Detail"><i data-lucide="eye"></i></a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline btn-sm btn-icon" title="Edit"><i data-lucide="pencil"></i></a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus"><i data-lucide="trash-2"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $products->links() }}</div>
    @endif
</div>
@endsection
@section('scripts')<script>lucide.createIcons();</script>@endsection
