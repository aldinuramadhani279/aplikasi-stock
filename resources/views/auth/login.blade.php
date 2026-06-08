<x-guest-layout>
    {{-- Status (e.g. password reset confirmation) --}}
    @if (session('status'))
        <div class="alert-auth success">
            <i data-lucide="check-circle-2"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert-auth error">
            <i data-lucide="alert-circle"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="f-group">
            <label class="f-label" for="email">Alamat Email</label>
            <div class="f-input-wrap">
                <span class="f-icon"><i data-lucide="mail"></i></span>
                <input
                    id="email" type="email" name="email"
                    value="{{ old('email') }}"
                    class="f-input"
                    placeholder="nama@perusahaan.com"
                    required autofocus autocomplete="username"
                >
            </div>
        </div>

        {{-- Password --}}
        <div class="f-group">
            <label class="f-label" for="password">Password</label>
            <div class="f-input-wrap">
                <span class="f-icon"><i data-lucide="lock"></i></span>
                <input
                    id="password" type="password" name="password"
                    class="f-input"
                    placeholder="••••••••"
                    required autocomplete="current-password"
                >
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="f-row">
            <label class="f-check">
                <input type="checkbox" name="remember" id="remember_me">
                <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="f-forgot">Lupa password?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login">
            <i data-lucide="log-in"></i>
            Masuk ke Dashboard
        </button>
    </form>
</x-guest-layout>
