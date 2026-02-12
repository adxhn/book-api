<?php

namespace App\Services\Identity;

use App\Mail\UserWelcome;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected SessionRepository $sessionRepository,
    ) {}

    /**
     * Register a new user and create an authentication token.
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function register(
        string $email,
        string $password,
    ): array
    {
        return DB::transaction(function () use ($email, $password) {
            $user = $this->userRepository->create(
                $this->generateUniqueName($email),
                $email,
                $password,
            );

            $token = $user->createToken('auth-token');
            $this->sessionRepository->saveDeviceInfo($token);

            Mail::to($user)->later(now()->addMinute(), new UserWelcome($user));

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token->plainTextToken,
            ];
        });
    }

    private function generateUniqueName(string $email): string
    {
        // Email'in @ öncesi kısmını al
        $baseName = strtolower(explode('@', $email)[0]);
        // Özel karakterleri temizle (sadece harf, rakam ve alt çizgi bırak)
        $baseName = preg_replace('/[^a-z0-9_]/', '', $baseName);

        // Eğer baseName boşsa veya 5 karakterden küçükse varsayılan bir değer kullan
        if (empty($baseName) || strlen($baseName) < 5) {
            $baseName = 'user';
        }

        // Eğer içinde hiç alt çizgi (_) yoksa, sona ekle
        if (!str_contains($baseName, '_')) {
            $baseName .= '_';
        }

        // Benzersiz bir son ek oluştur
        // microtime(true) -> 1707315453.1234 gibi bir sayı döner
        // Noktayı atıp sadece rakamları alıyoruz
        $micro = str_replace('.', '', microtime(true));

        // Son 8 hanesini alıyoruz (çünkü en hızlı değişen kısım sonudur)
        return $baseName . substr($micro, -8);
    }
}
