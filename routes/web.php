<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::get('/', [BookingController::class, 'index'])->name('home');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

    Route::get('/bookings/', [BookingController::class, 'view'])->name('bookings.view');
    Route::get('/dashboard/', [BookingController::class, 'view'])->name('dashboard');
    Route::post('/bookings/{id}/read', [BookingController::class, 'turnOffNotification']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings');
    Route::post('/booking/{id}/update', [AdminBookingController::class, 'update'])->name('booking.update');
    Route::post('/booking/{id}/update/completed', [AdminBookingController::class, 'markComplete'])->name('booking.update.complete');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages');
    Route::post('/package/store', [PackageController::class, 'store'])->name('store.package');
    Route::put('/package/{id}/update/', [PackageController::class, 'update'])->name('package.update');
    Route::delete('/package/{id}/delete/', [PackageController::class, 'destroy'])->name('package.destroy'); 

    Route::post('place/store', [PlaceController::class, 'store'])->name('store.spot');
    Route::put('place/update/{id}', [PlaceController::class, 'update'])->name('spot.update');
    Route::delete('/admin/spots/{id}', [PlaceController::class, 'destroy'])->name('spot.destroy');
    
    Route::get('statistic', function () {
        return view('statistic');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
