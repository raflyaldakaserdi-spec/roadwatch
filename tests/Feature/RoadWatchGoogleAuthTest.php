<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class RoadWatchGoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function mockGoogleUser(
        string $id,
        string $email,
        bool $verified = true
    ): void {
        $googleUser = new SocialiteUser();

        $googleUser->map([
            'id' => $id,
            'name' => 'Google Test User',
            'email' => $email,
        ]);

        $googleUser->user = [
            'email_verified' => $verified,
        ];

        $driver = Mockery::mock();

        $driver->shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($driver);
    }

    public function test_new_google_user_starts_as_citizen(): void
    {
        $this->mockGoogleUser(
            'google-test-001',
            'new-google@test.local'
        );

        $this->get(route('google.callback'))
            ->assertRedirect(route('dashboard'));

        $user = User::where(
            'email',
            'new-google@test.local'
        )->firstOrFail();

        $this->assertSame('masyarakat', $user->role);
        $this->assertSame('google-test-001', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        $this->mockGoogleUser(
            'google-test-002',
            'unverified@test.local',
            false
        );

        $this->get(route('google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'unverified@test.local',
        ]);
    }

    public function test_existing_password_account_is_not_auto_linked(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing@test.local',
            'role' => 'admin',
            'google_id' => null,
        ]);

        $this->mockGoogleUser(
            'google-test-003',
            'existing@test.local'
        );

        $this->get(route('google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->assertNull($existing->fresh()->google_id);
        $this->assertSame('admin', $existing->fresh()->role);
    }

    public function test_existing_google_account_preserves_approved_role(): void
    {
        $existing = User::factory()->create([
            'email' => 'worker-google@test.local',
            'google_id' => 'google-test-004',
            'role' => 'pekerja',
        ]);

        $this->mockGoogleUser(
            'google-test-004',
            'worker-google@test.local'
        );

        $this->get(route('google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($existing);
        $this->assertSame('pekerja', $existing->fresh()->role);
    }
}
