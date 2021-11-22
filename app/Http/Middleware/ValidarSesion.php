<?php

namespace App\Http\Middleware;

use App\Debug;
use App\Empleado;
use Closure;

class ValidarSesion
{
    
    public function handle($request, Closure $next)
    {
        if(Empleado::getEmpleadoLogeado()==""){
            return redirect()->route('user.verLogin');
        }

        return $next($request);
    }
}
