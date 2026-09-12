@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('storage/logotext.png') }}" alt="ETRAV Logo" class="mb-3" style="height: 55px; object-fit: contain;">
        <h4 class="fw-bold text-dark mb-1">Get Started</h4>
        <p class="text-muted small">Create an account to start configuring your ecosystem.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="px-md-2">
        @csrf

        <!-- Full Name -->
        <div class="mb-3">
            <div class="form-floating">
                <input id="name" type="text" name="name" class="form-control bg-light @error('name') is-invalid @enderror" 
                    value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name">
                <label for="name" class="text-muted"><i class="bi bi-person me-2"></i>Full Name</label>
            </div>
            @error('name')<div class="text-danger small mt-1 ms-2">{{ $message }}</div>@enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <div class="form-floating">
                <input id="email" type="email" name="email" class="form-control bg-light @error('email') is-invalid @enderror" 
                    value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="username">
                <label for="email" class="text-muted"><i class="bi bi-envelope me-2"></i>Email Address</label>
            </div>
            @error('email')<div class="text-danger small mt-1 ms-2">{{ $message }}</div>@enderror
        </div>

        <!-- Phone Number -->
        <div class="mb-3">
            <div class="form-floating">
                <input id="phone" type="tel" name="phone_number" class="form-control bg-light @error('phone_number') is-invalid @enderror" 
                    value="{{ old('phone_number') }}" placeholder="09123456789" required autocomplete="tel">
                <label for="phone" class="text-muted"><i class="bi bi-telephone me-2"></i>Phone Number</label>
            </div>
            @error('phone_number')<div class="text-danger small mt-1 ms-2">{{ $message }}</div>@enderror
        </div>

        <!-- Passwords Grid -->
        <div class="row">
            <div class="col-sm-6 mb-4">
                <div class="form-floating">
                    <input id="password" type="password" name="password" class="form-control bg-light @error('password') is-invalid @enderror" 
                        placeholder="••••••••" required autocomplete="new-password">
                    <label for="password" class="text-muted"><i class="bi bi-lock me-2"></i>Password</label>
                </div>
                @error('password')<div class="text-danger small mt-1 ms-2">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-sm-6 mb-4">
                <div class="form-floating">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control bg-light" 
                        placeholder="••••••••" required autocomplete="new-password">
                    <label for="password_confirmation" class="text-muted"><i class="bi bi-shield-check me-2"></i>Confirm Password</label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 rounded-pill btn-auth shadow-sm mb-4 fw-bold py-2">
            Create Account
        </button>

        <div class="text-center">
            <span class="text-muted small">Already registered?</span>
            <a href="{{ route('login') }}" class="fw-bold text-decoration-none ms-1" style="color: #0d6efd;">Sign in here</a>
        </div>
    </form>
@endsection