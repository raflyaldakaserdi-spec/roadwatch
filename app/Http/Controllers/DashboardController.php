<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalDetections = Detection::count();
        $detectionsToday = Detection::whereDate('detection_date', $today)->count();
        $detectionsThisWeek = Detection::where('detection_date', '>=', $startOfWeek)->count();
        $detectionsThisMonth = Detection::where('detection_date', '>=', $startOfMonth)->count();

        $recentDetections = Detection::latest()->take(5)->get();

        return view('dashboard', compact(
            'totalDetections',
            'detectionsToday',
            'detectionsThisWeek',
            'detectionsThisMonth',
            'recentDetections'
        ));
    }

    public function deviceInfo()
{
    // Dummy Status Hardware IoT
    $deviceStatus = [
        'device_name' => 'ESP32 Pothole Detector #01',
        'mcu' => 'ESP32-WROOM-32',
        'camera_module' => 'ESP32-CAM (OV2640)',
        'gps_module' => 'NEO-6M GPS Module',
        'model' => 'FOMO Object Detection (Edge Impulse)',
        'status' => 'Online',
        'last_ping' => now()->format('Y-m-d H:i:s'),
        'api_endpoint' => url('/api/detections'),
    ];

    return view('device-info', compact('deviceStatus'));
}
}