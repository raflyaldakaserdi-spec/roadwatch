<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,pekerja,masyarakat'], // Validasi pilihan role
        ]);

        // RoadWatch Registration Role Request V1
        // Semua akun publik dimulai sebagai masyarakat.
        // Role khusus harus melalui persetujuan administrator.
        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'masyarakat',
            ]);

            if (in_array($request->role, ['admin', 'pekerja'], true)) {
                \App\Models\RoleRequest::create([
                    'user_id' => $user->id,
                    'requested_role' => $request->role,
                    'status' => 'pending',
                    'reason' => 'Pengajuan akses melalui registrasi RoadWatch.',
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}