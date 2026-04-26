<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\Support\CreatesMovicData;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use CreatesMovicData;
    use RefreshDatabase;

    public function test_teacher_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'phone' => '11999999999',
            'password' => 'Password123',
            'role' => 'teacher',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.role', 'teacher');

        $userId = $response->json('user.id');

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'email' => 'teacher@example.com',
            'role' => 'teacher',
        ]);

        $this->assertDatabaseHas('teacher_profiles', [
            'user_id' => $userId,
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->createStudent([
            'email' => 'student@example.com',
            'password' => Hash::make('Password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_login_is_throttled_after_too_many_failed_attempts(): void
    {
        $user = $this->createStudent([
            'email' => 'rate-limit@example.com',
            'password' => Hash::make('Password123'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/auth/login', [
                'email' => $user->email,
                'password' => 'WrongPassword123',
            ])->assertStatus(401);
        }

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'WrongPassword123',
        ])->assertStatus(429);
    }

    public function test_forgot_password_returns_generic_success_response(): void
    {
        $user = $this->createStudent([
            'email' => 'forgot@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = $this->createStudent([
            'email' => 'reset@example.com',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        /** @var User $freshUser */
        $freshUser = $user->fresh();

        $this->assertTrue(Hash::check('NewPassword123', $freshUser->password));
    }
}
