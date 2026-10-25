<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Detection;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Throwable;

class DetectionApiController extends Controller
{
    public function store(Request $request)
    {
        // Authenticate registered IoT device.
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized device.',
            ], 401);
        }

        $device = Device::query()
            ->where('status', 'Active')
            ->where('api_token_hash', hash('sha256', $token))
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized device.',
            ], 401);
        }

        // Validate detection metadata and optional JPG upload.
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'severity' => 'nullable|in:Low,Medium,High',
            'image' => 'nullable|string|max:2048',
            'image_file' => [
                'nullable',
                File::types(['jpg', 'jpeg'])->max('1mb'),
            ],
        ]);

        $imagePath = $validated['image']
            ?? '/images/potholes/pothole-01.jpg';

        $uploadedPath = null;

        if ($request->hasFile('image_file')) {
            $uploadedPath = $request->file('image_file')
                ->store('detections', 'public');

            if (!$uploadedPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image upload failed.',
                ], 500);
            }

            $imagePath = Storage::disk('public')->url($uploadedPath);
        }

        try {
            $detection = DB::transaction(function () use (
                $validated,
                $device,
                $imagePath
            ) {
                $now = Carbon::now('Asia/Jakarta');

                $detection = Detection::create([
                    'device_id' => $device->id,
                    'detection_code' => 'PENDING-' . bin2hex(random_bytes(8)),
                    'image' => $imagePath,
                    'location' => $validated['location'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'detection_date' => $now->format('Y-m-d'),
                    'detection_day' => $now->format('l'),
                    'detection_time' => $now->format('H:i:s'),
                    'status' => 'Detected',
                    'severity' => $validated['severity'] ?? 'Medium',
                ]);

                $detection->update([
                    'detection_code' => 'PTH-' .
                        str_pad($detection->id, 4, '0', STR_PAD_LEFT),
                ]);

                return $detection;
            });
        } catch (Throwable $e) {
            // Avoid leaving orphaned uploaded files.
            if ($uploadedPath) {
                Storage::disk('public')->delete($uploadedPath);
            }

            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data deteksi berhasil diterima.',
            'device_code' => $device->device_code,
            'data' => $detection,
        ], 201);
    }
}
