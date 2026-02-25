<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBookToShelfRequest;
use App\Http\Resources\ShelfResource;
use App\Services\ShelfService;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function __construct(
        protected ShelfService $service,
    ) {}

    public function index(Request $request)
    {
        return ShelfResource::collection($this->service->get($request->user()));
    }

    public function add(AddBookToShelfRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        return $this->success(data: $this->service->add($request->user(), $data['book_id']));
    }
}
