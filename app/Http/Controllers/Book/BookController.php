<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\Book\BookDetailResource;
use App\Services\Book\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function __construct(
        protected BookService $service,
    ) {}

    public function show(Request $request, string $slug)
    {
        return new BookDetailResource($this->service->detail($slug));
    }
}
