<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBookToUserRequest;
use App\Http\Resources\UserBookResource;
use App\Services\UserBookService;
use Illuminate\Http\Request;

class UserBookController extends Controller
{
    public function __construct(
        protected UserBookService $service,
    ) {}

    public function index(Request $request)
    {
        return UserBookResource::collection($this->service->get($request->user()));
    }

    public function add(AddBookToUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        $this->service->add($request->user(), $data['book_slug']);
        return $this->success(message: 'Book added successfully', code: 201);
    }

    public function delete(Request $request, string $slug)
    {
        $this->service->delete($request->user(), $slug);
        return $this->noContent();
    }
}
