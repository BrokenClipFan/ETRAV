<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PlaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin/packages', [PackageController::class, 'index'])->name('admin.packages');
Route::post('/admin/package/store', [PackageController::class, 'store'])->name('admin.store.package');
Route::put('/admin/package/{id}/update/', [PackageController::class, 'update'])->name('admin.package.update');
Route::delete('/admin/package/{id}/delete/', [PackageController::class, 'destroy'])->name('admin.package.destroy');

Route::post('/admin/Place/store', [PlaceController::class, 'store'])->name('admin.store.spot');

Route::get('/admin/statistic', function () {
    return view('admin.statistic');
});

Route::get('/admin/bookings', function () {
    return view('admin.bookings');
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
