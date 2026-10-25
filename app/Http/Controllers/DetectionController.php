<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use Illuminate\Http\Request;

class DetectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Detection::query();

        // Fitur Search (Location atau Detection Code)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('location', 'like', '%' . $search . '%')
                  ->orWhere('detection_code', 'like', '%' . $search . '%');
            });
        }

        // Fitur Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fitur Filter Severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // RoadWatch Detection Analytics V2
        // Statistik global, tidak dibatasi filter atau pagination.
        $stats = [
            'total' => Detection::count(),
            'high' => Detection::where('severity', 'High')->count(),
            'progress' => Detection::where('status', 'In Progress')->count(),
            'repaired' => Detection::where('status', 'Repaired')->count(),
        ];

        $detections = $query->latest()->paginate(10)->withQueryString();

        if ($request->routeIs('detections.live-table')) {
            return view('detections.index', compact('detections', 'stats'))
                ->render();
        }

        return view('detections.index', compact('detections', 'stats'));
    }

    public function show($id)
    {
        $detection = Detection::findOrFail($id);
        return view('detections.show', compact('detection'));
    }


    public function updateStatus(\Illuminate\Http\Request $request, $id)
    {
        abort_unless(
            $request->user()?->role === 'admin',
            403,
            'Hanya admin yang dapat mengubah status.'
        );

        $validated = $request->validate([
            'status' => ['required', 'in:Detected,In Progress,Repaired'],
        ]);

        $changed = \Illuminate\Support\Facades\DB::transaction(function () use ($id, $validated, $request) {

            $detection = Detection::findOrFail($id);

            $oldStatus = $detection->status;
            $newStatus = $validated['status'];

            if ($oldStatus === $newStatus) {
                return false;
            }

            // Status dicatat secara khusus sebagai status_updated.
            // Hindari log updated ganda dari DetectionObserver.
            Detection::withoutEvents(function () use ($detection, $newStatus) {
                $detection->update([
                    'status' => $newStatus,
                ]);
            });

            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'status_updated',
                'description' => 'Admin ' . $request->user()->name
                    . ' mengubah status deteksi '
                    . $detection->detection_code
                    . ' dari ' . $oldStatus
                    . ' menjadi ' . $newStatus,
                'subject_type' => Detection::class,
                'subject_id' => $detection->id,
                'source' => 'web',
                'old_values' => [
                    'status' => $oldStatus,
                ],
                'new_values' => [
                    'status' => $newStatus,
                ],
            ]);

            return true;
        });

        return redirect()
            ->route('detections.index')
            ->with(
                'success',
                $changed
                    ? 'Status berhasil diperbarui dan dicatat di Log History.'
                    : 'Status tidak berubah.'
            );
    }

    public function mapLiveData()
    {
        $detections = Detection::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id',
                'detection_code',
                'image',
                'location',
                'latitude',
                'longitude',
                'severity',
                'status',
            ]);

        return response()->json([
            'detections' => $detections,
        ]);
    }

    public function map()
{
    $detections = Detection::all();
    return view('map', compact('detections'));
}
}