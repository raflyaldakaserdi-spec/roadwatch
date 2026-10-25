<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceHeartbeatController extends Controller
{
    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'device_code' => 'required|string|max:100',
            'gps_status' => 'nullable|in:Fix,No Fix,Unknown',
            'firmware_version' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $device = Device::where(
            'device_code',
            $validated['device_code']
        )->first();

        $token = $request->bearerToken();

        if (
            !$device ||
            $device->status !== 'Active' ||
            !$device->api_token_hash ||
            !$token ||
            !hash_equals(
                $device->api_token_hash,
                hash('sha256', $token)
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized device.',
            ], 401);
        }

        $updates = [
            'last_seen_at' => now(),
        ];

        foreach ([
            'gps_status',
            'firmware_version',
            'latitude',
            'longitude',
        ] as $field) {
            if (array_key_exists($field, $validated)) {
                $updates[$field] = $validated[$field];
            }
        }

        $device->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat received.',
            'device_code' => $device->device_code,
            'connection_status' => 'Online',
            'last_seen_at' => $device->last_seen_at?->toIso8601String(),
        ]);
    }
}
