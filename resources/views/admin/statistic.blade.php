<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Intelligence Hub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .metric-stripe {
            border-left: 4px solid #0d6efd;
        }
        .stripe-success { border-left-color: #198754; }
        .stripe-warning { border-left-color: #ffc107; }
        .stripe-info { border-left-color: #0dcaf0; }
        
        .progress-thin {
            height: 6px;
        }
        .analytics-viewport {
            position: relative;
            height: 280px;
            width: 100%;
        }
        .matrix-row:hover {
            background-color: #f8f9fa !important;
        }
    </style>
</head>
<body>
    @include('admin.layouts.nav')

    <!-- SIDEBAR & MAIN WRAPPER COMPACT GRID -->
    <div class="container-fluid py-4 px-md-4">
        
        <!-- INTEL HEADER SECTION -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-12 col-md-7">
                <span class="badge bg-dark-subtle text-dark border mb-2 px-2 py-1 uppercase tracking-wider font-monospace" style="font-size: 10px;">System Analytics Engine</span>
                <h4 class="fw-bold text-neutral mb-1">📊 Platform Performance & Data Matrix</h4>
                <p class="text-muted small mb-0">Real-time metrics tracking travel agency operations, optimization logs, and routing loads.</p>
            </div>
            <div class="col-12 col-md-5 text-md-end">
                <div class="btn-group shadow-sm">
                    <button class="btn btn-white border bg-white text-dark btn-sm fw-medium"><i class="bi bi-calendar3 me-2"></i>Filter Timeline</button>
                    <button class="btn btn-primary btn-sm fw-medium"><i class="bi bi-arrow-clockwise me-1"></i> Refresh Logs</button>
                </div>
            </div>
        </div>

        <!-- SECTION 1: SYSTEM EFFICIENCY STRIPES (Replaces Standard Metric Cards) -->
        <div class="row g-3 mb-4">
            <!-- Metric 1 -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-3 shadow-sm metric-stripe">
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 11px;">Gross Platform Billings</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 font-monospace">₱142,500</h3>
                        <span class="text-success small fw-medium text-nowrap" style="font-size: 12px;"><i class="bi bi-caret-up-fill"></i> 12%</span>
                    </div>
                </div>
            </div>
            <!-- Metric 2 -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-3 shadow-sm metric-stripe stripe-success">
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 11px;">Successful Bookings</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 font-monospace">84</h3>
                        <span class="text-muted small text-nowrap" style="font-size: 12px;">Completed Runs</span>
                    </div>
                </div>
            </div>
            <!-- Metric 3 -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-3 shadow-sm metric-stripe stripe-warning">
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 11px;">Average Capacity Load</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 font-monospace">5.4 <span class="fs-6 fw-normal text-muted">Pax</span></h3>
                        <span class="text-warning small fw-medium text-nowrap" style="font-size: 12px;">Optimal Van Size</span>
                    </div>
                </div>
            </div>
            <!-- Metric 4 -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-3 shadow-sm metric-stripe stripe-info">
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 11px;">Operational Catalog</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 font-monospace">12 <span class="fs-6 fw-normal text-muted">Active</span></h3>
                        <span class="text-info small text-nowrap" style="font-size: 12px;">45 Pinned Nodes</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: CHARTS MATRIX LAYOUT -->
        <div class="row g-4 mb-4">
            <!-- Chart Block 1: Revenue Timeline Split -->
            <div class="col-12 col-xl-7">
                <div class="bg-white border rounded-3 p-4 shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">📉 Gross Billings Output</h6>
                            <p class="text-muted small mb-0">Timeline variance mapping platform transactions over time.</p>
                        </div>
                    </div>
                    <div class="analytics-viewport">
                        <canvas id="billingsMatrixChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Block 2: Target Segment Share Bar Chart -->
            <div class="col-12 col-xl-5">
                <div class="bg-white border rounded-3 p-4 shadow-sm h-100">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">🔥 Category Demand Performance</h6>
                        <p class="text-muted small mb-3">Relative booking allocations based on Package Tags.</p>
                    </div>
                    <div class="analytics-viewport d-flex align-items-center justify-content-center">
                        <canvas id="categoryDemandChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: REVENUE DATA MATRIX (Replaces Standard Tables) -->
        <div class="row">
            <div class="col-12">
                <div class="bg-white border rounded-3 shadow-sm p-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">📍 Destination Node Utilization Matrix</h6>
                            <p class="text-muted small mb-0">Cross-referencing map coordinate locations against consumer selection volume.</p>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm rounded-2 font-monospace" style="font-size: 12px;"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV Export</button>
                    </div>

                    <!-- Progress Matrix Rows -->
                    <div class="d-flex flex-column gap-3 mt-4">
                        
                        <!-- Row Node Item 1 -->
                        <div class="p-3 bg-light border rounded-3 matrix-row">
                            <div class="row align-items-center g-2">
                                <div class="col-12 col-md-4">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> Magellan's Cross
                                    </div>
                                    <span class="text-muted small font-monospace" style="font-size: 11px;">Attached: Cebu Historical Tour</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted d-block small mb-1">Traffic Share</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress progress-thin flex-fill rounded-pill bg-secondary-subtle">
                                            <div class="progress-bar bg-primary rounded-pill" style="width: 75%"></div>
                                        </div>
                                        <span class="small fw-bold font-monospace text-dark" style="font-size: 12px;">75%</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 text-md-center">
                                    <span class="text-muted d-block small mb-1">Yield Value</span>
                                    <span class="fw-bold text-dark font-monospace">₱45,200</span>
                                </div>
                                <div class="col-12 col-md-2 text-md-end">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1"><i class="bi bi-graph-up me-1"></i> Peak</span>
                                </div>
                            </div>
                        </div>

                        <!-- Row Node Item 2 -->
                        <div class="p-3 bg-light border rounded-3 matrix-row">
                            <div class="row align-items-center g-2">
                                <div class="col-12 col-md-4">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> Sirao Flower Garden
                                    </div>
                                    <span class="text-muted small font-monospace" style="font-size: 11px;">Attached: Highland Escape</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted d-block small mb-1">Traffic Share</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress progress-thin flex-fill rounded-pill bg-secondary-subtle">
                                            <div class="progress-bar bg-success rounded-pill" style="width: 55%"></div>
                                        </div>
                                        <span class="small fw-bold font-monospace text-dark" style="font-size: 12px;">55%</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 text-md-center">
                                    <span class="text-muted d-block small mb-1">Yield Value</span>
                                    <span class="fw-bold text-dark font-monospace">₱32,150</span>
                                </div>
                                <div class="col-12 col-md-2 text-md-end">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1"><i class="bi bi-graph-up me-1"></i> Rising</span>
                                </div>
                            </div>
                        </div>

                        <!-- Row Node Item 3 -->
                        <div class="p-3 bg-light border rounded-3 matrix-row">
                            <div class="row align-items-center g-2">
                                <div class="col-12 col-md-4">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> Temple of Leah
                                    </div>
                                    <span class="text-muted small font-monospace" style="font-size: 11px;">Attached: Highland Escape</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted d-block small mb-1">Traffic Share</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress progress-thin flex-fill rounded-pill bg-secondary-subtle">
                                            <div class="progress-bar bg-warning rounded-pill" style="width: 40%"></div>
                                        </div>
                                        <span class="small fw-bold font-monospace text-dark" style="font-size: 12px;">40%</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 text-md-center">
                                    <span class="text-muted d-block small mb-1">Yield Value</span>
                                    <span class="fw-bold text-dark font-monospace">₱28,400</span>
                                </div>
                                <div class="col-12 col-md-2 text-md-end">
                                    <span class="badge bg-secondary-subtle text-muted rounded-pill px-3 py-1"><i class="bi bi-dash-lg me-1"></i> Stable</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart JS Core Engine Initialization Setup dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // 1. Matrix Area Chart (Fills space cleanly with sharp lines)
        const ctxMatrix = document.getElementById('billingsMatrixChart').getContext('2d');
        new Chart(ctxMatrix, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Gross Volume Stream',
                    data: [18000, 24000, 15000, 35000, 48000, 39000, 52000],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    fill: true,
                    tension: 0, // Sharp crisp matrix aesthetic curves
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#f1f3f5' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Horizontal Progress Bar Chart Style Configuration Metrics Layout
        const ctxDemand = document.getElementById('categoryDemandChart').getContext('2d');
        new Chart(ctxDemand, {
            type: 'bar',
            data: {
                labels: ['Popular', 'Best Combo', 'Trending', 'Budget Friendly'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Convert into clean horizontal lines profile bars
                plugins: { legend: { display: false } },
                scales: {
                    x: { max: 50, grid: { color: '#f1f3f5' } },
                    y: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>