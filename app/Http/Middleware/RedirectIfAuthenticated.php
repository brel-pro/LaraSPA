<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //TODO need to skip this, if it login route
        //TODO get the right HOME

        $requestUri = $request->requestUri;
        $isLoginRoute = str_contains($request['requestUri'], 'auth/login');

        if (Auth::guard($guard)->check()) {
            // if ($isLoginRoute) {
            //     # code...
            // }
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
