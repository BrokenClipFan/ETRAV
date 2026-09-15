<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="{{ url()->current() }}">
            <i class="bi bi-shield-lock-fill"></i>
            ETRAV Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/statistic') ? 'active fw-semibold' : '' }}" href="/admin/statistic">
                        <i class="bi bi-graph-up me-1"></i> Statistics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/bookings') ? 'active fw-semibold' : '' }}" href="/admin/bookings">
                        <i class="bi bi-journal-check me-1"></i> Bookings
                        @php $pendingAdminCount = \App\Models\Booking::where('status', 'pending')->count(); @endphp
                        @if($pendingAdminCount > 0)
                            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.7rem;">{{ $pendingAdminCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/packages') ? 'active fw-semibold' : '' }}" href="/admin/packages">
                        <i class="bi bi-box-seam me-1"></i> Packages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.transport.*') || request()->is('admin/transport') ? 'active fw-semibold' : '' }}" href="{{ route('admin.transport.index') }}">
                        <i class="bi bi-car-front-fill me-1"></i> Transport
                    </a>
                </li>
            </ul>
            <div class="dropdown">
                <button class="btn btn-dark border-0 d-flex align-items-center gap-2 dropdown-toggle" type="button" id="adminMenu" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i> Admin
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
