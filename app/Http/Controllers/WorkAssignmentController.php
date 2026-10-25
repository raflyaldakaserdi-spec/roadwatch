<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use App\Models\User;
use App\Models\WorkAssignment;
use App\Models\WorkAssignmentHistory;
use App\Notifications\AssignmentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkAssignmentController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $assignments = WorkAssignment::with([
            'detection',
            'worker',
            'assignedBy',
        ])->latest()->paginate(10);

        $detections = Detection::query()
            ->whereDoesntHave('workAssignment')
            ->where('status', '!=', 'Repaired')
            ->latest()
            ->get();

        $workers = User::query()
            ->where('role', 'pekerja')
            ->orderBy('name')
            ->get();

        return view('assignments.index', compact(
            'assignments',
            'detections',
            'workers'
        ));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $validated = $request->validate([
            'detection_id' => [
                'required',
                'integer',
                Rule::exists('detections', 'id'),
                Rule::unique('work_assignments', 'detection_id'),
            ],
            'worker_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'pekerja')
                    ->whereNull('deleted_at'),
            ],
            'priority' => [
                'required',
                Rule::in(['Low', 'Medium', 'High']),
            ],
            'instructions' => ['nullable', 'string', 'max:3000'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $detection = Detection::query()
                ->whereKey($validated['detection_id'])
                ->where('status', '!=', 'Repaired')
                ->firstOrFail();

            // ROADWATCH_ASSIGNMENT_CREATE_V7
            $assignment = WorkAssignment::create([
                'detection_id' => $detection->id,
                'worker_id' => $validated['worker_id'],
                'assigned_by' => $request->user()->id,
                'priority' => $validated['priority'],
                'status' => 'Assigned',
                'instructions' => $validated['instructions'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
            ]);

            WorkAssignmentHistory::create([
                'work_assignment_id' => $assignment->id,
                'user_id' => $request->user()->id,
                'action' => 'created',
                'old_status' => null,
                'new_status' => 'Assigned',
                'notes' => 'Penugasan dibuat oleh Admin.',
            ]);

            $worker = User::findOrFail($assignment->worker_id);

            $worker->notify(new AssignmentStatusNotification(
                $assignment->id,
                'assigned',
                'Penugasan Baru',
                'Anda menerima penugasan perbaikan jalan baru.',
                route('my-assignments.index')
            ));
        });

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Penugasan berhasil dibuat.');
    }
}
