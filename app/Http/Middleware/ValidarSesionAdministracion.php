<?php

namespace App\Http\Middleware;


use Closure;
use App\Debug;
use App\Empleado;
use App\ErrorHistorial;
use App\Configuracion;
class ValidarSesionAdministracion
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
            if(!Empleado::getEmpleadoLogeado()->esJefeAdmin()){
                $codErrorHistorial = ErrorHistorial::registrarError("Acceso a ruta sin permiso", app('request')->route()->getAction(),"");
                $msj = Configuracion::getMensajeError($codErrorHistorial);
                return redirect()->route('error')->with('datos',$msj);
            }
        
        return $next($request);
    }
}
