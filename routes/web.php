<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\PackageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/packages', [PackageController::class, 'index'])->name('admin.packages');

Route::post('/admin/package/create', [PackageController::class, 'store'])->name('admin.store.package');

Route::get('/admin/statistic', function () {
    return view('admin.statistic');
});

Route::get('/admin/bookings', function () {
    return view('admin.bookings');
});
