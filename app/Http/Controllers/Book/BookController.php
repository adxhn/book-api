<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookDetailResource;
use App\Services\Book\BookService;
use Illuminate\Http\Request;

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
