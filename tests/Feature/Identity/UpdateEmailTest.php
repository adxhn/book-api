<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UpdateEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_verification_notification_when_email_is_updated()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);

        $newEmail = 'new@example.com';

        $this->actingAs($user)
            ->putJson('/api/update-email', [
                'email' => $newEmail,
            ])->assertCreated();

        $user->refresh();

        $this->assertEquals($newEmail, $user->email);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_it_does_not_send_notification_if_email_remains_the_same()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'same@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->put('/api/update-email', [
                'email' => 'same@example.com',
            ]);

        Notification::assertNothingSent();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_it_fails_validation_with_invalid_email_format()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put('/api/update-email', [
                'email' => 'invalid-email-string',
            ]);

        $response->assertSessionHasErrors('email');
    }
}
