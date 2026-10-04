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

        $detections = $query->latest()->paginate(10)->withQueryString();

        return view('detections.index', compact('detections'));
    }

    public function show($id)
    {
        $detection = Detection::findOrFail($id);
        return view('detections.show', compact('detection'));
    }

    public function map()
{
    $detections = Detection::all();
    return view('map', compact('detections'));
}
}