<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ShelfRepository;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShelfService
{
    public function __construct(
        protected ShelfRepository $shelfRepository,
    ) {}

    public function get(User $user): HasMany
    {
        return $this->shelfRepository->get($user);
    }
}
