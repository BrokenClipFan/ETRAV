@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('storage/logotext.png') }}" alt="ETRAV Logo" class="mb-3" style="height: 55px; object-fit: contain;">
        <h4 class="fw-bold text-dark mb-1">Welcome Back</h4>
        <p class="text-muted small">Enter your administrative credentials to manage your ecosystem.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="px-md-2">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <div class="form-floating">
                <input id="email" type="email" name="email" class="form-control bg-light @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username">
                <label for="email" class="text-muted"><i class="bi bi-envelope me-2"></i>Email Address</label>
            </div>
            @error('email')<div class="text-danger small mt-1 ms-2">{{ $message }}</div>@enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="form-floating">
                <input id="password" type="password" name="password" class="form-control bg-light @error('password') is-invalid @enderror"
                    placeholder="••••••••" required autocomplete="current-password">
                <label for="password" class="text-muted"><i class="bi bi-lock me-2"></i>Password</label>
            </div>
            @error('password')<div class="text-danger small mt-1 ms-2">{{ $message }}</div>@enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input shadow-sm">
                <label for="remember_me" class="form-check-label text-muted small fw-medium">Keep me signed in</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-decoration-none fw-semibold" style="color: #0d6efd;">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Action Button -->
        <button type="submit" class="btn btn-primary w-100 rounded-pill btn-auth shadow-sm mb-3 fw-bold py-2">
            Sign In to Dashboard
        </button>

        <a href="{{ route('facebook.login') }}" class="btn btn-outline-primary w-100 rounded-pill fw-bold py-2 mb-4">
            <i class="bi bi-facebook me-1"></i> Continue with Facebook
        </a>

        <div class="text-center">
            <span class="text-muted small">New to ETRAV?</span>
            <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color: #0d6efd;">Create an account</a>
        </div>
    </form>
@endsection
