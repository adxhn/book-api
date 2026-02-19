<?php

namespace Tests\Feature\Identity;

use App\Models\User;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Services\Identity\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class SessionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_logout_with_current_token(): void
    {
        $user = User::factory()->create();

        $currentAccessToken = $user->createToken('current-session-token');
        $this->withToken($currentAccessToken->plainTextToken)->getJson('/api/me')->assertOk();
        $this->withToken($currentAccessToken->plainTextToken)->postJson('/api/logout')->assertNoContent();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $currentAccessToken->accessToken->id]);
    }

    public function test_sessions_request(): void
    {
        $user = User::factory()->create();

        $currentAccessToken = $user->createToken('current-session-token');
        $this->withToken($currentAccessToken->plainTextToken)->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonStructure(["data"]);
    }

    /**
     * Birden fazla token'ı olan bir kullanıcı için diğer cihazlardan çıkış yapma testi, mevcut token'ı koruyarak.
     */
    public function test_logout_other_devices_multiple_tokens_keeps_current(): void
    {
        $user = User::factory()->create();

        // Tokenlar oluştur
        $currentAccessToken = $user->createToken('current-session-token'); // Bu mevcut token olacak
        $otherToken1 = $user->createToken('other-device-token-1');
        $otherToken2 = $user->createToken('other-device-token-2');

        // Kullanıcı için mevcut erişim tokenını ayarla
        $user->withAccessToken($currentAccessToken);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $currentAccessToken->plainTextToken
        ])->postJson('/api/logout-other-devices')->assertNoContent();

        // Kalan tokenları kontrol etmek için kullanıcının tüm tokenlarını tekrar getir
        $remainingTokens = $user->tokens()->get();

        // Sadece mevcut tokenın kaldığını doğrula
        $this->assertCount(1, $remainingTokens);
        $this->assertEquals($currentAccessToken->accessToken->id, $remainingTokens->first()->id);
        $this->assertEquals('current-session-token', $remainingTokens->first()->name);

        // Silinen tokenların veritabanından gerçekten silindiğini doğrula
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $otherToken1->accessToken->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $otherToken2->accessToken->id]);
    }
}
