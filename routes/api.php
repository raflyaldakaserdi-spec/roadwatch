<?php

use App\Http\Controllers\Api\DetectionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/detections', [DetectionApiController::class, 'store'])
    ->middleware('throttle:30,1');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/devices/heartbeat',
    [\App\Http\Controllers\Api\DeviceHeartbeatController::class, 'heartbeat']
)->middleware('throttle:30,1');
