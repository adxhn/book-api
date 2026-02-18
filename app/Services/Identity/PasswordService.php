<?php

namespace App\Services\Identity;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordService
{
    /**
     * @param string $email
     * @return string
     * @throws ValidationException
     */
    public function sendResetMail(string $email): string
    {
        $status = Password::sendResetLink([
            'email' => $email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        return trans($status);
    }

    public function resetPassword(array $data): string
    {
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            $user->tokens()->delete();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        return trans($status);
    }

    public static function passwordRules(): array
    {
        return [
            'required',
            'string',
            'confirmed',
            \Illuminate\Validation\Rules\Password::min(6)
                ->letters()
                ->numbers()
                ->mixedCase(),
        ];
    }
}
