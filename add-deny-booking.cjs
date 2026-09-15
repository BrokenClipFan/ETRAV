const fs = require('fs');

// 1. Update AdminBookingController
let controllerPath = 'app/Http/Controllers/Admin/AdminBookingController.php';
let controllerText = fs.readFileSync(controllerPath, 'utf8');

let newFunction = `    public function deny($id) {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'cancelled',
            'notify' => true
        ]);

        // Just to be safe, if vehicle was assigned, make it available
        if ($booking->vehicle) {
            $booking->vehicle->update(['status' => 'available']);
        }
        
        return redirect()->route('admin.bookings')->with('success', 'Booking Denied.');
    }

    public function markComplete($id) {`;

controllerText = controllerText.replace(/    public function markComplete\(\$id\) {/, newFunction);
fs.writeFileSync(controllerPath, controllerText);

// 2. Update routes
let routePath = 'routes/web.php';
let routeText = fs.readFileSync(routePath, 'utf8');
routeText = routeText.replace(/Route::post\('\/booking\/\{id\}\/update', \[AdminBookingController::class, 'update'\]\)->name\('booking\.update'\);/, `Route::post('/booking/{id}/update', [AdminBookingController::class, 'update'])->name('booking.update');\n    Route::post('/booking/{id}/deny', [AdminBookingController::class, 'deny'])->name('booking.deny');`);
fs.writeFileSync(routePath, routeText);

// 3. Update view
let viewPath = 'resources/views/admin/booking-details.blade.php';
let viewText = fs.readFileSync(viewPath, 'utf8');

let denyButton = `                    <form action="{{ route('admin.booking.deny', $booking->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-x-circle me-1"></i> Deny Booking</button>
                    </form>
                    <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" class="m-0">`;

viewText = viewText.replace(/<form action="\{\{ route\('admin\.booking\.update', \$booking->id\) \}\}" method="POST" class="m-0">/, denyButton);
fs.writeFileSync(viewPath, viewText);

console.log('done');
