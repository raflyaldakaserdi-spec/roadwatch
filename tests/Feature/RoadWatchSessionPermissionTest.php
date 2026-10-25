<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RoadWatchSessionPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_promoted_to_worker_gets_permission_without_relogin(): void
    {
        $user = User::factory()->create([
            'role' => 'masyarakat',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertFalse(Gate::allows('viewActivityLogs'));

        $user->update(['role' => 'pekerja']);

        $this->assertFalse(Gate::allows('viewActivityLogs'));
        $this->assertFalse(Gate::allows('manageRoleRequests'));
    }

    public function test_worker_promoted_to_admin_gets_permission_without_relogin(): void
    {
        $user = User::factory()->create([
            'role' => 'pekerja',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertFalse(Gate::allows('manageRoleRequests'));

        $user->update(['role' => 'admin']);

        $this->assertTrue(Gate::allows('manageRoleRequests'));
        $this->assertFalse(Gate::allows('approveWorker'));
        $this->assertFalse(Gate::allows('approveAdmin'));
    }

    public function test_demoted_admin_loses_permission_without_relogin(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertTrue(Gate::allows('manageRoleRequests'));
        $this->assertTrue(Gate::allows('viewActivityLogs'));

        $user->update(['role' => 'masyarakat']);

        $this->assertFalse(Gate::allows('manageRoleRequests'));
        $this->assertFalse(Gate::allows('viewActivityLogs'));
        $this->assertFalse(Gate::allows('approveWorker'));
    }

    public function test_regular_admin_cannot_approve_admin(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertFalse(Gate::allows('approveWorker'));
        $this->assertFalse(Gate::allows('approveAdmin'));
    }
}
