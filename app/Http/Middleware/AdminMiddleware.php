<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
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
        if(Auth::guard('admin')->user() && Auth::guard('admin')->user()->is_logged_in == 0){
            auth()->guard('admin')->logout();
        }
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        return redirect()->route('admin.auth.login');
    }
}
