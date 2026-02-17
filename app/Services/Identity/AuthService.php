<?php

namespace App\Services\Identity;

use App\Enums\UserStatus;
use App\Mail\UserWelcome;
use App\Models\User;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Resources\AuthResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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

            return AuthResource::make($user, $token);
        });
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     * @throws ValidationException
     */
    public function login(
        string $email,
        string $password
    ): array
    {
        $user = User::where('email', '=', $email)->first();

        if (! $user
            || ! Hash::check($password, $user->password)
            || $user->user_status !== UserStatus::ACTIVE
        ) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $token = $user->createToken('auth-token');
        $this->sessionRepository->saveDeviceInfo($token);

        return AuthResource::make($user, $token);
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
