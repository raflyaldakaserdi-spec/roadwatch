<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchHttpAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_cannot_access_restricted_pages(): void
    {
        $user = User::factory()->create(['role' => 'masyarakat']);
        $this->actingAs($user);

        foreach ([
            'activity-logs.index',
            'activity-logs.latest',
            'approval-center.index',
            'user-management.index',
        ] as $route) {
            $this->get(route($route))->assertForbidden();
        }
    }

    public function test_worker_can_view_logs_but_not_admin_pages(): void
    {
        $user = User::factory()->create(['role' => 'pekerja']);
        $this->actingAs($user);

        $this->get(route('activity-logs.index'))->assertForbidden();
        $this->get(route('activity-logs.latest'))->assertForbidden();
        $this->get(route('approval-center.index'))->assertForbidden();
        $this->get(route('user-management.index'))->assertForbidden();
    }

    public function test_admin_can_access_restricted_pages(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->get(route('activity-logs.index'))->assertOk();
        $this->get(route('activity-logs.latest'))->assertOk();
        $this->get(route('approval-center.index'))->assertOk();
        $this->get(route('user-management.index'))->assertOk();
    }

    public function test_demoted_admin_loses_http_access(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->get(route('approval-center.index'))->assertOk();

        $user->forceFill(['role' => 'masyarakat'])->save();

        $this->get(route('approval-center.index'))->assertForbidden();
        $this->get(route('activity-logs.index'))->assertForbidden();
    }
}
