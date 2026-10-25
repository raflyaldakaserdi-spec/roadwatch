<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Detection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchDetectionStatusTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
        ]);
    }

    private function makeDetection(): Detection
    {
        return Detection::create([
            'detection_code' => 'RW-SECURITY-TEST-001',
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

    public function test_admin_can_update_detection_status(): void
    {
        $detection = $this->makeDetection();
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->patch(route('detections.update-status', $detection->id), [
                'status' => 'In Progress',
            ])
            ->assertRedirect(route('detections.index'));

        $this->assertSame(
            'In Progress',
            $detection->fresh()->status
        );
    }

    public function test_worker_cannot_update_detection_status(): void
    {
        $detection = $this->makeDetection();
        $worker = $this->makeUser('pekerja');

        $this->actingAs($worker)
            ->patch(route('detections.update-status', $detection->id), [
                'status' => 'Repaired',
            ])
            ->assertForbidden();

        $this->assertSame('Detected', $detection->fresh()->status);
    }

    public function test_citizen_cannot_update_detection_status(): void
    {
        $detection = $this->makeDetection();
        $citizen = $this->makeUser('masyarakat');

        $this->actingAs($citizen)
            ->patch(route('detections.update-status', $detection->id), [
                'status' => 'Repaired',
            ])
            ->assertForbidden();

        $this->assertSame('Detected', $detection->fresh()->status);
    }

    public function test_admin_status_change_is_logged(): void
    {
        $detection = $this->makeDetection();
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->patch(route('detections.update-status', $detection->id), [
                'status' => 'Repaired',
            ])
            ->assertRedirect(route('detections.index'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'subject_type' => Detection::class,
            'subject_id' => $detection->id,
            'action' => 'status_updated',
        ]);

        $this->assertSame(
            1,
            ActivityLog::where('subject_type', Detection::class)
                ->where('subject_id', $detection->id)
                ->where('action', 'status_updated')
                ->count()
        );

        $this->assertSame(
            0,
            ActivityLog::where('subject_type', Detection::class)
                ->where('subject_id', $detection->id)
                ->where('action', 'updated')
                ->count()
        );
    }
}
