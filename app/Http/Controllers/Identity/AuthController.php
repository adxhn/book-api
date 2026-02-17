<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\LoginRequest;
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

        return $this->success(
            message: 'Kayıt işlemi başarılı.',
            data: $result,
            code: 201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->service->login(
            $validated['email'],
            $validated['password']
        );

        return $this->success(
            message: 'Giriş işlemi başarılı.',
            data: $result,
            code: 201)
            ;
    }
}
