<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Statistics & Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f6f9; font-family: system-ui, sans-serif; }
        .stat-card { transition: transform 0.2s; border: none; border-radius: 1rem; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 24px; }
    </style>
</head>
<body>
    <div class="no-print">
        @include('admin.layouts.nav')
    </div>

    <div class="container-fluid p-4">
        <div class="mb-4">
            <h4 class="fw-bold text-dark mb-1">📈 Business Analytics</h4>
            <p class="text-muted small mb-0">Overview of system performance, financials, and popular packages.</p>
        </div>

        <!-- Top Overview Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Revenue -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card stat-card bg-white h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="icon-box bg-success-subtle text-success me-3">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Revenue</span>
                            <h3 class="fw-bold text-dark mb-0">₱{{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Bookings -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card stat-card bg-white h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="icon-box bg-primary-subtle text-primary me-3">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Bookings</span>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($totalBookings) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Itineraries -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card stat-card bg-white h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="icon-box bg-danger-subtle text-danger me-3">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Custom Itineraries</span>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($customBookingsCount) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Charts -->
            <div class="col-12 col-xl-8">
                <!-- Status Distribution Chart -->
                <div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Booking Status Distribution</h6>
                    </div>
                    <div class="card-body p-4">
                        <div style="height: 300px; width: 100%; display: flex; justify-content: center;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Table -->
                <div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Recent Bookings</h6>
                        <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th class="ps-4 fw-medium border-0 py-3">Client</th>
                                        <th class="fw-medium border-0 py-3">Date</th>
                                        <th class="fw-medium border-0 py-3">Status</th>
                                        <th class="pe-4 fw-medium border-0 py-3 text-end">Amount Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentBookings as $booking)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="fw-bold text-dark">{{ $booking->user->name ?? 'Unknown' }}</div>
                                                <div class="small text-muted">{{ $booking->package->name ?? 'Custom Itinerary' }}</div>
                                            </td>
                                            <td class="py-3 text-muted small">{{ $booking->created_at->format('M d, Y') }}</td>
                                            <td class="py-3">
                                                @if($booking->status === 'pending')
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">🟡 Pending Approval</span>
                                                @elseif($booking->status === 'approved')
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">🔵 Awaiting Payment</span>
                                                @elseif($booking->status === 'confirmed')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">🟢 Confirmed</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2">⚫ {{ ucfirst($booking->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 py-3 text-end fw-bold text-success">
                                                ₱{{ number_format($booking->amount_paid, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No recent bookings found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Top Packages & Places -->
            <div class="col-12 col-xl-4">
                
                <!-- Top Packages -->
                <div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Most Popular Packages</h6>
                    </div>
                    <div class="card-body p-4">
                        @forelse($packages as $index => $pkg)
                            <div class="d-flex align-items-center mb-4 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 32px; height: 32px; font-weight: bold; font-size: 14px;">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $pkg->name }}</h6>
                                    <small class="text-muted">{{ $pkg->bookings_count }} Bookings</small>
                                </div>
                                @if(isset($pkg->image_path))
                                    <img src="{{ $pkg->image_path }}" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted shadow-sm" style="width: 48px; height: 48px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-50"></i>
                                No package data available.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Places -->
                <div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Most Visited Destinations</h6>
                    </div>
                    <div class="card-body p-4">
                        @forelse($popularPlaces as $index => $place)
                            <div class="d-flex align-items-center mb-4 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 32px; height: 32px; font-weight: bold; font-size: 14px;">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $place->name }}</h6>
                                    <small class="text-muted">Visited {{ $place->booking_places_count }} times</small>
                                </div>
                                @if(isset($place->image_path))
                                    <img src="{{ $place->image_path }}" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted shadow-sm" style="width: 48px; height: 48px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-geo fs-1 d-block mb-2 opacity-50"></i>
                                No place data available.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js Configuration
            const ctx = document.getElementById('statusChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending Approval', 'Awaiting Payment', 'Confirmed / Paid', 'Completed'],
                    datasets: [{
                        data: [
                            {{ $pendingCount }}, 
                            {{ $approvedCount }}, 
                            {{ $confirmedCount }}, 
                            {{ $completedCount }}
                        ],
                        backgroundColor: [
                            '#ffc107', // Warning (Pending)
                            '#0dcaf0', // Info (Approved)
                            '#198754', // Success (Confirmed)
                            '#6c757d'  // Secondary (Completed)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    family: 'system-ui, sans-serif',
                                    size: 12
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
</body>
</html>
