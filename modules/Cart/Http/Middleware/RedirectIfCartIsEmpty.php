<?php

namespace Modules\Cart\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;

class RedirectIfCartIsEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (isAPI() && Cart::isEmpty()) {
            return response()->json( [
                'status' => 'success',
                'message' => 'Cart is empty!',
                'data' => []
            ], 200);
        }
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index');
        }

        return $next($request);
    }
}
