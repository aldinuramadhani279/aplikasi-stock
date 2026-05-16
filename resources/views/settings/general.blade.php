@extends('layouts.app')
@section('title', 'Pengaturan Umum')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <div class="card-title" style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="settings" style="width:20px;height:20px;"></i> Pengaturan Umum
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <div style="padding:16px; background:rgba(255,255,255,0.03); border-radius:10px;">
                <div style="font-weight:600; margin-bottom:4px; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="package" style="width:16px;height:16px;color:#a5b4fc;"></i> Tentang Aplikasi
                </div>
                <div style="font-size:13px; color:var(--text-secondary); padding-left:24px;">Aplikasi Manajemen Stok & Gudang dengan Telegram Bot Integration</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:8px; padding-left:24px;">Versi 1.0.0 · Laravel 10 · Dibuat dengan <i data-lucide="heart" style="width:12px;height:12px;color:#ef4444;fill:#ef4444;display:inline-block;vertical-align:middle;"></i></div>
            </div>

            <div style="padding:16px; background:rgba(255,255,255,0.03); border-radius:10px;">
                <div style="font-weight:600; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="users" style="width:16px;height:16px;color:#a5b4fc;"></i> Manajemen Pengguna
                </div>
                @php $users = \App\Models\User::all() @endphp
                @foreach($users as $u)
                    <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                        <div style="width:32px; height:32px; background:linear-gradient(135deg, #6366f1, #8b5cf6); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:white; flex-shrink:0;">{{ strtoupper(substr($u->name,0,1)) }}</div>
                        <div style="flex:1;">
                            <div style="font-weight:600; font-size:13px;">{{ $u->name }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $u->email }}</div>
                        </div>
                        <span class="badge badge-{{ $u->role === 'admin' ? 'purple' : 'blue' }}">{{ ucfirst($u->role) }}</span>
                    </div>
                @endforeach
            </div>

            <div style="padding:16px; background:rgba(255,255,255,0.03); border-radius:10px;">
                <div style="font-weight:600; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="terminal" style="width:16px;height:16px;color:#a5b4fc;"></i> Artisan Commands
                </div>
                <div style="font-size:13px; color:var(--text-secondary); display:flex; flex-direction:column; gap:6px; padding-left:24px;">
                    <code style="background:rgba(0,0,0,0.3); padding:4px 10px; border-radius:4px;">php artisan telegram:set-webhook</code>
                    <code style="background:rgba(0,0,0,0.3); padding:4px 10px; border-radius:4px;">php artisan telegram:send-report</code>
                    <code style="background:rgba(0,0,0,0.3); padding:4px 10px; border-radius:4px;">php artisan stock:check-low</code>
                    <code style="background:rgba(0,0,0,0.3); padding:4px 10px; border-radius:4px;">php artisan queue:work</code>
                    <code style="background:rgba(0,0,0,0.3); padding:4px 10px; border-radius:4px;">php artisan schedule:run</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
