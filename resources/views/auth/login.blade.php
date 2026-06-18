<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success border-0 shadow-sm mb-4" role="alert" style="font-size: var(--font-size-sm); border-radius: var(--radius-sm);">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold" style="font-size:var(--font-size-sm);color:var(--color-text-dark);">Alamat Email</label>
            <input id="email" class="form-control form-control-custom @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', 'superadmin@cctv.com') }}" required autofocus autocomplete="username" placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold" style="font-size:var(--font-size-sm);color:var(--color-text-dark);">Kata Sandi</label>
            <input id="password" class="form-control form-control-custom @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember" style="cursor: pointer;">
            <label for="remember_me" class="form-check-label text-secondary" style="font-size: var(--font-size-sm); cursor: pointer; user-select: none;">TETAP MASUK</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary-custom py-2" style="font-size:var(--font-size-sm);letter-spacing:0.3px;">
                <i class="bi bi-box-arrow-in-right me-2"></i>MASUK KE DASHBOARD
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="text-decoration-none" href="{{ route('password.request') }}" style="font-size:var(--font-size-xs);color:var(--color-brand);">
                    Lupa kata sandi Anda?
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
