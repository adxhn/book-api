<?php

namespace Tests\Feature\Identity;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_successfully_with_email(): void
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
            'password' => 'Password123*',
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($user->tokens()->first());
    }

    public function test_user_can_login_successfully_with_username(): void
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
            'user_status' => UserStatus::ACTIVE,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'testuser',
            'password' => 'Password123*',
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($user->tokens()->first());
    }

    public function test_login_fails_with_incorrect_login(): void
    {
        User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
            'user_status' => UserStatus::ACTIVE,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'wrong@example.com',
            'password' => 'Password123*',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_login_fails_with_incorrect_password(): void
    {
        User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
            'user_status' => UserStatus::ACTIVE,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
            'password' => 'WrongPassword123*',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_login_fails_when_user_is_inactive(): void
    {
        User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
            'user_status' => UserStatus::INACTIVE,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
            'password' => 'Password123*',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_login_fails_when_user_is_deleted(): void
    {
        User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
            'user_status' => UserStatus::DELETED,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
            'password' => 'Password123*',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_login_fails_when_login_is_missing(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'Password123*',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_login_fails_when_password_is_missing(): void
    {
        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_it_rate_limits_login_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123*'),
            'user_status' => UserStatus::ACTIVE,
        ]);

        // İlk 5 deneme başarılı olmalı veya kimlik doğrulama başarısız olsa bile 200 veya 422 dönmeli
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'login' => 'test@example.com',
                'password' => 'WrongPassword123*', // Yanlış şifre ile deneme
            ]);
            $response->assertStatus(422); // Yanlış şifre olduğu için 422 bekliyoruz
        }

        // 6. deneme hız sınırlamasına takılmalı
        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
            'password' => 'WrongPassword123*',
        ]);
        $response->assertStatus(429);
    }
}
