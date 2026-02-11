<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\RegisterRequest;
use App\Services\Identity\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service,
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->service->register(
            $validated['email'],
            $validated['password']
        );

        return $this->success($result, 201);
    }
}
