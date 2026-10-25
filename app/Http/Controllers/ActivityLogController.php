<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderByDesc('created_at')->orderByDesc('id');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        // RoadWatch Activity Overview Premium V2
        // Statistik global seluruh log, tidak bergantung pada pagination.
        $activityStats = [
            'total' => ActivityLog::count(),
            'created' => ActivityLog::where('action', 'created')->count(),
            'status_changes' => ActivityLog::where('action', 'status_updated')->count(),
            'api_events' => ActivityLog::where('source', 'esp32_api')->count(),
        ];

        // RoadWatch WIB Date Filter V3
        // Input tanggal menggunakan WIB, query database menggunakan UTC.
        $request->validate([
            'source' => ['nullable', 'in:web,esp32_api,console'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        if ($request->filled('date_from')) {
            $startUtc = \Carbon\Carbon::createFromFormat(
                '!Y-m-d',
                $request->input('date_from'),
                'Asia/Jakarta'
            )->utc();

            $query->where('created_at', '>=',
                $startUtc->format('Y-m-d H:i:s'));
        }

        if ($request->filled('date_to')) {
            $endUtc = \Carbon\Carbon::createFromFormat(
                '!Y-m-d',
                $request->input('date_to'),
                'Asia/Jakarta'
            )->addDay()->utc();

            $query->where('created_at', '<',
                $endUtc->format('Y-m-d H:i:s'));
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('activity-logs.index', compact('logs', 'activityStats'));
    }

    public function latest()
    {
        return response()->json([
            'latest_id' => ActivityLog::max('id') ?? 0,
        ]);
    }
}

