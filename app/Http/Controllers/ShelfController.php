<?php

namespace App\Http\Controllers;

use App\Services\ShelfService;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function __construct(
        protected ShelfService $service,
    ) {}

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->success(data: $this->service->get($request->user()));
    }
}
