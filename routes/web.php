<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\FacebookAuthController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/custom-package', [App\Http\Controllers\BookingController::class, 'viewCustomPackage'])->name('custom.package.book');

Route::middleware('auth')->group(function() {
    Route::get('/', [BookingController::class, 'index'])->name('home');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

    Route::get('/bookings/', [BookingController::class, 'view'])->name('bookings.view');
    Route::get('/dashboard/', [BookingController::class, 'view'])->name('dashboard');
    Route::post('/bookings/{id}/read', [BookingController::class, 'turnOffNotification']);
    Route::get('/booking/{id}/edit', [BookingController::class, 'editCustomPackage'])->name('booking.edit');
    Route::put('/booking/{id}/update', [BookingController::class, 'updateCustomBooking'])->name('booking.update.custom');
    Route::get('/booking/{id}/payment', [BookingController::class, 'paymentPage'])->name('booking.payment');
    Route::post('/booking/{id}/pay', [BookingController::class, 'payDeposit']);
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/package/{id}', [BookingController::class, 'viewPackage'])->name('package.book');

});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings');
    Route::get('/booking/{id}', [AdminBookingController::class, 'show'])->name('booking.show');
    Route::post('/booking/{id}/update', [AdminBookingController::class, 'update'])->name('booking.update');
    Route::post('/booking/{id}/set-price', [AdminBookingController::class, 'setPrice'])->name('booking.set-price');
    Route::post('/booking/{id}/deny', [AdminBookingController::class, 'deny'])->name('booking.deny');
    Route::post('/booking/{id}/update/completed', [AdminBookingController::class, 'markComplete'])->name('booking.update.complete');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages');
    Route::post('/package/store', [PackageController::class, 'store'])->name('store.package');
    Route::put('/package/{id}/update/', [PackageController::class, 'update'])->name('package.update');
    Route::delete('/package/{id}/delete/', [PackageController::class, 'destroy'])->name('package.destroy'); 

    Route::post('place/store', [PlaceController::class, 'store'])->name('store.spot');
    Route::put('place/update/{id}', [PlaceController::class, 'update'])->name('spot.update');
    Route::delete('/admin/spots/{id}', [PlaceController::class, 'destroy'])->name('spot.destroy');
    
    Route::get('/statistic', [AdminBookingController::class, 'statistics'])->name('statistic');

    Route::get('/calendar', [AdminBookingController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/events', [AdminBookingController::class, 'calendarEvents'])->name('calendar.events');

    Route::resource('/transport', TransportController::class);
    Route::resource('/categories', CategoryController::class)->except(['create', 'show', 'edit']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/facebook', [FacebookAuthController::class, 'redirect'])
    ->name('facebook.login');

Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback']);

require __DIR__.'/auth.php';
