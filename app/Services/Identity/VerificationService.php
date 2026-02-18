<?php

namespace App\Services\Identity;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerificationService
{
    public function sendVerificationEmail(User $user): string
    {
        if ($user->hasVerifiedEmail()) {
            throw new AuthorizationException(__('verification.already'));
        }

        $user->sendEmailVerificationNotification();
        return __('verification.sent');
    }

    public static function verificationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );
    }
}
