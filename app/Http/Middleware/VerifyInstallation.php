<?php

namespace App\Http\Middleware;

use Closure;

class VerifyInstallation {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if(app()->environment('setup') && !$request->session()->has('setup')) {
            return redirect()->route('home')->with('setup', true);
        }else {
            return $next($request);
        }
    }
}
