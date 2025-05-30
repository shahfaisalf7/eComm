<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            return $next($request);
        }

        $url = url()->full();

        if (!$request->isMethod('get')) {
            $url = url()->previous();
        }

        session()->put('url.intended', $url);

        if ($request->ajax()) {
            abort(403, 'Unauthenticated.');
        }

        return redirect()->route('login');
    }
}
