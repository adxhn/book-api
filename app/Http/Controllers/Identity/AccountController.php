<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\UpdateEmailRequest;
use App\Services\Identity\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $service,
    ) {}

    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->service->updateEmail(
            $validated['email'],
            $request->user()
        );

        return $this->success(
            message: $result,
            data: ['email' => $validated['email']],
            code: 201
        );
    }
}
