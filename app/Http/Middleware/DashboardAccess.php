<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardAccess
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
        if ($request->header('X-Dashboard-Access') !== 'allowed' && env('APP_ENV') != 'local') {
            return response()->json(['error' => 'Không có trang này'], 404);
        }

        return $next($request);
    }
}
