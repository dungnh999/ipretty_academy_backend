<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EnsureAdultMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            (
                empty($request->cookie(Config::get('constants.COOKIE_ADULT_CONFIRMATION_KEY')))
                || $request->cookie(Config::get('constants.COOKIE_ADULT_CONFIRMATION_KEY')) != Config::get('constants.COOKIE_ADULT_CONFIRMATION_VALUE')
            ) // not confirmed before
            && isRequestingAdultRoutes($request)
        ) {
            session()->put(Config::get('constants.SESSION_KEY_PREVIOUS_URI_BEFORE_ADULT_CONFIRMATION'), $request->getRequestUri());
            return redirect()->route('microsite.adult-confirmation');
        }
        return $next($request);
    }
}
