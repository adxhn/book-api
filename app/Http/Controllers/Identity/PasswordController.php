<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\ForgotPasswordRequest;
use App\Http\Requests\Identity\ResetPasswordRequest;
use App\Services\Identity\PasswordService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function __construct(
        protected PasswordService $service,
    ) {}

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->validated();
        $this->service->forgotPassword($data['email']);

        return $this->success(code: 201, message: trans('passwords.sent'));
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $status = $this->service->resetPassword($data);

        return response()->json(['status' => trans($status)]);
    }

    public function resetPasswordForm(Request $request)
    {
        return 'form view';
    }
}
