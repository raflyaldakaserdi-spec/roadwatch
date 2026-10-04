<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Detection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DetectionApiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'severity' => 'nullable|in:Low,Medium,High',
            'image' => 'nullable|string', // URL Gambar / Base64
        ]);

        $now = Carbon::now();
        $latestId = Detection::max('id') + 1;
        $detectionCode = 'PTH-' . str_pad($latestId, 4, '0', STR_PAD_LEFT);

        $detection = Detection::create([
            'detection_code' => $detectionCode,
            'image' => $validated['image'] ?? 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'detection_date' => $now->format('Y-m-d'),
            'detection_day' => $now->format('l'),
            'detection_time' => $now->format('H:i:s'),
            'status' => 'Detected',
            'severity' => $validated['severity'] ?? 'Medium',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data deteksi pothole berhasil diterima dari ESP32',
            'data' => $detection
        ], 201);
    }
}