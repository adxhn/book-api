<?php

namespace Tests\Feature\Identity;

use App\Mail\UserWelcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertStringStartsWith('user_', $user->name);
        $this->assertMatchesRegularExpression('/^user_[0-9]{8}$/', $user->name);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertNotNull($user->tokens()->first());
    }

    public function test_registration_creates_unique_name_when_prefix_exists(): void
    {
        // İlk kullanıcıyı oluştur
        User::factory()->create([
            'name' => 'test',
            'email' => 'test1@example.com',
        ]);

        // Aynı email prefix'i ile ikinci kullanıcı kaydet
        $userData = [
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->name);
        $this->assertStringStartsWith('user_', $user->name);
        $this->assertMatchesRegularExpression('/^user_[0-9]{8}$/', $user->name);
        $this->assertNotEquals('user_'.substr(str_replace('.', '', microtime(true)), -8), $user->name);
    }

    public function test_registration_fails_when_email_is_missing(): void
    {
        $userData = [
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_when_password_is_missing(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_password_is_too_short(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Different123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_email_is_invalid(): void
    {
        $userData = [
            'email' => 'invalid-email',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_creates_name_from_email_with_special_chars(): void
    {
        $userData = [
            'email' => 'test.user+123@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', 'test.user+123@example.com')->first();
        // Özel karakterler temizlenmeli, sadece harf ve rakam kalmalı
        $this->assertMatchesRegularExpression('/^[a-z0-9_]+$/', $user->name);
    }

    public function test_registration_fails_when_email_already_exists(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'email' => 'existing@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_when_password_has_no_letters(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_password_has_no_numbers(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'Password',
            'password_confirmation' => 'Password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_password_has_no_mixed_case(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_creates_name_from_short_email_prefix(): void
    {
        $userData = [
            'email' => 'ab@example.com', // Çok kısa prefix
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', 'ab@example.com')->first();
        $this->assertNotNull($user->name);
        // Çok kısa prefix'ler için 'user_' fallback kullanılmalı
        $this->assertStringStartsWith('user_', $user->name);
        $this->assertMatchesRegularExpression('/^user_[0-9]{8}$/', $user->name);
    }

    public function test_sends_welcome_email_on_registration(): void
    {
        Mail::fake();

        $userData = [
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->postJson('/api/register', $userData);

        $user = User::where('email', 'test@example.com')->first();

        Mail::assertQueued(UserWelcome::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
