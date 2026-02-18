<?php

namespace App\Services\Identity;

use App\Models\User;

class AccountService
{
    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->tokens()->orderBy('last_used_at', 'desc')->get();
    }
}
