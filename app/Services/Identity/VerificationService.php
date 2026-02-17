<?php

namespace App\Services\Identity;

use App\Models\User;

class VerificationService
{
    public function sendVerificationEmail(User $user): string
    {
        if ($user->hasVerifiedEmail()) {
            return __('verification.already');
        }

        $user->sendEmailVerificationNotification();

        return __('verification.sent');
    }
}
