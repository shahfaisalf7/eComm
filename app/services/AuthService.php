<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Passport\TokenGuard;

class AuthService
{
    public function getAuthenticatedUser()
    {
        $authType = Config::get('auth_method.auth_type');

        if ($authType === 'jwt') {
            return JWTAuth::parseToken()->authenticate();
        } elseif ($authType === 'passport') {
            return auth()->guard('api')->user();
        }

        return null;
    }
}
