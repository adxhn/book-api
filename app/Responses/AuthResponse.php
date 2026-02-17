<?php

namespace App\Responses;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class AuthResponse
{
    public static function make(User $user, NewAccessToken $accessToken)
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $accessToken->plainTextToken,
        ];
    }
}
