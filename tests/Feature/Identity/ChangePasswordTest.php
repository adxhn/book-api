<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_updates_password_successfully_and_invalidates_other_tokens()
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $currentToken = $user->createToken('current_device')->plainTextToken;
        $user->createToken('other_device');

        $this->assertCount(2, $user->tokens);

        $response = $this->withToken($currentToken)
            ->putJson('/api/change-password', [
                'current_password' => 'OldPassword123!',
                'password' => 'NewSecurePassword123!',
                'password_confirmation' => 'NewSecurePassword123!',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Şifre başarıyla güncellendi.');
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->fresh()->password));
        $this->assertCount(1, $user->fresh()->tokens);
    }

    public function test_it_fails_if_current_password_is_incorrect()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/change-password', [
                'current_password' => 'WrongPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
        $this->assertTrue(Hash::check('CorrectPassword123!', $user->fresh()->password));
    }

    public function test_it_fails_if_new_password_is_same_as_current_password()
    {
        $password = 'SamePassword123!';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/change-password', [
                'current_password' => $password,
                'password' => $password,
                'password_confirmation' => $password,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_it_fails_if_password_confirmation_does_not_match()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson('/api/change-password', [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'DifferentPassword123!',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
