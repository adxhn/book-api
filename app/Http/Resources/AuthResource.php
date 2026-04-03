<?php

namespace App\Http\Resources;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class AuthResource
{
    public static function make(User $user, NewAccessToken $accessToken)
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'display_name' => $user->display_name,
                'profile_photo_url' => $user->profile_photo_url,
            ],
            'token' => $accessToken->plainTextToken,
        ];
    }
}
