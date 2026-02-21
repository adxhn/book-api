<?php

namespace App\Services\Identity;

use App\Enums\UserStatus;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialUser;
use App\Notifications\UserWelcome;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected SessionRepository $sessionRepository,
    ) {}

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

            $user->notify(
                (new UserWelcome(VerificationService::verificationUrl($user))
                )->delay(now()->plus(minutes: 1)));

            return AuthResource::make($user, $token);
        });
    }

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

    public function socialAuth
    (
        string $provider,
        SocialUser $socialUser
    )
    {
        return DB::transaction(function () use ($provider, $socialUser) {
            $user = $this->userRepository->socialAuth(
                $socialUser->email,
                $this->generateUniqueName($socialUser->email),
                $socialUser->name,
                $provider,
                $socialUser->id
            );

            $token = $user->createToken('auth-token');
            $this->sessionRepository->saveDeviceInfo($token);

            return AuthResource::make($user, $token);
        });
    }

    public function sessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->tokens()->orderBy('last_used_at', 'desc')->get();
    }

    public function logoutOtherDevices(User $user): int
    {
        return $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
    }

    public function logout(User $user): int
    {
        return $user->tokens()->where('id', '=', $user->currentAccessToken()->id)->delete();
    }

    private function generateUniqueName(string $email): string
    {
        $baseName = strtolower(explode('@', $email)[0]);
        $baseName = preg_replace('/[^a-z0-9_]/', '', $baseName);

        if (empty($baseName) || strlen($baseName) < 5) {
            $baseName = 'user';
        }

        if (!str_contains($baseName, '_')) {
            $baseName .= '_';
        }

        $micro = str_replace('.', '', microtime(true));

        return $baseName . substr($micro, -8);
    }
}
