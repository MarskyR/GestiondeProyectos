<?php

namespace App\Http\Middleware;

use App\Configuracion;
use App\Empleado;
use App\ErrorHistorial;
use Closure;

class ValidarSesionGerenteOUGE
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
        //deja pasar si es uge o es gerente

        if(!Empleado::getEmpleadoLogeado()->esAdminSistema())
            if(!Empleado::getEmpleadoLogeado()->esUGE() && !Empleado::getEmpleadoLogeado()->esGerente()){
                $codErrorHistorial = ErrorHistorial::registrarError("Acceso a ruta sin permiso", app('request')->route()->getAction(),"");
                $msj = Configuracion::getMensajeError($codErrorHistorial);
                return redirect()->route('error')->with('datos',$msj);
            }


        return $next($request);
    }
}
