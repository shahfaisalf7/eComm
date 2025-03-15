<?php
namespace FleetCart\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $authType = getAuthMethod();
        \Log::info('ApiAuthMiddleware: Auth Type', [$authType]);

        if ($authType === 'jwt') {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            Auth::guard('api')->setUser($user);
        } elseif ($authType === 'passport') {
            if (!$user = auth('api')->user()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            Auth::guard('api')->setUser($user);
            Auth::shouldUse('api'); // Set 'api' as default for all API requests
        }
        return $next($request);
    }
}
