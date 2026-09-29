<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Transport Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f4f6f9;
        }

        .main-admin-wrapper {
            height: calc(100vh - 65px);
        }

        .scrollable-panel {
            height: 100%;
            overflow-y: auto;
        }

        .vehicle-item-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #e3e8ef;
        }

        .vehicle-item-card:hover,
        .vehicle-item-card.active {
            border-color: #0d6efd !important;
            background-color: #f8fafc !important;
        }

        .img-upload-box {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .img-upload-box:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .vehicle-img-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 5;
        }

        .gallery-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        @media (max-width: 767.98px) {

            html,
            body {
                overflow: auto;
                height: auto;
            }

            .main-admin-wrapper {
                height: auto;
            }

            .scrollable-panel {
                height: auto;
                overflow-y: visible;
            }
        }
    </style>
</head>

<body>

    @include('admin.layouts.nav')

    <div class="container-fluid main-admin-wrapper">
        <div class="row h-100 g-0">

            <!-- COLUMN 1: FLEET LIST -->
            <div class="col-12 col-md-3 bg-white border-end h-100 scrollable-panel p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-car-front-fill text-primary me-2"></i>Fleet List
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="resetForm()">
                        <i class="bi bi-plus-lg me-1"></i> New
                    </button>
                </div>

                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" placeholder="Search vehicle...">
                </div>

                <div class="d-flex flex-column gap-2" id="fleetContainer">
                    @forelse($vehicles ?? [] as $vehicle)
                        <div class="bg-white rounded-3 p-3 shadow-sm vehicle-item-card"
                            data-vehicle="{{ json_encode($vehicle) }}" onclick="selectVehicle(this)">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="fw-bold text-dark">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                @if ($vehicle->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i
                                            class="bi bi-check-circle"></i> Active</span>
                                @elseif($vehicle->status === 'maintenance')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i
                                            class="bi bi-wrench"></i> Maint.</span>
                                @elseif($vehicle->status === 'unavailable')
                                    <span class="badge bg-dark-subtle text-dark border border-dark-subtle"><i
                                            class="bi bi-slash-circle"></i> Unavail.</span>
                                @else
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i
                                            class="bi bi-dash-circle"></i> Retired</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted small font-monospace">Plate: {{ $vehicle->plate_number }}</span>
                                <div class="d-flex gap-2">
                                    <span class="text-secondary small fw-medium"><i
                                            class="bi bi-people-fill me-1"></i>{{ $vehicle->capacity }} Pax</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-car-front fs-3 d-block mb-2 opacity-50"></i>
                            No vehicles found.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- COLUMN 2: ADD/EDIT FORM -->
            <div class="col-12 col-md-4 bg-white border-end h-100 scrollable-panel p-4">
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-1" id="formHeader">✨ Register Vehicle</h5>
                    <p class="text-muted small">Input specifications and operational details.</p>
                </div>

                <form id="vehicleForm" action="{{ route('admin.transport.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="methodContainer"></div>
                    <input type="hidden" name="vehicle_id" id="vehicleId">

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold mb-1">Brand</label>
                            <input type="text" class="form-control rounded-3" name="brand"
                                placeholder="e.g., Toyota" value="{{ old('brand') }}" required>
                            @error('brand')
                                <span class="text-danger small" style="font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold mb-1">Model</label>
                            <input type="text" class="form-control rounded-3" name="model"
                                placeholder="e.g., Hiace" value="{{ old('model') }}" required>
                            @error('model')
                                <span class="text-danger small" style="font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold mb-1">Plate Number</label>
                            <input type="text" class="form-control rounded-3 text-uppercase font-monospace"
                                name="plate_number" placeholder="ABC 1234" value="{{ old('plate_number') }}" required>
                            @error('plate_number')
                                <span class="text-danger small" style="font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold mb-1">Max Capacity</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white rounded-start-3"><i
                                        class="bi bi-people"></i></span>
                                <input type="number" class="form-control rounded-end-3" name="capacity" min="1"
                                    value="{{ old('capacity', 4) }}" required>
                            </div>
                            @error('capacity')
                                <span class="text-danger small" style="font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>



                    <div class="row g-2 mb-4">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold mb-1">Operational Status</label>
                            <select class="form-select rounded-3" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>🟢 Active
                                </option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>🟡
                                    Maint.</option>
                                <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>⚫
                                    Unavail.</option>
                                <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>⚪ Retired
                                </option>
                            </select>
                            @error('status')
                                <span class="text-danger small" style="font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Verification Photos</h6>

                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 11px;">Front
                                View</label>
                            <label class="img-upload-box {{ $errors->has('front_image') ? 'border-danger' : '' }}">
                                <input type="file" name="front_image" class="d-none" accept="image/*">
                                <i class="bi bi-camera fs-4 text-secondary mb-1"></i>
                                <span class="small text-muted" style="font-size: 10px;">Upload</span>
                            </label>
                            @error('front_image')
                                <div class="text-danger fw-medium mt-1 text-center"
                                    style="font-size: 10px; line-height: 1.1;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 11px;">Side
                                View</label>
                            <label class="img-upload-box {{ $errors->has('side_image') ? 'border-danger' : '' }}">
                                <input type="file" name="side_image" class="d-none" accept="image/*">
                                <i class="bi bi-car-front fs-4 text-secondary mb-1"></i>
                                <span class="small text-muted" style="font-size: 10px;">Upload</span>
                            </label>
                            @error('side_image')
                                <div class="text-danger fw-medium mt-1 text-center"
                                    style="font-size: 10px; line-height: 1.1;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 11px;">Plate
                                Number</label>
                            <label class="img-upload-box {{ $errors->has('plate_image') ? 'border-danger' : '' }}">
                                <input type="file" name="plate_image" class="d-none" accept="image/*">
                                <i class="bi bi-123 fs-4 text-secondary mb-1"></i>
                                <span class="small text-muted" style="font-size: 10px;">Upload</span>
                            </label>
                            @error('plate_image')
                                <div class="text-danger fw-medium mt-1 text-center"
                                    style="font-size: 10px; line-height: 1.1;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm"><i
                                class="bi bi-save me-1"></i> Save Vehicle Data</button>
                    </div>
                </form>
            </div>

            <!-- COLUMN 3: VEHICLE PROFILE & GALLERY -->
            <div class="col-12 col-md-5 bg-light h-100 scrollable-panel p-4 position-relative">

                <div id="noVehicleSelected"
                    class="h-100 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-white p-4 rounded-circle shadow-sm border mb-3">
                        <i class="bi bi-car-front fs-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No Vehicle Selected</h5>
                    <p class="text-muted small">Select a vehicle from the fleet list to view its profile and gallery,
                        or register a new one.</p>
                </div>

                <div id="vehicleProfile" class="d-none">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <span class="badge bg-success mb-2 px-3 py-1 rounded-pill"><i
                                    class="bi bi-check-circle me-1"></i> Active</span>
                            <h3 class="fw-bold text-dark mb-0">Toyota Hiace Commuter</h3>
                            <span class="text-muted font-monospace fs-5">ABC 1234</span>
                        </div>
                        <div class="d-flex gap-3 text-end">
                            <div>
                                <div class="fs-5 fw-bold text-primary" id="profileMaxPax">14</div>
                                <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">Max Pax</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div id="gallery-front-view"
                                    class="bg-light text-center p-5 text-muted d-flex flex-column align-items-center justify-content-center"
                                    style="height: 250px;">
                                    <i class="bi bi-image fs-1 opacity-25"></i>
                                    <div class="mt-2 small">Front View Image</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div id="gallery-side-view"
                                    class="bg-light text-center p-4 text-muted d-flex flex-column align-items-center justify-content-center"
                                    style="height: 160px;">
                                    <i class="bi bi-image fs-2 opacity-25"></i>
                                    <div class="mt-2 small" style="font-size: 11px;">Side View</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div id="gallery-plate-view"
                                    class="bg-light text-center p-4 text-muted d-flex flex-column align-items-center justify-content-center"
                                    style="height: 160px;">
                                    <i class="bi bi-image fs-2 opacity-25"></i>
                                    <div class="mt-2 small" style="font-size: 11px;">Plate Number Close-up</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-4 p-4 shadow-sm border mt-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Recent
                            Maintenance Logs</h6>
                        <div class="text-center py-3 text-muted small font-italic">
                            No logs recorded for this vehicle yet.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview logic
        document.querySelectorAll('.img-upload-box input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    const box = this.closest('.img-upload-box');

                    reader.onload = function(e) {
                        let img = box.querySelector('.vehicle-img-preview');
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'vehicle-img-preview';
                            box.appendChild(img);
                        }
                        img.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        let activeVehicle = null;
        const formElement = document.getElementById('vehicleForm');
        const defaultAction = formElement.action; // Store default route('admin.transport.store')

        // Listen for brand/model/plate changes to trigger "New Vehicle" mode
        document.querySelector('input[name="brand"]').addEventListener('input', checkModifications);
        document.querySelector('input[name="model"]').addEventListener('input', checkModifications);
        document.querySelector('input[name="plate_number"]').addEventListener('input', checkModifications);

        function checkModifications() {
            if (!activeVehicle) return; // if creating new from scratch, ignore

            const currentBrand = document.querySelector('input[name="brand"]').value.trim();
            const currentModel = document.querySelector('input[name="model"]').value.trim();
            const currentPlate = document.querySelector('input[name="plate_number"]').value.trim();

            if (currentBrand !== activeVehicle.brand || currentModel !== activeVehicle.model || currentPlate !==
                activeVehicle.plate_number) {
                // Treated as creating a new vehicle because brand/model/plate changed
                formElement.action = defaultAction;
                document.getElementById('methodContainer').innerHTML = ''; // POST
                document.getElementById('formHeader').innerHTML =
                    '✨ Register New Vehicle <span class="badge bg-primary text-white ms-2" style="font-size:10px;">Duplicated</span>';
                document.getElementById('vehicleId').value = '';
                toggleImageRequirement(true);
            } else {
                // Restored back to original data, so back to Edit Mode
                formElement.action = defaultAction.replace(/\/$/, '') + '/' + activeVehicle.id;
                document.getElementById('methodContainer').innerHTML =
                    '<input type="hidden" name="_method" value="PUT">'; // PUT
                document.getElementById('formHeader').innerHTML = '✏️ Edit Vehicle';
                document.getElementById('vehicleId').value = activeVehicle.id;
                toggleImageRequirement(false);
            }
        }

        function toggleImageRequirement(isRequired) {
            document.querySelector('input[name="front_image"]').required = isRequired;
            document.querySelector('input[name="side_image"]').required = isRequired;
            document.querySelector('input[name="plate_image"]').required = isRequired;

            document.querySelectorAll('.img-upload-box .text-muted').forEach(span => {
                if (isRequired) {
                    span.innerHTML = 'Upload <span class="text-danger">*</span>';
                } else {
                    span.innerHTML = 'Upload <span class="text-secondary">(Optional)</span>';
                }
            });
        }

        function selectVehicle(element) {
            const vehicle = JSON.parse(element.getAttribute('data-vehicle'));
            activeVehicle = vehicle;

            // Update active state in list
            document.querySelectorAll('.vehicle-item-card').forEach(card => card.classList.remove('active'));
            element.classList.add('active');

            // Switch layout in column 3
            document.getElementById('noVehicleSelected').classList.add('d-none');
            document.getElementById('vehicleProfile').classList.remove('d-none');

            // Switch layout in column 2 (Form)
            document.getElementById('formHeader').innerHTML = '✏️ Edit Vehicle';

            // Set form action to the update route (/admin/transport/{id})
            formElement.action = defaultAction.replace(/\/$/, '') + '/' + vehicle.id;
            document.getElementById('methodContainer').innerHTML =
                '<input type="hidden" name="_method" value="PUT">'; // PUT

            // Populate form fields
            document.getElementById('vehicleId').value = vehicle.id;
            document.querySelector('input[name="brand"]').value = vehicle.brand;
            document.querySelector('input[name="model"]').value = vehicle.model;
            document.querySelector('input[name="plate_number"]').value = vehicle.plate_number;
            document.querySelector('input[name="capacity"]').value = vehicle.capacity;
            document.querySelector('select[name="status"]').value = vehicle.status;

            // In edit mode, images are not required
            toggleImageRequirement(false);

            // Populate vehicle profile (Column 3)
            const profileHeader = document.querySelector('#vehicleProfile h3');
            profileHeader.innerText = vehicle.brand + ' ' + vehicle.model;

            const profilePlate = document.querySelector('#vehicleProfile .font-monospace');
            profilePlate.innerText = vehicle.plate_number;

            const profileCapacity = document.getElementById('profileMaxPax');
            profileCapacity.innerText = vehicle.capacity;

            // Profile status badge
            const profileStatus = document.querySelector('#vehicleProfile .badge');
            let badgeClass = 'bg-secondary';
            let badgeHtml = '<i class="bi bi-dash-circle me-1"></i> Retired';

            if (vehicle.status === 'active') {
                badgeClass = 'bg-success';
                badgeHtml = '<i class="bi bi-check-circle me-1"></i> Active';
            } else if (vehicle.status === 'maintenance') {
                badgeClass = 'bg-warning';
                badgeHtml = '<i class="bi bi-wrench me-1"></i> Maint.';
            } else if (vehicle.status === 'unavailable') {
                badgeClass = 'bg-dark';
                badgeHtml = '<i class="bi bi-slash-circle me-1"></i> Unavail.';
            }

            profileStatus.className = 'badge mb-2 px-3 py-1 rounded-pill ' + badgeClass;
            profileStatus.innerHTML = badgeHtml;

            // Load gallery images if available
            const assetBaseUrl = "{{ asset('storage') }}/";

            const frontView = document.getElementById('gallery-front-view');
            const sideView = document.getElementById('gallery-side-view');
            const plateView = document.getElementById('gallery-plate-view');

            // Reset image previews in column 3
            frontView.innerHTML =
                `<i class="bi bi-image fs-1 opacity-25"></i><div class="mt-2 small">Front View Image</div>`;
            sideView.innerHTML =
                `<i class="bi bi-image fs-2 opacity-25"></i><div class="mt-2 small" style="font-size: 11px;">Side View</div>`;
            plateView.innerHTML =
                `<i class="bi bi-image fs-2 opacity-25"></i><div class="mt-2 small" style="font-size: 11px;">Plate Number Close-up</div>`;

            if (vehicle.front_image_path) {
                frontView.innerHTML =
                    `<img src="${assetBaseUrl}${vehicle.front_image_path}" class="gallery-img w-100 h-100 object-fit-cover">`;
            }
            if (vehicle.side_image_path) {
                sideView.innerHTML =
                    `<img src="${assetBaseUrl}${vehicle.side_image_path}" class="gallery-img w-100 h-100 object-fit-cover">`;
            }
            if (vehicle.plate_image_path) {
                plateView.innerHTML =
                    `<img src="${assetBaseUrl}${vehicle.plate_image_path}" class="gallery-img w-100 h-100 object-fit-cover">`;
            }

            // Remove upload previews from the form
            document.querySelectorAll('.vehicle-img-preview').forEach(img => img.remove());
        }

        function resetForm() {
            activeVehicle = null;
            document.querySelectorAll('.vehicle-item-card').forEach(card => card.classList.remove('active'));
            formElement.reset();
            formElement.action = defaultAction;
            document.getElementById('methodContainer').innerHTML = ''; // Remove PUT method for creating new
            document.getElementById('vehicleId').value = '';

            // Remove image previews
            document.querySelectorAll('.vehicle-img-preview').forEach(img => img.remove());

            toggleImageRequirement(true);

            document.getElementById('formHeader').innerHTML = '✨ Register Vehicle';

            document.getElementById('noVehicleSelected').classList.remove('d-none');
            document.getElementById('vehicleProfile').classList.add('d-none');
        }

        // Initialize as create mode
        toggleImageRequirement(true);
    </script>
</body>

</html>
