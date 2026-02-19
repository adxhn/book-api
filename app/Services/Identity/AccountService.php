<?php

namespace App\Services\Identity;

use App\Models\User;

class AccountService
{
    public function updateEmail(string $email, User $user): string
    {
        if ($email !== $user->email) {
            $user->fill([
                'email' => $email,
                'email_verified_at' => null, // 2. Doğrulamayı sıfırla
            ]);

            $user->save();
            $user->sendEmailVerificationNotification();

            return 'E-posta adresiniz güncellendi. Lütfen yeni adresinizi doğrulayın.';
        }

        return 'E-posta adresiniz aynı';
    }
}
