<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Detection;
use App\Observers\DetectionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // RoadWatch Super Admin User Role Management V1
        \Illuminate\Support\Facades\Gate::define(
            'changeUserRole',
            fn (\App\Models\User $user) => $user->isSuperAdmin()
        );

        // RoadWatch Role Approval Gates V1
        \Illuminate\Support\Facades\Gate::define(
            'manageRoleRequests',
            fn (\App\Models\User $user) => $user->role === 'admin'
        );

        \Illuminate\Support\Facades\Gate::define(
            'approveWorker',
            fn (\App\Models\User $user) =>
                $user->role === 'admin'
                && (bool) $user->is_super_admin
        );

        \Illuminate\Support\Facades\Gate::define(
            'approveAdmin',
            fn (\App\Models\User $user) =>
                $user->role === 'admin'
                && (bool) $user->is_super_admin
        );

        \Illuminate\Support\Facades\Gate::define(
            'viewActivityLogs',
            fn (\App\Models\User $user) => in_array(
                $user->role,
                ['admin'],
                true
            )
        );

        Detection::observe(DetectionObserver::class);
	if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme("https");
        }
    }
}

