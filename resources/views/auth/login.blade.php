@extends('layouts.guest')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Welcome back to ETRAV</h4>
        <p class="text-muted small">Enter your administrative credentials to manage your ecosystem.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label text-muted small fw-bold mb-1">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" class="form-control bg-light border-start-0"
                    value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label text-muted small fw-bold mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary"
                        style="font-size: 12px;">Forgot password?</a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" name="password" class="form-control bg-light border-start-0"
                    placeholder="••••••••" required autocomplete="current-password">
            </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label text-muted small">Keep me signed in on this machine</label>
        </div>

        <!-- Action Button -->
        <button type="submit" class="btn btn-primary w-100 rounded-pill btn-auth shadow-sm mb-3">
            Sign In
        </button>

        <a href="{{ route('facebook.login') }}" class="btn btn-primary w-100">
            <i class="bi bi-facebook"></i> Login with Facebook
        </a>

        <p class="text-center text-muted small mb-0">
            New to ETRAV? <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">Create an
                account</a>
        </p>
    </form>
@endsection
