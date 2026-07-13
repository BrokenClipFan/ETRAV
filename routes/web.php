<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin.home');
});

Route::get('/admin/statistic', function () {
    return view('admin.statistic');
});

Route::get('/admin/bookings', function () {
    return view('admin.bookings');
});
