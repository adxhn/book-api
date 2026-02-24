<?php

namespace App\Repositories;

use App\Enums\BookStatus;
use App\Models\Book;

class BookRepository
{
    public function findBySlug(string $slug): Book
    {
        return Book::with(['category', 'author', 'publisher'])
            ->where('slug', $slug)
            ->where('book_status', '=', BookStatus::ACTIVE->value)
            ->firstOrFail();
    }
}
