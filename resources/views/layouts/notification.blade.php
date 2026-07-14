@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-sm rounded-3" role="alert" style="max-width: 350px; z-index: 9999;">
        <div class="d-flex align-items-center gap-2">
            <span>✅</span>
            <div class="small fw-medium">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-sm rounded-3" role="alert" style="max-width: 350px; z-index: 9999;">
        <div class="d-flex align-items-center gap-2">
            <span>❌</span>
            <div class="small fw-medium">{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif