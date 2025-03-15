<?php

namespace FleetCart\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class DetectApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        // $response = $next($request);

        // if(isAPI()){
        //     return apiResponse($response->getData(), 'Request successful');
        // }

        return  $next($request);
    }
}

