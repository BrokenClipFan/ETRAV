<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stop Categories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    @include('admin.layouts.nav')

    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-0"><i class="bi bi-tags-fill text-primary me-2"></i>Stop Categories</h3>
                <p class="text-muted small mb-0 mt-1">Manage dynamic stop categories and their PNG icons.</p>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4">Icon</th>
                            <th>Name</th>
                            <th>Created</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="ps-4">
                                    @if($category->icon_path)
                                        <img src="{{ asset('storage/' . $category->icon_path) }}" alt="{{ $category->name }}" class="rounded shadow-sm" style="width: 32px; height: 32px; object-fit: contain;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold">{{ $category->name }}</h6>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $category->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border text-primary me-2 rounded-3" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border text-danger rounded-3" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-secondary">Category Name</label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ $category->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-secondary">Icon (PNG only)</label>
                                                    <input type="file" name="icon" class="form-control rounded-3" accept="image/png">
                                                    <div class="form-text">Leave blank to keep existing icon.</div>
                                                    @if($category->icon_path)
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/' . $category->icon_path) }}" width="40" class="rounded border p-1 bg-light">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                                <button type="button" class="btn btn-light rounded-pill w-100 mb-2" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary rounded-pill w-100 m-0">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-body text-center p-4">
                                            <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="fw-bold mb-2">Delete Category?</h5>
                                            <p class="text-muted small mb-4">This action cannot be undone.</p>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-light rounded-pill w-50" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger rounded-pill w-50">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-tags fs-1 d-block mb-2 opacity-50"></i>
                                    No categories found. Create one to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Category Name</label>
                            <input type="text" name="name" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Icon (PNG only)</label>
                            <input type="file" name="icon" class="form-control rounded-3" accept="image/png" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill w-100 mb-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill w-100 m-0">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
