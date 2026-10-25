<?php

namespace App\Http\Controllers;

use App\Models\WorkAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AssignmentWorkflowController extends Controller
{
    private function requireWorker(Request $request, WorkAssignment $assignment): void
    {
        abort_unless(
            $request->user()?->role === 'pekerja'
            && (int) $assignment->worker_id === (int) $request->user()->id,
            403
        );
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    public function start(Request $request, WorkAssignment $assignment)
    {
        $this->requireWorker($request, $assignment);

        DB::transaction(function () use ($assignment) {
            $task = WorkAssignment::whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            // ROADWATCH_ASSIGNMENT_SECURITY_V6
            abort_unless(
                (int) $task->worker_id === (int) auth()->id(),
                403
            );

            if ($task->status !== 'Assigned') {
                throw ValidationException::withMessages([
                    'status' => 'Pekerjaan ini tidak dapat dimulai.',
                ]);
            }

            $task->update([
                'status' => 'In Progress',
                'started_at' => now(),
            ]);
        });

        return back()->with('success', 'Pekerjaan berhasil dimulai.');
    }

    public function submit(Request $request, WorkAssignment $assignment)
    {
        $this->requireWorker($request, $assignment);

        $validated = $request->validate([
            'worker_notes' => ['required', 'string', 'max:3000'],
            'completion_photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        // ROADWATCH_ASSIGNMENT_PHOTO_CLEANUP_V6
        $path = null;
        $oldPhoto = null;

        try {
            DB::transaction(function () use ($assignment, $validated, $request, &$path, &$oldPhoto) {
                $task = WorkAssignment::whereKey($assignment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    (int) $task->worker_id === (int) $request->user()->id,
                    403
                );

                if ($task->status !== 'In Progress') {
                    throw ValidationException::withMessages([
                        'status' => 'Pekerjaan belum dalam tahap pengerjaan.',
                    ]);
                }

                $oldPhoto = $task->completion_photo;

                $path = $request->file('completion_photo')
                    ->store('assignment-proofs', 'public');

                if (!$path) {
                    throw ValidationException::withMessages([
                        'completion_photo' => 'Gagal menyimpan foto bukti.',
                    ]);
                }

                $task->update([
                    'status' => 'Pending Review',
                    'worker_notes' => $validated['worker_notes'],
                    'completion_photo' => $path,
                    'submitted_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        if ($oldPhoto && $oldPhoto !== $path
            && str_starts_with($oldPhoto, 'assignment-proofs/')) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with('success', 'Bukti pekerjaan berhasil dikirim untuk review.');
    }

    public function approve(Request $request, WorkAssignment $assignment)
    {
        $this->requireAdmin($request);

        DB::transaction(function () use ($assignment) {
            $task = WorkAssignment::whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($task->status !== 'Pending Review') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pekerjaan Pending Review yang dapat disetujui.',
                ]);
            }

            if (!$task->completion_photo || !$task->worker_notes) {
                throw ValidationException::withMessages([
                    'status' => 'Bukti foto dan catatan pekerjaan wajib tersedia.',
                ]);
            }

            $task->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

            // ROADWATCH_ASSIGNMENT_AUDIT_V6
            $detection = $task->detection()
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = $detection->status;

            if ($oldStatus !== 'Repaired') {
                \App\Models\Detection::withoutEvents(function () use ($detection) {
                    $detection->update(['status' => 'Repaired']);
                });

                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'status_updated',
                    'description' => 'Admin menyetujui pekerjaan dan mengubah status deteksi '
                        . $detection->detection_code
                        . ' dari ' . $oldStatus . ' menjadi Repaired',
                    'subject_type' => \App\Models\Detection::class,
                    'subject_id' => $detection->id,
                    'source' => 'web',
                    'old_values' => ['status' => $oldStatus],
                    'new_values' => ['status' => 'Repaired'],
                ]);
            }
        });

        return back()->with('success', 'Pekerjaan disetujui dan status jalan diperbarui.');
    }

    public function reject(Request $request, WorkAssignment $assignment)
    {
        $this->requireAdmin($request);

        DB::transaction(function () use ($assignment) {
            $task = WorkAssignment::whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($task->status !== 'Pending Review') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pekerjaan Pending Review yang dapat dikembalikan.',
                ]);
            }

            $task->update([
                'status' => 'In Progress',
                'submitted_at' => null,
            ]);
        });

        return back()->with('success', 'Pekerjaan dikembalikan untuk revisi.');
    }
}
