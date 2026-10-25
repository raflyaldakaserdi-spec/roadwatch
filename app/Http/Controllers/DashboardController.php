<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ROADWATCH_ROLE_DASHBOARD_V1
        $user = auth()->user();

        // ROADWATCH_WORKER_MONITORING_V5
        // Admin, Pekerja, dan Masyarakat menggunakan dashboard monitoring.
        // Penugasan Pekerja tersedia melalui myAssignments().

        // ROADWATCH_PUBLIC_PREMIUM_V1
        // Masyarakat menggunakan dashboard original.
        // Pembatasan data publik akan diterapkan terpisah.

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $startOfWeek = Carbon::now('Asia/Jakarta')->startOfWeek()->toDateString();
        $startOfMonth = Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();

        $totalDetections = Detection::count();
        $detectionsToday = Detection::whereDate('detection_date', $today)->count();
        $detectionsThisWeek = Detection::where('detection_date', '>=', $startOfWeek)->count();
        $detectionsThisMonth = Detection::where('detection_date', '>=', $startOfMonth)->count();

        $previousMonthStart = Carbon::now('Asia/Jakarta')->subMonthNoOverflow()->startOfMonth()->toDateString();
        $previousMonthEnd = Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();

        $detectionsPreviousMonth = Detection::where(
            'detection_date', '>=', $previousMonthStart
        )->where('detection_date', '<', $previousMonthEnd)->count();

        $monthlyGrowth = $detectionsPreviousMonth > 0
            ? round((($detectionsThisMonth - $detectionsPreviousMonth) / $detectionsPreviousMonth) * 100, 1)
            : null;

        $recentDetections = Detection::latest()->take(5)->get();

        $mapDetections = Detection::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id',
                'detection_code',
                'latitude',
                'longitude',
                'location',
                'severity'
            ]);

        $device = \App\Models\Device::where(
            'device_code',
            'RW-DEVICE-001'
        )->first();

        $chartData = $this->chartData();
        $severityData = $this->severityData();
        $trendData = $this->trendData();
        $trendPeriods = $this->trendPeriods();

        return view('dashboard', compact(
            'totalDetections',
            'detectionsToday',
            'detectionsThisWeek',
            'detectionsThisMonth',
            'monthlyGrowth',
            'recentDetections',
            'mapDetections',
            'chartData',
            'severityData',
            'trendData',
            'trendPeriods',
            'device'
        ));
    }

    // ROADWATCH_WORKER_WORKSPACE_V5_METHOD
    public function myAssignments()
    {
        abort_unless(
            auth()->user()?->role === 'pekerja',
            403,
            'Halaman ini khusus Pekerja.'
        );

        $user = auth()->user();

        $tasksQuery = \App\Models\WorkAssignment::query()
            ->where('worker_id', $user->id);

        $taskStats = [
            'total' => (clone $tasksQuery)->count(),
            'assigned' => (clone $tasksQuery)->where('status', 'Assigned')->count(),
            'progress' => (clone $tasksQuery)->where('status', 'In Progress')->count(),
            'review' => (clone $tasksQuery)->where('status', 'Pending Review')->count(),
            'completed' => (clone $tasksQuery)->where('status', 'Completed')->count(),
        ];

        $myTasks = (clone $tasksQuery)
            ->with('detection')
            ->orderByRaw("CASE priority WHEN 'High' THEN 1 WHEN 'Medium' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(8);

        return view('dashboards.worker', compact('taskStats', 'myTasks'));
    }

    public function liveData()
    {
        $device = \App\Models\Device::where(
            'device_code',
            'RW-DEVICE-001'
        )->first();

        $latestDetection = Detection::latest()->first();

        $mapDetections = Detection::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id',
                'detection_code',
                'latitude',
                'longitude',
                'location',
                'severity'
            ]);

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $startOfWeek = Carbon::now('Asia/Jakarta')->startOfWeek()->toDateString();
        $startOfMonth = Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();

        $previousMonthStart = Carbon::now('Asia/Jakarta')->subMonthNoOverflow()->startOfMonth()->toDateString();

        $detectionsPreviousMonth = Detection::where(
            'detection_date', '>=', $previousMonthStart
        )->where('detection_date', '<', $startOfMonth)->count();

        $detectionsCurrentMonth = Detection::where(
            'detection_date', '>=', $startOfMonth
        )->count();

        $monthlyGrowth = $detectionsPreviousMonth > 0
            ? round((($detectionsCurrentMonth - $detectionsPreviousMonth) / $detectionsPreviousMonth) * 100, 1)
            : null;

        return response()->json([
            'charts' => $this->chartData(),
            'severity' => $this->severityData(),
            'trend' => $this->trendData(),
            'trend_periods' => $this->trendPeriods(),
            'stats' => [
                'monthly_growth' => $monthlyGrowth,
                'total' => Detection::count(),
                'today' => Detection::whereDate('detection_date', $today)->count(),
                'week' => Detection::where('detection_date', '>=', $startOfWeek)->count(),
                'month' => Detection::where('detection_date', '>=', $startOfMonth)->count(),
            ],
            'device' => [
                'online' => $device?->is_online ?? false,
                'mcu' => $device?->mcu ?? 'ESP32-S3 CAM',
                'gps_module' => $device?->gps_module ?? 'NEO-6M',
                'gps_status' => ($device?->is_online ?? false)
                    ? ($device?->gps_status ?? 'Unknown')
                    : 'Unknown',
                'last_seen_at' => $device?->last_seen_at?->toIso8601String(),
                'firmware_version' => $device?->firmware_version,
                'latitude' => $device?->latitude,
                'longitude' => $device?->longitude,
            ],
            'latest_detection' => $latestDetection ? [
                'id' => $latestDetection->id,
                'detection_code' => $latestDetection->detection_code,
                'image' => $latestDetection->image,
                'location' => $latestDetection->location,
                'severity' => $latestDetection->severity,
                'latitude' => $latestDetection->latitude,
                'longitude' => $latestDetection->longitude,
                'detection_time' => $latestDetection->detection_time,
                'detail_url' => route('detections.show', $latestDetection->id),
            ] : null,
            'recent_detections' => Detection::latest()->take(5)->get([
                'id',
                'detection_code',
                'location',
                'latitude',
                'longitude',
                'detection_date',
                'detection_time',
                'severity',
                'status',
            ]),
            'map_detections' => $mapDetections,
        ]);
    }


    private function trendData(int $days = 7): array
    {
        if (!in_array($days, [7, 30], true)) {
            $days = 7;
        }

        $today = Carbon::today('Asia/Jakarta');
        $start = $today->copy()->subDays($days - 1);

        $counts = Detection::query()
            ->whereBetween('detection_date', [
                $start->toDateString(),
                $today->toDateString(),
            ])
            ->selectRaw('detection_date, COUNT(*) as total')
            ->groupBy('detection_date')
            ->pluck('total', 'detection_date');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $date = $day->toDateString();

            $labels[] = $day->format('d M');
            $values[] = (int) ($counts[$date] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function trendPeriods(): array
    {
        return [
            '7' => $this->trendData(7),
            '30' => $this->trendData(30),
        ];
    }

    private function severityData(): array
    {
        $counts = Detection::query()
            ->selectRaw('severity, COUNT(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        return [
            'High' => (int) ($counts['High'] ?? 0),
            'Medium' => (int) ($counts['Medium'] ?? 0),
            'Low' => (int) ($counts['Low'] ?? 0),
        ];
    }

    private function chartData(): array
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        $start30 = $now->copy()->subDays(29)->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        $dailyCounts = Detection::query()
            ->whereBetween('detection_date', [
                $start30->toDateString(),
                $today
            ])
            ->selectRaw('detection_date, COUNT(*) as total')
            ->groupBy('detection_date')
            ->pluck('total', 'detection_date');

        $makeDaily = function ($start, $end, $cumulative = false) use ($dailyCounts) {
            $labels = [];
            $values = [];
            $running = 0;

            for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
                $date = $day->toDateString();
                $count = (int) ($dailyCounts[$date] ?? 0);

                $running += $count;

                $labels[] = $day->format('d M');
                $values[] = $cumulative ? $running : $count;
            }

            return [
                'labels' => $labels,
                'values' => $values,
            ];
        };

        $hourCounts = Detection::query()
            ->whereDate('detection_date', $today)
            ->selectRaw('detection_time')
            ->get()
            ->countBy(function ($item) {
                return substr((string) $item->detection_time, 0, 2);
            });

        $todayLabels = [];
        $todayValues = [];

        for ($hour = 0; $hour <= $now->hour; $hour++) {
            $key = sprintf('%02d', $hour);
            $todayLabels[] = $key . ':00';
            $todayValues[] = (int) ($hourCounts[$key] ?? 0);
        }

        $total = $makeDaily($start30, $now, true);

        // Include detections older than the 30-day chart window.
        $previousTotal = Detection::where(
            'detection_date',
            '<',
            $start30->toDateString()
        )->count();

        $total['values'] = array_map(
            fn ($value) => $value + $previousTotal,
            $total['values']
        );

        return [
            'total' => $total,
            'today' => [
                'labels' => $todayLabels,
                'values' => $todayValues,
            ],
            'week' => $makeDaily($weekStart, $now),
            'month' => $makeDaily($monthStart, $now),
        ];
    }

    // RoadWatch Global Device Status API V1
    public function deviceStatusLive()
    {
        $device = \App\Models\Device::where(
            'device_code',
            'RW-DEVICE-001'
        )->first();

        $online = $device?->is_online ?? false;

        return response()->json([
            'online' => $online,
            'mcu' => $device?->mcu ?? 'ESP32-S3 CAM',
            'gps_status' => $online
                ? ($device?->gps_status ?? 'Unknown')
                : 'Unknown',
            'last_seen_at' => $device?->last_seen_at?->toIso8601String(),

            // RoadWatch Device Live Telemetry V2
            'latitude' => $device?->latitude,
            'longitude' => $device?->longitude,
            'firmware_version' => $device?->firmware_version ?? 'Not Reported',
            'last_reported_gps_status' => $device?->gps_status ?? 'Unknown',
        ])->header('Cache-Control', 'no-store, private');
    }

    public function deviceInfo()
    {
        $device = \App\Models\Device::where(
            'device_code',
            'RW-DEVICE-001'
        )->first();

        $deviceStatus = [
            // RoadWatch Device Info V2
            'device_code' => $device?->device_code ?? 'RW-DEVICE-001',
            'firmware_version' => $device?->firmware_version ?? 'Not Reported',
            'device_name' => $device?->name ?? 'RoadWatch Device 01',
            'mcu' => $device?->mcu ?? 'ESP32-S3 CAM',
            'camera_module' => 'ESP32-S3 CAM (Integrated Camera)',
            'gps_module' => $device?->gps_module ?? 'NEO-6M',
            'model' => 'FOMO Object Detection (Edge Impulse)',
            'status' => $device?->is_online ? 'Online' : 'Offline',
            'last_ping' => $device?->last_seen_at?->copy()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') ?? 'Never',
            'gps_status' => $device?->gps_status ?? 'Unknown',
            'latitude' => $device?->latitude,
            'longitude' => $device?->longitude,
            'api_endpoint' => url('/api/detections'),
        ];

        return view('device-info', compact('deviceStatus'));
    }
}
