<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/detections', [DetectionApiController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
