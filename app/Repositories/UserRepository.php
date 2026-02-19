<?php

namespace App\Repositories;

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
}
