<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\LoginRequest;
use App\Http\Requests\Identity\RegisterRequest;
use App\Http\Resources\SessionResource;
use App\Services\Identity\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service,
    ) {}

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

    public function sessions(Request $request): AnonymousResourceCollection
    {
        return SessionResource::collection(
            $this->service->sessions($request->user())
        );
    }

    public function logoutOtherDevices(Request $request): Response
    {
        $this->service->logoutOtherDevices($request->user());

        return $this->noContent();
    }

    public function logout(Request $request): Response
    {
        $this->service->logout($request->user());

        return $this->noContent();
    }
}
