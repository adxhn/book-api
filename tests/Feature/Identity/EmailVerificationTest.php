<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_a_new_verification_link(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->postJson(route('verification.email'));

        Notification::assertSentTo($user, VerifyEmail::class);

        $response->assertStatus(200)->assertJson(['message' => trans('verification.sent')]);
    }

    public function test_email_can_be_verified_with_valid_link(): void
    {
        // E-postası doğrulanmamış bir kullanıcı oluştur
        $user = User::factory()->unverified()->create();

        // Doğrulama URL'sini manuel olarak oluştur
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Event'lerin tetiklendiğini doğrulamak için
        Event::fake();
       $response = $this->actingAs($user)->get($verificationUrl);

        // "Verified" event'inin tetiklendiğini doğrula
        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $response->assertStatus(201)
            ->assertJson(['message' => trans('verification.success')]);
    }

    public function test_email_cannot_be_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        // Geçersiz bir hash ile doğrulama URL'si oluştur
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // Kullanıcının e-postasının hala doğrulanmamış olduğunu kontrol et
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    public function test_email_cannot_be_verified_with_expired_hash(): void
    {
        $user = User::factory()->unverified()->create();

        // Geçersiz bir hash ile doğrulama URL'si oluştur
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $expire = config('auth.verification.expire', 60) + 1;
        $this->travel($expire)->minutes();

        $response = $this->actingAs($user)->get($verificationUrl);

        // Kullanıcının e-postasının hala doğrulanmamış olduğunu kontrol et
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    public function test_email_cannot_be_verified_by_different_user(): void
    {
        $user = User::factory()->unverified()->create();
        $user2 = User::factory()->unverified()->create();

        // Geçersiz bir hash ile doğrulama URL'si oluştur
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user2)->get($verificationUrl);

        // Kullanıcının e-postasının hala doğrulanmamış olduğunu kontrol et
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    public function test_verified_user_cannot_request_new_link()
    {
        $user = User::factory()->unverified()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->postJson('/api/email/verification-notification');

        $this->assertTrue($user->hasVerifiedEmail());
        $response->assertStatus(403)
            ->assertJson(['message' => trans('verification.already')]);
    }

    public function test_user_cannot_request_verification_email_too_frequently(): void
    {
        Notification::fake(); // Gerçekten mail gönderilmesini engelle
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        for ($i = 0; $i < 3; $i++) {
            $this->postJson(route('verification.email'))
                ->assertStatus(200);
        }

        $response = $this->postJson(route('verification.email'));
        $response->assertStatus(429);

        Notification::assertCount(3);
    }
}
