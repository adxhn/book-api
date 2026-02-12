<?php

namespace App\Repositories;

use Laravel\Sanctum\NewAccessToken;

class SessionRepository
{
    public function saveDeviceInfo(NewAccessToken $token): void
    {
        $token->accessToken->forceFill([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => request()->header('X-DEVICE-TYPE'),
            'device_model' => request()->header('X-DEVICE-MODEL'),
            'device_id' => request()->header('X-DEVICE-ID'),
        ])->save();
    }
}
