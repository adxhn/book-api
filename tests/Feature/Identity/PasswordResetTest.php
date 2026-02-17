<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertStatus(201)
            ->assertJson(['message' => trans('passwords.sent')]);

        // Assert that a token was created in the database
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, PasswordResetNotification::class);
    }

    public function test_email_is_required_for_password_reset_request(): void
    {
        $this->postJson('/api/forgot-password', ['email' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $newPassword = 'Password123!';

        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
            ->assertStatus(201)
            ->assertJson(['message' => trans('passwords.reset')]);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

        // Assert the token is deleted after a successful reset
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $newPassword = 'Password123!';

        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email'); // Laravel returns 'email' error for invalid token
    }

    public function test_password_requires_confirmation_and_validation_rules(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Test without password confirmation
        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'Password123!',
            'password_confirmation' => '',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('password');

        // Test with a weak password
        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('password');
    }

    public function test_resetting_password_logs_out_other_devices(): void
    {
        $user = User::factory()->create();

        // Simulate a login and get a token (first device)
        $token = $user->createToken('test-device-1')->plainTextToken;

        // Verify the token works
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/me')->assertOk();

        // Begin the password reset process
        $resetToken = Password::createToken($user);
        $newPassword = 'NewSecurePassword123!';

        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $resetToken,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertStatus(201);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        $this->app['auth']->forgetGuards();

        // Try to use the old token from the "first device"
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/me');

        // Assert that the session is no longer authenticated
        $response->assertUnauthorized();
    }

    public function test_forgot_password_endpoint_is_rate_limited(): void
    {
        $user = User::factory()->create();

        // İlk istek başarılı olmalı
        $this->postJson('/api/forgot-password', ['email' => $user->email])
             ->assertStatus(201);

        // 30 saniye içinde ikinci istek 422 hatası vermeli (Laravel'in dahili throttle)
        $this->postJson('/api/forgot-password', ['email' => $user->email])
             ->assertStatus(422)
             ->assertJsonValidationErrorFor('email');

        // Zamanı 31 saniye ileri sararak throttle'ın geçmesini bekle
        $this->travel(31)->seconds();

        // 31 saniye sonra tekrar istek gönder, başarılı olmalı
        $this->postJson('/api/forgot-password', ['email' => $user->email])
             ->assertStatus(201);
    }

    public function test_password_reset_token_expires_after_configured_time(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Zamanı 121 dakika ileri sar (limit 120 dakika) - Token artık geçersiz
        $this->travel(15)->minutes();

        $newPassword = 'NewPassword123!';

        // Süresi dolmuş token ile şifre sıfırlamayı dene
        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
            ->assertStatus(422) // Geçersiz token hatası bekliyoruz
            ->assertJsonValidationErrorFor('email'); // Laravel süresi dolmuş token'lar için 'email' hatası verir
    }
}
