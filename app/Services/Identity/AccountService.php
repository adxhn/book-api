<?php

namespace App\Services\Identity;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function changePassword(string $password, User $user): string
    {
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return 'Şifre başarıyla güncellendi.';
    }
}
