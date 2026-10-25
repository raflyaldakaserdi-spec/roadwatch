<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchRoleApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(
        string $role = 'masyarakat',
        bool $superAdmin = false
    ): User {
        $user = User::factory()->create(['role' => $role]);

        $user->forceFill([
            'is_super_admin' => $superAdmin,
        ])->save();

        return $user;
    }

    private function makeRequest(
        User $user,
        string $role = 'pekerja'
    ): RoleRequest {
        return RoleRequest::create([
            'user_id' => $user->id,
            'requested_role' => $role,
            'status' => 'pending',
            'reason' => 'Memerlukan akses untuk monitoring jalan.',
        ]);
    }

    public function test_masyarakat_cannot_open_approval_center(): void
    {
        $this->actingAs($this->makeUser())
            ->get(route('approval-center.index'))
            ->assertForbidden();
    }

    public function test_pekerja_cannot_open_approval_center(): void
    {
        $this->actingAs($this->makeUser('pekerja'))
            ->get(route('approval-center.index'))
            ->assertForbidden();
    }

    public function test_admin_can_open_approval_center(): void
    {
        $this->actingAs($this->makeUser('admin'))
            ->get(route('approval-center.index'))
            ->assertOk();
    }

    public function test_regular_admin_cannot_approve_admin(): void
    {
        $admin = $this->makeUser('admin');
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target, 'admin');

        $this->actingAs($admin)
            ->patch(route('role-requests.approve', $roleRequest))
            ->assertForbidden();

        $this->assertSame('masyarakat', $target->fresh()->role);
        $this->assertSame('pending', $roleRequest->fresh()->status);
    }

    public function test_regular_admin_cannot_approve_worker(): void
    {
        $admin = $this->makeUser('admin');
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target);

        $this->actingAs($admin)
            ->patch(route('role-requests.approve', $roleRequest))
            ->assertForbidden();

        $this->assertSame('masyarakat', $target->fresh()->role);
        $this->assertSame('pending', $roleRequest->fresh()->status);
    }

    public function test_regular_admin_cannot_reject_request(): void
    {
        $admin = $this->makeUser('admin');
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target);

        $this->actingAs($admin)
            ->patch(route('role-requests.reject', $roleRequest), [
                'review_note' => 'Rejected by regular admin',
            ])
            ->assertForbidden();

        $this->assertSame('masyarakat', $target->fresh()->role);
        $this->assertSame('pending', $roleRequest->fresh()->status);
    }

    public function test_super_admin_can_approve_worker(): void
    {
        $superAdmin = $this->makeUser('admin', true);
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target);

        $this->actingAs($superAdmin)
            ->from(route('approval-center.index'))
            ->patch(route('role-requests.approve', $roleRequest))
            ->assertRedirect(route('approval-center.index'));

        $this->assertSame('pekerja', $target->fresh()->role);
        $this->assertSame('approved', $roleRequest->fresh()->status);
    }

    public function test_super_admin_can_approve_admin(): void
    {
        $superAdmin = $this->makeUser('admin', true);
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target, 'admin');

        $this->actingAs($superAdmin)
            ->from(route('approval-center.index'))
            ->patch(route('role-requests.approve', $roleRequest))
            ->assertRedirect(route('approval-center.index'));

        $this->assertSame('admin', $target->fresh()->role);
        $this->assertSame('approved', $roleRequest->fresh()->status);
    }

    public function test_approved_request_cannot_be_processed_again(): void
    {
        $admin = $this->makeUser('admin', true);
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target);

        $this->actingAs($admin)
            ->patch(route('role-requests.approve', $roleRequest));

        $this->actingAs($admin)
            ->patch(route('role-requests.reject', $roleRequest))
            ->assertStatus(409);

        $this->assertSame('approved', $roleRequest->fresh()->status);
        $this->assertSame('pekerja', $target->fresh()->role);

        $this->assertSame(
            1,
            ActivityLog::where('subject_type', RoleRequest::class)
                ->where('subject_id', $roleRequest->id)
                ->whereIn('action', ['role_approved', 'role_rejected'])
                ->count()
        );
    }

    public function test_rejection_does_not_change_user_role(): void
    {
        $admin = $this->makeUser('admin', true);
        $target = $this->makeUser();
        $roleRequest = $this->makeRequest($target);

        $this->actingAs($admin)
            ->from(route('approval-center.index'))
            ->patch(route('role-requests.reject', $roleRequest), [
                'review_note' => 'Dokumen pendukung belum lengkap.',
            ])
            ->assertRedirect(route('approval-center.index'));

        $this->assertSame('masyarakat', $target->fresh()->role);
        $this->assertSame('rejected', $roleRequest->fresh()->status);

        $this->assertDatabaseHas('activity_logs', [
            'subject_id' => $roleRequest->id,
            'action' => 'role_rejected',
        ]);
    }
}
