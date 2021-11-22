<?php

namespace App\Http\Middleware;

use App\Empleado;
use Closure;
use App\ErrorHistorial;
use App\Configuracion;
class ValidarSesionAdministracionOContador
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

        /* EN ESTE MIDDLEWARE, SER ADMIN DE SISTEMA NO DESACTIVA LA VALIDACIÓN, */
        //if(!Empleado::getEmpleadoLogeado()->esAdminSistema())
            if(!Empleado::getEmpleadoLogeado()->esContador() && ! Empleado::getEmpleadoLogeado()->esJefeAdmin() ){
                $codErrorHistorial = ErrorHistorial::registrarError("Acceso a ruta sin permiso", app('request')->route()->getAction(),"Middleware ValidarSesionAdministracionOContador");
                $msj = Configuracion::getMensajeError($codErrorHistorial);
                return redirect()->route('error')->with('datos',$msj);
            }


        return $next($request);
    }
}
