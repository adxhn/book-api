<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Services\Identity\VerificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(
        protected VerificationService $service,
    ) {}

    public function sendVerificationEmail(Request $request): JsonResponse
    {
        return $this->success($this->service->sendVerificationEmail($request->user()));
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return $this->success(
            message: __('verification.success'),
            code: 201
        );
    }
}
