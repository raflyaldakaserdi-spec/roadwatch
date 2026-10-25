<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\RoleRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        // V1: Pengajuan role khusus hanya dari akun masyarakat.
        abort_unless($user->role === 'masyarakat', 403);

        $validated = $request->validate([
            'requested_role' => [
                'required',
                Rule::in(['admin', 'pekerja']),
            ],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'organization' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($user, $validated) {
                // Periksa role terbaru dari database.
                $currentUser = \App\Models\User::findOrFail($user->id);

                if ($currentUser->role !== 'masyarakat') {
                    abort(403, 'Akun sudah memiliki akses khusus.');
                }

                if (RoleRequest::where('user_id', $currentUser->id)
                    ->where('status', 'pending')
                    ->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'requested_role' => 'Masih ada pengajuan yang menunggu persetujuan.',
                    ]);
                }

                $roleRequest = RoleRequest::create([
                    'user_id' => $currentUser->id,
                    'requested_role' => $validated['requested_role'],
                    'status' => 'pending',
                    'reason' => $validated['reason'],
                    'organization' => $validated['organization'] ?? null,
                ]);

                ActivityLog::create([
                    'user_id' => $currentUser->id,
                    'action' => 'role_requested',
                    'description' => 'Pengguna mengajukan akses ' .
                        $roleRequest->requested_role,
                    'subject_type' => RoleRequest::class,
                    'subject_id' => $roleRequest->id,
                    'source' => 'web',
                    'new_values' => [
                        'requested_role' => $roleRequest->requested_role,
                        'status' => 'pending',
                    ],
                ]);
            });
        } catch (QueryException $e) {
            // Tangani kemungkinan dua request bersamaan.
            if (str_contains(strtolower($e->getMessage()), 'unique')) {
                return back()->withErrors([
                    'requested_role' => 'Masih ada pengajuan yang menunggu persetujuan.',
                ]);
            }

            throw $e;
        }

        return back()->with(
            'role_request_success',
            'Pengajuan akses berhasil dikirim dan menunggu persetujuan.'
        );
    }
}
