<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ApprovalCenterController extends Controller
{

    public function liveData(Request $request): JsonResponse
    {
        Gate::authorize('manageRoleRequests');

        $roleRequests = RoleRequest::with(['user', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('id')
            ->paginate(15);

        $stats = [
            'pending' => RoleRequest::where('status', 'pending')->count(),
            'approved' => RoleRequest::where('status', 'approved')->count(),
            'rejected' => RoleRequest::where('status', 'rejected')->count(),
        ];

        $html = view('admin.approval-center', [
            'roleRequests' => $roleRequests,
            'stats' => $stats,
        ])->render();

        return response()->json([
            'html' => $html,
            'version' => hash('sha256', $html),
        ]);
    }

    public function index(Request $request): View
    {
        Gate::authorize('manageRoleRequests');

        $roleRequests = RoleRequest::with(['user', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('id')
            ->paginate(15);

        $stats = [
            'pending' => RoleRequest::where('status', 'pending')->count(),
            'approved' => RoleRequest::where('status', 'approved')->count(),
            'rejected' => RoleRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.approval-center', [
            'roleRequests' => $roleRequests,
            'stats' => $stats,
        ]);
    }
}
