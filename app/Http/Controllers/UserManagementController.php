<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserManagementController extends Controller
{

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('changeUserRole');

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'pekerja', 'masyarakat'])],
        ]);

        DB::transaction(function () use ($request, $user, $validated) {
            $target = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $target->id === $request->user()->id || $target->isSuperAdmin(),
                403,
                'Akun Super Admin tidak dapat diubah.'
            );

            $oldRole = $target->role;
            $newRole = $validated['role'];

            if ($oldRole === $newRole) {
                return;
            }

            $target->role = $newRole;
            $target->save();

            // Pending requests are no longer valid after manual role change.
            RoleRequest::where('user_id', $target->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                    'review_note' => 'Pengajuan ditutup karena role diubah oleh Super Admin.',
                ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'user_role_changed',
                'description' => 'Super Admin mengubah role pengguna dari '
                    . $oldRole . ' menjadi ' . $newRole,
                'subject_type' => User::class,
                'subject_id' => $target->id,
                'source' => 'web',
                'old_values' => ['role' => $oldRole],
                'new_values' => ['role' => $newRole],
            ]);
        });

        return back()->with(
            'user_role_success',
            'Hak akses pengguna berhasil diperbarui.'
        );
    }


    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('changeUserRole');

        $request->validate([
            'confirmation' => ['required', 'in:DELETE'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $target = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $target->id === $request->user()->id
                || (bool) $target->is_super_admin,
                403,
                'Akun ini tidak dapat dihapus.'
            );

            $target->delete();

            // Hapus sesi database milik akun yang dinonaktifkan.
            DB::table('sessions')
                ->where('user_id', $target->id)
                ->delete();

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'user_deleted',
                'description' => 'Super Admin menghapus akun pengguna: '
                    . $target->name,
                'subject_type' => User::class,
                'subject_id' => $target->id,
                'source' => 'web',
                'old_values' => [
                    'email' => $target->email,
                    'role' => $target->role,
                    'deleted_at' => null,
                ],
                'new_values' => [
                    'deleted_at' => $target->deleted_at?->toDateTimeString(),
                ],
            ]);
        });

        return redirect()->route('user-management.index')
            ->with('user_role_success', 'Akun berhasil dihapus.');
    }

    public function deletedUsers(Request $request): View
    {
        Gate::authorize('changeUserRole');

        $users = User::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.deleted-users', compact('users'));
    }

    public function restoreUser(Request $request, int $user): RedirectResponse
    {
        Gate::authorize('changeUserRole');

        DB::transaction(function () use ($request, $user) {
            $target = User::onlyTrashed()
                ->whereKey($user)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                (bool) $target->is_super_admin,
                403,
                'Akun Super Admin tidak dapat diproses.'
            );

            $target->restore();

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'user_restored',
                'description' => 'Super Admin memulihkan akun: '
                    . $target->name,
                'subject_type' => User::class,
                'subject_id' => $target->id,
                'source' => 'web',
                'old_values' => ['deleted_at' => 'deleted'],
                'new_values' => ['deleted_at' => null],
            ]);
        });

        return redirect()->route('user-management.deleted')
            ->with('user_role_success', 'Akun berhasil dipulihkan.');
    }

    public function index(Request $request): View
    {
        Gate::authorize('manageRoleRequests');

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:admin,pekerja,masyarakat'],
        ]);

        $query = User::query();

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (!empty($validated['role'])) {
            $query->where('role', $validated['role']);
        }

        $users = $query->latest('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'pekerja' => User::where('role', 'pekerja')->count(),
            'masyarakat' => User::where('role', 'masyarakat')->count(),
        ];

        return view('admin.user-management', compact('users', 'stats'));
    }
}
