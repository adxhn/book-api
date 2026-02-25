<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BookRepository;

class ShelfService
{
    public function __construct(
        protected BookRepository $bookRepository,
    ) {}

    public function get(User $user)
    {
        return $user->shelves()->with('book')->paginate(10);
    }

    public function add(User $user, string $slug)
    {
        $bookId = $this->bookRepository->getIdBySlug($slug);
        return $user->shelves()->with('book')->updateOrCreate(['book_id' => $bookId]);
    }
}
