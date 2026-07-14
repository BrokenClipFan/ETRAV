<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PlaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/packages', [PackageController::class, 'index'])->name('packages');
    Route::post('/package/store', [PackageController::class, 'store'])->name('store.package');
    Route::put('/package/{id}/update/', [PackageController::class, 'update'])->name('package.update');
    Route::delete('/package/{id}/delete/', [PackageController::class, 'destroy'])->name('package.destroy');

    Route::post('Place/store', [PlaceController::class, 'store'])->name('store.spot');

    Route::get('statistic', function () {
        return view('statistic');
    });

    Route::get('bookings', function () {
        return view('bookings');
    });

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
