<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserSignupRequest;
use App\Services\UserAuthService;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
     public $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function signup(UserSignupRequest $request, $referral_code=null)
    {
        $validated = $request->validated();
        return $this->userAuthService->signup($validated);
    }

    public function login(UserLoginRequest $request)
    {
        $validated = $request->validated();
        return $this->userAuthService->login($validated);
    }
    public function userProfile()
    {
        return $this->userAuthService->userProfile();
    }

    public function logout()
    {
        return $this->userAuthService->logout();
    }
}
