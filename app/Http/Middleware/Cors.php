<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        // https://medium.com/@gilbertoparedes/rest-api-laravel-utilizando-passport-para-autenticaci%C3%B3n-con-aplicaciones-ionic-82e8f30e5862
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH,DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type,Authorization, X-Requested-With, X-XSRF-TOKEN');
            
        //return $next($request);
    }
}
