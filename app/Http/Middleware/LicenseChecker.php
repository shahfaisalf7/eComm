<?php

namespace FleetCart\Http\Middleware;

use Closure;
use FleetCart\License;

class LicenseChecker
{
    private $license;


    public function __construct(License $license)
    {
        $this->license = $license;
    }


    public function handle($request, Closure $next)
    {
        // if ($this->license->shouldRecheck()) {
        //     $this->license->recheck();
        // } else if ($this->license->shouldCreateLicense()) {
        //     return redirect()->route('license.create');
        // }

        return $next($request);
    }
}
