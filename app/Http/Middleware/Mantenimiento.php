<?php

namespace App\Http\Middleware;

use App\Configuracion;
use Closure;

class Mantenimiento
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

        if(Configuracion::estamosEnMantenimiento){
            return redirect()->route('mantenimiento');
        }

        return $next($request);
    }
}
