<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BackendAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem header 'X-Api-Access' có tồn tại và có giá trị 'allowed' không
        if ($request->header('X-backend-Access') !== 'allowed') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
