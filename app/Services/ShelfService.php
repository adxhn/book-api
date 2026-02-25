<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ShelfRepository;

class ShelfService
{
    public function __construct(
        protected ShelfRepository $shelfRepository,
    ) {}

    public function get(User $user)
    {
        return $user->shelves()->with('book')->paginate(10);
    }

    public function add(User $user, int $bookId)
    {
        return $user->shelves()->with('book')->updateOrCreate(['book_id' => $bookId]);
    }
}
