<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Services\Identity\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $service,
    ) {}

    public function sessions(Request $request)
    {
        return SessionResource::collection(
            $this->service->sessions($request->user())
        );
    }
}
