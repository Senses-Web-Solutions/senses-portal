<?php

namespace App\Http\Middleware;

use Closure;

class BeforeAlwaysReturnJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json'); //todo doesn't seem to always apply?
        return $next($request);
    }
}
