<?php

namespace SmartBots\Http\Middleware;

use Closure;
use Session;

class Authenticated
{
    /**
     * Check if user is loged in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth()->guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return abort(401);
            } else {
                return redirect()->route('a::login');
            }
        }

        return $next($request);
    }

}
