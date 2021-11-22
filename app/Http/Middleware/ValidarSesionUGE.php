<?php

namespace App\Http\Middleware;

use App\Configuracion;
use Closure;
use App\Empleado;
use App\ErrorHistorial;

class ValidarSesionUGE
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

        
        if(!Empleado::getEmpleadoLogeado()->esAdminSistema())
            if(!Empleado::getEmpleadoLogeado()->esUGE()){
                $codErrorHistorial = ErrorHistorial::registrarError("Acceso a ruta sin permiso", app('request')->route()->getAction(),"");
                $msj = Configuracion::getMensajeError($codErrorHistorial);
                return redirect()->route('error')->with('datos',$msj);
            }
                
        return $next($request);
    }
}
