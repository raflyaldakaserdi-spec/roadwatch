<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route Detections
    Route::get('/detections', [DetectionController::class, 'index'])->name('detections.index');
    Route::get('/detections/{id}', [DetectionController::class, 'show'])->name('detections.show');

    // Route Map Monitoring
    Route::get('/map-monitoring', [DetectionController::class, 'map'])->name('map.index');

    // Route Device Info
    Route::get('/device-info', [DashboardController::class, 'deviceInfo'])->name('device.info');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';