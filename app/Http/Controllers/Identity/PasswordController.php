<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\ForgotPasswordRequest;
use App\Http\Requests\Identity\ResetPasswordRequest;
use App\Services\Identity\PasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function __construct(
        protected PasswordService $service,
    ) {}

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $message = $this->service->forgotPassword($data['email']);

        return $this->success(
            message: $message,
            code: 201
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $message = $this->service->resetPassword($data);

        return $this->success(
            message: $message,
            code: 201
        );
    }
}
