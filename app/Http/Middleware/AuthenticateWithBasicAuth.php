<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AuthenticateWithBasicAuth
{
    /**
     * The guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Specify the guard and field for the middleware.
     *
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return string
     *
     * @named-arguments-supported
     */
    public static function using($guard = null, $field = null)
    {
        return static::class.':'.implode(',', func_get_args());
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        logger($request->getUser());
        logger($request->getPassword());

        return $next($request);
    }
}
