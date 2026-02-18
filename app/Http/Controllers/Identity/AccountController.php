<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Services\Identity\AccountService;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $service,
    ) {}
}
