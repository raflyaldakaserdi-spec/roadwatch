<?php

namespace Tests\Feature;

use App\Models\Detection;
use App\Models\User;
use App\Models\WorkAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchAssignmentNotificationV7Test extends TestCase
{
    use RefreshDatabase;

    private function createUsers(): array
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $worker = User::factory()->create([
            'role' => 'pekerja',
        ]);

        return [$admin, $worker];
    }

    private function createDetection(): Detection
    {
        return Detection::create([
            'detection_code' => 'RW-V7-' . uniqid(),
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
    }

    private function assignmentData(
        Detection $detection,
        User $worker
    ): array {
        return [
            'detection_id' => $detection->id,
            'worker_id' => $worker->id,
            'priority' => 'High',
            'instructions' => 'Perbaiki kerusakan jalan.',
        ];
    }

    public function test_creating_assignment_records_history_and_notification(): void
    {
        [$admin, $worker] = $this->createUsers();
        $detection = $this->createDetection();

        $response = $this->actingAs($admin)->post(
            route('assignments.store'),
            $this->assignmentData($detection, $worker)
        );

        $response->assertRedirect(route('assignments.index'));

        $assignment = WorkAssignment::firstOrFail();

        $this->assertDatabaseHas('work_assignment_histories', [
            'work_assignment_id' => $assignment->id,
            'user_id' => $admin->id,
            'action' => 'created',
            'old_status' => null,
            'new_status' => 'Assigned',
        ]);

        $this->assertDatabaseCount('work_assignment_histories', 1);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $worker->id,
        ]);

        $this->assertDatabaseCount('notifications', 1);

        $notification = $worker->notifications()->firstOrFail();

        $this->assertSame(
            'assigned',
            $notification->data['event']
        );

        $this->assertSame(
            $assignment->id,
            $notification->data['assignment_id']
        );
    }

    public function test_duplicate_assignment_does_not_create_extra_records(): void
    {
        [$admin, $worker] = $this->createUsers();
        $detection = $this->createDetection();

        $data = $this->assignmentData($detection, $worker);

        $this->actingAs($admin)
            ->post(route('assignments.store'), $data)
            ->assertRedirect(route('assignments.index'));

        $this->actingAs($admin)
            ->post(route('assignments.store'), $data)
            ->assertSessionHasErrors('detection_id');

        $this->assertDatabaseCount('work_assignments', 1);
        $this->assertDatabaseCount('work_assignment_histories', 1);
        $this->assertDatabaseCount('notifications', 1);
    }
}
