<?php

namespace Tests\Feature;

use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function registerUser(string $role, string $email): void
    {
        $this->post(route('register'), [
            'name' => 'RoadWatch Test User',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => $role,
        ])->assertRedirect(route('dashboard'));
    }

    public function test_citizen_registration_has_no_role_request(): void
    {
        $this->registerUser('masyarakat', 'citizen@test.local');

        $user = User::where('email', 'citizen@test.local')->firstOrFail();

        $this->assertSame('masyarakat', $user->role);

        $this->assertSame(
            0,
            RoleRequest::where('user_id', $user->id)->count()
        );
    }

    public function test_worker_registration_creates_pending_request(): void
    {
        $this->registerUser('pekerja', 'worker@test.local');

        $user = User::where('email', 'worker@test.local')->firstOrFail();

        $this->assertSame('masyarakat', $user->role);

        $this->assertDatabaseHas('role_requests', [
            'user_id' => $user->id,
            'requested_role' => 'pekerja',
            'status' => 'pending',
        ]);
    }

    public function test_admin_registration_creates_pending_request(): void
    {
        $this->registerUser('admin', 'admin@test.local');

        $user = User::where('email', 'admin@test.local')->firstOrFail();

        $this->assertSame('masyarakat', $user->role);

        $this->assertDatabaseHas('role_requests', [
            'user_id' => $user->id,
            'requested_role' => 'admin',
            'status' => 'pending',
        ]);
    }

    public function test_citizen_can_request_worker_access(): void
    {
        $user = User::factory()->create([
            'role' => 'masyarakat',
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('role-requests.store'), [
                'requested_role' => 'pekerja',
                'reason' => 'Saya membutuhkan akses untuk monitoring jalan.',
                'organization' => 'RoadWatch Test',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('role_requests', [
            'user_id' => $user->id,
            'requested_role' => 'pekerja',
            'status' => 'pending',
        ]);
    }

    public function test_duplicate_pending_request_is_rejected(): void
    {
        $user = User::factory()->create([
            'role' => 'masyarakat',
        ]);

        RoleRequest::create([
            'user_id' => $user->id,
            'requested_role' => 'pekerja',
            'status' => 'pending',
            'reason' => 'Pengajuan awal untuk monitoring jalan.',
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('role-requests.store'), [
                'requested_role' => 'admin',
                'reason' => 'Saya membutuhkan akses administrasi.',
            ])
            ->assertSessionHasErrors('requested_role');

        $this->assertSame(
            1,
            RoleRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count()
        );
    }

    public function test_super_admin_can_approve_worker_registration_request(): void
    {
        $this->registerUser('pekerja', 'approved-worker@test.local');

        $target = User::where(
            'email',
            'approved-worker@test.local'
        )->firstOrFail();

        $roleRequest = RoleRequest::where(
            'user_id',
            $target->id
        )->firstOrFail();

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('approval-center.index'))
            ->patch(route('role-requests.approve', $roleRequest))
            ->assertRedirect(route('approval-center.index'));

        $this->assertSame('pekerja', $target->fresh()->role);
        $this->assertSame('approved', $roleRequest->fresh()->status);
    }
}
