<?php

namespace App\Repositories;

use App\Models\SocialAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function create
    (
        string $name,
        string $email,
        string $password
    ): User
    {
        return User::create([
            'name' => $name,
            'display_name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public function socialAuth
    (
        string $email,
        string $name,
        string $displayName,
        string $provider,
        string $providerId
    ): User
    {
        $user = User::firstOrCreate([
            'email' => $email,
        ], [
            'name' => $name,
            'email' => $email,
            'display_name' => $displayName,
            'email_verified_at' => now(),
            'password' => Hash::make(now()),
        ]);

        SocialAuth::updateOrCreate([
            'provider_id' => $providerId,
            'provider' => $provider,
        ], [
            'name' => $name,
            'email' => $email,
            'user_id' => $user->id,
        ]);

        return $user;
    }
}
