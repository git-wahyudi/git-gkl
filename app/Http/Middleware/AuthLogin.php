<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::user()) {
            return redirect('/');
        }
        return $next($request);
    }
}
