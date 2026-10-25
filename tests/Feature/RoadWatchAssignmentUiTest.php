<?php

namespace Tests\Feature;

use App\Models\Detection;
use App\Models\User;
use App\Models\WorkAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchAssignmentUiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAssignment(string $status): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'pekerja']);

        $detection = Detection::create([
            'detection_code' => 'RW-UI-' . uniqid(),
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

        $assignment = WorkAssignment::create([
            'detection_id' => $detection->id,
            'worker_id' => $worker->id,
            'assigned_by' => $admin->id,
            'priority' => 'High',
            'status' => $status,
            'instructions' => 'Perbaiki jalan dan dokumentasikan hasilnya.',
            'worker_notes' => 'Perbaikan telah dilakukan.',
            'completion_photo' => 'assignment-proofs/test.jpg',
        ]);

        return [$admin, $worker, $assignment];
    }

    public function test_worker_sees_start_button_for_assigned_task(): void
    {
        [, $worker, $assignment] = $this->makeAssignment('Assigned');

        $this->actingAs($worker)
            ->get(route('my-assignments.index'))
            ->assertOk()
            ->assertSee(route('my-assignments.start', $assignment), false)
            ->assertSee('Mulai Pekerjaan')
            ->assertDontSee('Kirim untuk Review Admin');
    }

    public function test_worker_sees_submission_form_for_in_progress_task(): void
    {
        [, $worker, $assignment] = $this->makeAssignment('In Progress');

        $this->actingAs($worker)
            ->get(route('my-assignments.index'))
            ->assertOk()
            ->assertSee(route('my-assignments.submit', $assignment), false)
            ->assertSee('Kirim Bukti Pekerjaan')
            ->assertSee('Kirim untuk Review Admin');
    }

    public function test_worker_sees_pending_review_message(): void
    {
        [, $worker] = $this->makeAssignment('Pending Review');

        $this->actingAs($worker)
            ->get(route('my-assignments.index'))
            ->assertOk()
            ->assertSee('Menunggu review Admin.')
            ->assertDontSee('Kirim untuk Review Admin');
    }

    public function test_worker_sees_completed_message(): void
    {
        [, $worker] = $this->makeAssignment('Completed');

        $this->actingAs($worker)
            ->get(route('my-assignments.index'))
            ->assertOk()
            ->assertSee('Pekerjaan selesai dan telah disetujui Admin.');
    }

    public function test_admin_sees_review_buttons_for_pending_assignment(): void
    {
        [$admin, , $assignment] = $this->makeAssignment('Pending Review');

        $this->actingAs($admin)
            ->get(route('assignments.index'))
            ->assertOk()
            ->assertSee(route('assignments.approve', $assignment), false)
            ->assertSee(route('assignments.reject', $assignment), false)
            ->assertSee('Minta Revisi')
            ->assertSee('Setujui');
    }

    public function test_worker_cannot_open_admin_assignment_center(): void
    {
        [, $worker] = $this->makeAssignment('Assigned');

        $this->actingAs($worker)
            ->get(route('assignments.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_open_worker_assignments_page(): void
    {
        [$admin] = $this->makeAssignment('Assigned');

        $this->actingAs($admin)
            ->get(route('my-assignments.index'))
            ->assertForbidden();
    }
}
