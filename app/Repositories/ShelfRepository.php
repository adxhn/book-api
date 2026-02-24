<?php

namespace App\Repositories;

use App\Models\Shelf;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShelfRepository
{
    public function get(User $user): HasMany
    {
        return $user->shelves();
    }
}
