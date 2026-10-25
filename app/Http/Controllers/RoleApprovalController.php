<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RoleApprovalController extends Controller
{
    public function approve(Request $request, RoleRequest $roleRequest): RedirectResponse
    {
        return $this->review($request, $roleRequest, 'approved');
    }

    public function reject(Request $request, RoleRequest $roleRequest): RedirectResponse
    {
        return $this->review($request, $roleRequest, 'rejected');
    }

    private function review(
        Request $request,
        RoleRequest $roleRequest,
        string $decision
    ): RedirectResponse {
        Gate::authorize('manageRoleRequests');

        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use (
            $request,
            $roleRequest,
            $decision,
            $validated
        ) {
            $roleRequest = RoleRequest::whereKey($roleRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $roleRequest->status === 'pending',
                409,
                'Pengajuan ini sudah diproses.'
            );

            abort_unless(
                in_array($roleRequest->requested_role, ['admin', 'pekerja'], true),
                422,
                'Role yang diminta tidak valid.'
            );

            if ($roleRequest->requested_role === 'admin') {
                Gate::authorize('approveAdmin');
            } else {
                Gate::authorize('approveWorker');
            }

            $target = User::whereKey($roleRequest->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $target->id === $request->user()->id,
                403,
                'Tidak dapat memproses pengajuan sendiri.'
            );

            abort_unless(
                $target->role === 'masyarakat'
                && !$target->isSuperAdmin(),
                409,
                'Role pengguna sudah berubah.'
            );

            if ($decision === 'approved') {
                $target->role = $roleRequest->requested_role;
                $target->save();
            }

            // RoadWatch Atomic Approval Guard V1
            $updated = RoleRequest::whereKey($roleRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status' => $decision,
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                    'review_note' => $validated['review_note'] ?? null,
                ]);

            abort_unless(
                $updated === 1,
                409,
                'Pengajuan sudah diproses oleh administrator lain.'
            );

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $decision === 'approved'
                    ? 'role_approved'
                    : 'role_rejected',
                'description' => 'Permintaan akses ' .
                    $roleRequest->requested_role . ' ' .
                    ($decision === 'approved' ? 'disetujui' : 'ditolak'),
                'subject_type' => RoleRequest::class,
                'subject_id' => $roleRequest->id,
                'source' => 'web',
                'old_values' => [
                    'role' => 'masyarakat',
                    'status' => 'pending',
                ],
                'new_values' => [
                    'role' => $target->role,
                    'status' => $decision,
                ],
            ]);
        });

        return back()->with(
            'role_approval_success',
            $decision === 'approved'
                ? 'Permintaan akses berhasil disetujui.'
                : 'Permintaan akses berhasil ditolak.'
        );
    }
}
