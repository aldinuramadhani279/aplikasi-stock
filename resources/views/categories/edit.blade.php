@extends('layouts.app')
@section('title', 'Edit Kategori')

@section('content')
<div style="max-width:500px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="pencil" style="width:20px;height:20px;"></i> Edit Kategori
            </span>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left"></i> Kembali</a>
        </div>
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
