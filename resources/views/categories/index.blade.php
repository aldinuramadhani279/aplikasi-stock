@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="grid grid-2">
    {{-- Form --}}
    <div class="card" style="height:fit-content;">
        <div class="card-title" style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="plus-circle" style="width:20px;height:20px;"></i> Tambah Kategori
        </div>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Nama kategori..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Keterangan kategori...">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;"><i data-lucide="save"></i> Simpan Kategori</button>
        </form>
    </div>

    {{-- List --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="tags" style="width:20px;height:20px;"></i> Daftar Kategori <span style="font-size:12px;color:var(--text-muted);font-weight:400;">({{ $categories->total() }})</span>
        </div>

        @if($categories->isEmpty())
            <div style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada kategori</div>
        @else
            @foreach($categories as $cat)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 12px; background:rgba(255,255,255,0.03); border-radius:8px; margin-bottom:8px;">
                    <div>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $cat->name }}</div>
                        <div style="font-size:12px; color:var(--text-muted);">{{ $cat->products_count }} produk · {{ $cat->description }}</div>
                    </div>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('categories.edit', $cat) }}" class="btn btn-secondary btn-sm btn-icon" title="Edit"><i data-lucide="pencil"></i></a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus"><i data-lucide="trash-2"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
            <div class="pagination-wrapper">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
