<?php

namespace Tests\Feature;

use App\Models\Detection;
use App\Models\User;
use App\Models\WorkAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoadWatchAssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function assignment(
        User $worker,
        User $admin,
        string $status = 'Assigned'
    ): WorkAssignment {
        $detection = Detection::create([
            'detection_code' => 'RW-V6-' . uniqid(),
            'image' => 'test/detection.jpg',
            'location' => 'Testing Location',
            'latitude' => -6.20000000,
            'longitude' => 106.81666600,
            'detection_date' => '2026-10-24',
            'detection_day' => 'Saturday',
            'detection_time' => '12:00:00',
            'status' => 'Detected',
            'severity' => 'Medium',
        ]);

        return WorkAssignment::create([
            'detection_id' => $detection->id,
            'worker_id' => $worker->id,
            'assigned_by' => $admin->id,
            'priority' => 'High',
            'status' => $status,
            'instructions' => 'Perbaiki kerusakan jalan.',
        ]);
    }

    public function test_worker_can_start_own_assignment(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin);

        $this->actingAs($worker)
            ->post(route('my-assignments.start', $task))
            ->assertRedirect();

        $this->assertSame('In Progress', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->started_at);
    }

    public function test_worker_cannot_start_another_workers_assignment(): void
    {
        $owner = $this->user('pekerja');
        $other = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($owner, $admin);

        $this->actingAs($other)
            ->post(route('my-assignments.start', $task))
            ->assertForbidden();

        $this->assertSame('Assigned', $task->fresh()->status);
    }

    public function test_admin_cannot_start_worker_assignment(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin);

        $this->actingAs($admin)
            ->post(route('my-assignments.start', $task))
            ->assertForbidden();
    }

    public function test_worker_cannot_submit_without_photo(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'In Progress');

        $this->actingAs($worker)
            ->post(route('my-assignments.submit', $task), [
                'worker_notes' => 'Jalan sudah diperbaiki.',
            ])
            ->assertSessionHasErrors('completion_photo');

        $this->assertSame('In Progress', $task->fresh()->status);
    }

    public function test_worker_can_submit_photo_for_review(): void
    {
        Storage::fake('public');

        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'In Progress');

        $this->actingAs($worker)
            ->post(route('my-assignments.submit', $task), [
                'worker_notes' => 'Perbaikan telah selesai.',
                'completion_photo' => UploadedFile::fake()
                    ->image('proof.jpg'),
            ])
            ->assertRedirect();

        $task->refresh();

        $this->assertSame('Pending Review', $task->status);
        $this->assertNotNull($task->submitted_at);
        $this->assertNotNull($task->completion_photo);

        Storage::disk('public')->assertExists($task->completion_photo);

        $this->assertSame('Detected', $task->detection->fresh()->status);
    }

    public function test_admin_can_approve_submitted_assignment(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'Pending Review');

        $task->update([
            'worker_notes' => 'Perbaikan selesai.',
            'completion_photo' => 'assignment-proofs/proof.jpg',
        ]);

        $this->actingAs($admin)
            ->post(route('assignments.approve', $task))
            ->assertRedirect();

        $this->assertSame('Completed', $task->fresh()->status);
        $this->assertSame('Repaired', $task->detection->fresh()->status);
    }

    public function test_admin_can_reject_submitted_assignment(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'Pending Review');

        $this->actingAs($admin)
            ->post(route('assignments.reject', $task))
            ->assertRedirect();

        $this->assertSame('In Progress', $task->fresh()->status);
        $this->assertSame('Detected', $task->detection->fresh()->status);
    }

    public function test_worker_cannot_approve_assignment(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'Pending Review');

        $this->actingAs($worker)
            ->post(route('assignments.approve', $task))
            ->assertForbidden();
    }

    public function test_admin_cannot_approve_without_proof(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'Pending Review');

        $this->actingAs($admin)
            ->post(route('assignments.approve', $task))
            ->assertSessionHasErrors('status');

        $this->assertSame('Pending Review', $task->fresh()->status);
    }

    public function test_completed_assignment_cannot_be_approved_again(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'Completed');

        $this->actingAs($admin)
            ->post(route('assignments.approve', $task))
            ->assertSessionHasErrors('status');

        $this->assertSame('Completed', $task->fresh()->status);
    }
    public function test_approval_creates_exactly_one_detection_status_log(): void
    {
        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'Pending Review');

        $task->update([
            'worker_notes' => 'Perbaikan selesai.',
            'completion_photo' => 'assignment-proofs/proof.jpg',
        ]);

        $this->actingAs($admin)
            ->post(route('assignments.approve', $task))
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'status_updated',
            'subject_type' => Detection::class,
            'subject_id' => $task->detection_id,
        ]);

        $this->assertSame(
            1,
            \App\Models\ActivityLog::query()
                ->where('subject_type', Detection::class)
                ->where('subject_id', $task->detection_id)
                ->where('action', 'status_updated')
                ->count()
        );

        $this->assertSame('Repaired', $task->detection->fresh()->status);
    }

    public function test_resubmission_replaces_old_completion_photo(): void
    {
        Storage::fake('public');

        $worker = $this->user('pekerja');
        $admin = $this->user('admin');
        $task = $this->assignment($worker, $admin, 'In Progress');

        $oldPath = UploadedFile::fake()
            ->image('old.jpg')
            ->store('assignment-proofs', 'public');

        $task->update([
            'completion_photo' => $oldPath,
            'worker_notes' => 'Bukti sebelumnya.',
        ]);

        $this->actingAs($worker)
            ->post(route('my-assignments.submit', $task), [
                'worker_notes' => 'Bukti revisi terbaru.',
                'completion_photo' => UploadedFile::fake()
                    ->image('new.jpg'),
            ])
            ->assertRedirect();

        $task->refresh();

        $this->assertSame('Pending Review', $task->status);
        $this->assertNotSame($oldPath, $task->completion_photo);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($task->completion_photo);
    }

}
