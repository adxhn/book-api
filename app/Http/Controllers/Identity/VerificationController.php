<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return $this->success(
            message: __('verification_success'),
            code: 201
        );
    }
}
