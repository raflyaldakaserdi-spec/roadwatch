<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Login Google gagal. Silakan coba kembali.',
                ]);
        }

        $googleId = (string) $googleUser->getId();
        $email = strtolower(trim((string) $googleUser->getEmail()));

        $emailVerified = filter_var(
            $googleUser->user['email_verified'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        if (!$googleId || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$emailVerified) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Akun Google harus memiliki email yang terverifikasi.',
                ]);
        }

        try {
            $user = DB::transaction(function () use ($googleId, $email, $googleUser) {
                $existing = User::withTrashed()->where('google_id', $googleId)->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        throw new \RuntimeException('account_deleted');
                    }
                    if (strtolower($existing->email) !== $email) {
                        throw new \RuntimeException('google_email_changed');
                    }

                    return $existing;
                }

                if (User::withTrashed()->where('email', $email)->exists()) {
                    throw new \RuntimeException('email_already_registered');
                }

                return User::create([
                    'name' => $googleUser->getName() ?: explode('@', $email)[0],
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(64)),
                    'google_id' => $googleId,
                    'role' => 'masyarakat',
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'account_deleted') {
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Akun telah dinonaktifkan. Hubungi administrator RoadWatch.',
                    ]);
            }

            if ($e->getMessage() === 'email_already_registered') {
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Email sudah terdaftar. Silakan login menggunakan password RoadWatch terlebih dahulu.',
                    ]);
            }

            if ($e->getMessage() === 'google_email_changed') {
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Email akun Google berubah. Hubungi administrator RoadWatch.',
                    ]);
            }

            report($e);

            return redirect()->route('login')
                ->withErrors(['email' => 'Login Google belum dapat diproses.']);
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->withErrors(['email' => 'Login Google belum dapat diproses.']);
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
