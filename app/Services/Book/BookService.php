<?php

namespace App\Services\Book;

use App\Repositories\BookRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BookService
{
    public function __construct(
        protected BookRepository $bookRepository
    ) {}

    public function detail(string $slug)
    {
        if (strlen($slug) > 255) {
            abort(404);
        }

        $cleanSlug = Str::slug($slug);
        $cacheKey = 'book_detail_' . $cleanSlug;
        $cacheDuration = 60;

        return Cache::remember($cacheKey, $cacheDuration, function () use ($cleanSlug) {
            return $this->bookRepository->findBySlug($cleanSlug);
        });
    }
}
