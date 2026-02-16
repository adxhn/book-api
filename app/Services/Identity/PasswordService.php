<?php

namespace App\Services\Identity;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordService
{
    /**
     * @param string $email
     * @throws ValidationException
     */
    public function forgotPassword(string $email): void
    {
        $this->sendResetMail($email);
    }

    /**
     * @param string $email
     * @throws ValidationException
     */
    private function sendResetMail(string $email): void
    {
        $status = Password::sendResetLink([
            'email' => $email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }
    }

    public function resetPassword(array $data): string
    {
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            Auth::logoutOtherDevices($password);
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        return $status;
    }
}
