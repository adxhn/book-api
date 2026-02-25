<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BookRepository;

class UserBookService
{
    public function __construct(
        protected BookRepository $bookRepository,
    ) {}

    public function get(User $user)
    {
        return $user->userBooks()->with('book')->paginate(10);
    }

    public function add(User $user, string $slug)
    {
        $bookId = $this->bookRepository->getIdBySlug($slug);
        return $user->userBooks()->updateOrCreate(['book_id' => $bookId]);
    }

    public function delete(User $user, string $slug)
    {
        $bookId = $this->bookRepository->getIdBySlug($slug);
        return $user->userBooks()->where(['book_id' => $bookId])->delete();
    }
}
