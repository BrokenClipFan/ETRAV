@extends('layouts.guest')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Get started with ETRAV</h4>
        <p class="text-muted small">Create an account to start configuring backend system tools.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Full Name -->
        <div class="mb-3">
            <label for="name" class="form-label text-muted small fw-bold mb-1">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                <input id="name" type="text" name="name" class="form-control bg-light border-start-0" value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name">
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label text-muted small fw-bold mb-1">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" class="form-control bg-light border-start-0" value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="username">
            </div>
        </div>

        <!-- Passwords Grid -->
        <div class="row">
            <div class="col-sm-6 mb-3">
                <label for="password" class="form-label text-muted small fw-bold mb-1">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required autocomplete="new-password">
                </div>
            </div>
            
            <div class="col-sm-6 mb-3">
                <label for="password_confirmation" class="form-label text-muted small fw-bold mb-1">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control bg-light border-start-0" placeholder="••••••••" required autocomplete="new-password">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success w-100 rounded-pill btn-auth shadow-sm mb-3">
            Register Account
        </button>

        <p class="text-center text-muted small mb-0">
            Already registered? <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Sign in here</a>
        </p>
    </form>
@endsection