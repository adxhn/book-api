<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
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
        return $this->success(data: $this->service->detail($slug));
    }
}
