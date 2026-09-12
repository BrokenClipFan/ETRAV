<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETRAV - Profile Settings</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .notify-dot-absolute {
            width: 8px;
            height: 8px;
            background-color: #dc3545;
            border-radius: 50%;
            position: absolute;
            top: 4px;
            right: 12px;
        }
    </style>
</head>
<body class="bg-light">

    @php
        $user = Auth::user();
        $hasNotification = \App\Models\Booking::where('user_id', $user->id)->where('notify', true)->exists();
    @endphp

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top py-3 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2"
                href="{{ route('home') }}">
                <img src="{{ asset('storage/logotext.png') }}" alt="ETRAV Logo"
                    style="height: 38px; object-fit: contain;">
            </a>

            <div class="ms-auto">
                <div class="dropdown">
                    <button
                        class="btn border-0 d-flex align-items-center gap-1 text-muted fw-medium fs-6 bg-transparent p-0 position-relative"
                        type="button" id="breezeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>{{ $user->name ?? 'Guest User' }}</span>
                        <span class="notify-dot-absolute {{ $hasNotification ? '' : 'd-none' }}"></span>
                        <i class="bi bi-chevron-down small ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1"
                        aria-labelledby="breezeDropdown" style="width: 220px; border-radius: 6px;">
                        @if($user->is_admin)
                            <li>
                                <a class="dropdown-item py-2 text-primary fw-bold px-4 d-flex align-items-center" href="{{ route('admin.bookings') }}">
                                    <i class="bi bi-shield-lock-fill me-2 fs-6"></i> Admin Panel
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                        @endif
                        <li><a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center" href="{{ route('home') }}"><i class="bi bi-map me-2 fs-6"></i> Explore Tours</a></li>
                        <li><a class="dropdown-item py-2 text-primary fw-medium px-4 d-flex align-items-center" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 fs-6"></i> Profile</a></li>
                        <li>
                            <a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center justify-content-between"
                                href="{{ route('bookings.view') }}">
                                <span><i class="bi bi-journal-bookmark me-2 fs-6"></i> Bookings</span>
                                <span class="badge rounded-pill bg-danger {{ $hasNotification ? '' : 'd-none' }}"
                                    style="font-size: 10px;">New</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger px-4 fw-medium d-flex align-items-center"><i
                                        class="bi bi-box-arrow-right me-2 fs-6"></i> Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container py-5" style="max-width: 800px;">
        <h3 class="fw-bold text-dark mb-4"><i class="bi bi-person-circle text-primary me-2"></i>Account Settings</h3>

        <!-- Profile Information -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold mb-1">Profile Information</h5>
                <p class="text-muted small mb-4">Update your account's profile information and email address.</p>

                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Full Name</label>
                        <input type="text" class="form-control bg-light" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Phone Number</label>
                        <input type="text" class="form-control bg-light" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="+63 912 345 6789">
                        @error('phone_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium small">Email Address</label>
                        <input type="email" class="form-control bg-light" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4 fw-medium rounded-pill shadow-sm">Save Changes</button>
                        @if (session('status') === 'profile-updated')
                            <span class="text-success small fw-medium"><i class="bi bi-check-circle-fill me-1"></i>Saved successfully!</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold mb-1">Update Password</h5>
                <p class="text-muted small mb-4">Ensure your account is using a long, random password to stay secure.</p>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Current Password</label>
                        <input type="password" class="form-control bg-light" name="current_password" required>
                        @error('current_password', 'updatePassword')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small">New Password</label>
                        <input type="password" class="form-control bg-light" name="password" required>
                        @error('password', 'updatePassword')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium small">Confirm Password</label>
                        <input type="password" class="form-control bg-light" name="password_confirmation" required>
                        @error('password_confirmation', 'updatePassword')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-dark px-4 fw-medium rounded-pill shadow-sm">Update Password</button>
                        @if (session('status') === 'password-updated')
                            <span class="text-success small fw-medium"><i class="bi bi-check-circle-fill me-1"></i>Password updated!</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="card border-0 shadow-sm rounded-4 border-danger">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold mb-1 text-danger">Delete Account</h5>
                <p class="text-muted small mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. This action is irreversible.</p>

                <button type="button" class="btn btn-outline-danger px-4 fw-medium rounded-pill" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    Delete Account
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body p-4 p-md-5">
                        <h5 class="fw-bold text-dark mb-3">Are you sure you want to delete your account?</h5>
                        <p class="text-muted small mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>

                        <div class="mb-4">
                            <label class="form-label fw-medium small">Password</label>
                            <input type="password" class="form-control" name="password" required placeholder="Enter your password">
                            @error('password', 'userDeletion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger px-4 fw-medium rounded-pill shadow-sm">Delete Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto-open modal if there are password errors during deletion -->
    @if($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
                myModal.show();
            });
        </script>
    @endif
</body>
</html>
