<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // RoadWatch User Management Route V1
    Route::get(
        '/user-management',
        [\App\Http\Controllers\UserManagementController::class, 'index']
    )->middleware('can:manageRoleRequests')
     ->name('user-management.index');

    // RoadWatch Super Admin Change User Role V1
    Route::patch(
        '/user-management/{user}/role',
        [\App\Http\Controllers\UserManagementController::class, 'updateRole']
    )->middleware('can:changeUserRole')
     ->name('user-management.update-role');

    // RoadWatch Approval Center Route V1
    Route::get(
        '/approval-center',
        [\App\Http\Controllers\ApprovalCenterController::class, 'index']
    )->middleware('can:manageRoleRequests')
     ->name('approval-center.index');

    // RoadWatch Smart Approval Refresh V2
    Route::get(
        '/approval-center/live-data',
        [\App\Http\Controllers\ApprovalCenterController::class, 'liveData']
    )->middleware('can:manageRoleRequests')
     ->name('approval-center.live-data');

    // RoadWatch Role Approval Routes V1
    Route::middleware('can:approveAdmin')->group(function () {
        Route::patch(
            '/role-requests/{roleRequest}/approve',
            [\App\Http\Controllers\RoleApprovalController::class, 'approve']
        )->name('role-requests.approve');

        Route::patch(
            '/role-requests/{roleRequest}/reject',
            [\App\Http\Controllers\RoleApprovalController::class, 'reject']
        )->name('role-requests.reject');
    });

    // RoadWatch Role Request Submission V1
    Route::post('/role-requests',
        [\App\Http\Controllers\RoleRequestController::class, 'store']
    )->name('role-requests.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ROADWATCH_WORKER_WORKSPACE_V5_ROUTE
    Route::get('/my-assignments',
        [DashboardController::class, 'myAssignments']
    )->name('my-assignments.index');



    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])
        ->name('dashboard.live-data');


    // ROADWATCH_ASSIGNMENT_WORKFLOW_V6_ROUTES
    Route::post('/my-assignments/{assignment}/start',
        [\App\Http\Controllers\AssignmentWorkflowController::class, 'start']
    )->name('my-assignments.start');

    Route::post('/my-assignments/{assignment}/submit',
        [\App\Http\Controllers\AssignmentWorkflowController::class, 'submit']
    )->name('my-assignments.submit');

    Route::post('/assignments/{assignment}/approve',
        [\App\Http\Controllers\AssignmentWorkflowController::class, 'approve']
    )->name('assignments.approve');

    Route::post('/assignments/{assignment}/reject',
        [\App\Http\Controllers\AssignmentWorkflowController::class, 'reject']
    )->name('assignments.reject');

    // ROADWATCH_WORK_ASSIGNMENT_ROUTES_V1
    Route::middleware('can:manageRoleRequests')->group(function () {
        Route::get('/assignments',
            [\App\Http\Controllers\WorkAssignmentController::class, 'index']
        )->name('assignments.index');

        Route::post('/assignments',
            [\App\Http\Controllers\WorkAssignmentController::class, 'store']
        )->name('assignments.store');
    });

    // Route Detections
    Route::get('/detections', [DetectionController::class, 'index'])->name('detections.index');

    Route::get('/detections/live-table', [DetectionController::class, 'index'])
        ->name('detections.live-table');
    Route::patch('/detections/{id}/status', [DetectionController::class, 'updateStatus'])
        ->name('detections.update-status');

    Route::get('/detections/{id}', [DetectionController::class, 'show'])->name('detections.show');

    // Route Map Monitoring
    Route::get('/map-monitoring', [DetectionController::class, 'map'])->name('map.index');

    Route::get('/map-monitoring/live-data', [DetectionController::class, 'mapLiveData'])
        ->name('map.live-data');

    // RoadWatch Global Device Status API V1
    Route::get('/device-status/live', [DashboardController::class, 'deviceStatusLive'])
        ->name('device-status.live');

    // Route Device Info
    Route::get('/device-info', [DashboardController::class, 'deviceInfo'])->name('device.info');

    // RoadWatch RBAC: Activity Logs - Admin and Pekerja only
    Route::middleware('can:viewActivityLogs')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/latest', [ActivityLogController::class, 'latest'])->name('activity-logs.latest');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Google OAuth Authentication
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])
        ->name('google.redirect');

    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])
        ->name('google.callback');
});

require __DIR__.'/auth.php';


// RoadWatch Super Admin Account Lifecycle V1
Route::middleware(['auth', 'verified', 'can:changeUserRole'])
    ->group(function () {
        Route::get(
            '/user-management/deleted',
            [\App\Http\Controllers\UserManagementController::class, 'deletedUsers']
        )->name('user-management.deleted');

        Route::delete(
            '/user-management/{user}/delete',
            [\App\Http\Controllers\UserManagementController::class, 'destroyUser']
        )->name('user-management.destroy-user');

        Route::patch(
            '/user-management/deleted/{user}/restore',
            [\App\Http\Controllers\UserManagementController::class, 'restoreUser']
        )->whereNumber('user')
         ->name('user-management.restore-user');
    });
