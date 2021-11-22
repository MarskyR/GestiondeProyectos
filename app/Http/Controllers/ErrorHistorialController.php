<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\ErrorHistorial;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Configuracion;
use App\Debug;

class ErrorHistorialController extends Controller
{
    const PAGINATION = 20;

    public function listarErrores(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $controllerDondeOcurrio=$request->controllerDondeOcurrio;

        //$empleado=Empleado::getEmpleadoLogeado();

        $errores=ErrorHistorial::where('codErrorHistorial','>','0');//este es como un all pero que no retorne collection

        if($codEmpleadoBuscar!=0){
            $errores= $errores->where('codEmpleado','=',$codEmpleadoBuscar);
        }
        if($controllerDondeOcurrio!=0){
            $errores= $errores->where('controllerDondeOcurrio','=',$controllerDondeOcurrio);
        }
        $errores = $errores->orderBy('fechaHora','DESC');

        $errores=$errores->paginate($this::PAGINATION);

        /*
        $action = app('request')->route()->getAction(); // obtiene la accion : "App\Http\Controllers\HomeController@index" 
        $controller = class_basename($action['controller']); // obtiene el nombre base de la clase : "HomeController@index"
        list($controllerNameActual,$action) = explode('@', $controller);
        //explode : {$controller : "HomeController", $action : "index"}
        /***************************/

        $empleados=Empleado::getEmpleadosActivos();
        $controllers=DB::TABLE('error_historial')->SELECT('error_historial.controllerDondeOcurrio as controllerDondeOcurrio')->groupBy('controllerDondeOcurrio')->get();
        return view('HistorialErrores.ListarErrores',
            compact('errores','empleados','codEmpleadoBuscar','controllerDondeOcurrio','controllers'));
    }



    public function guardarRazonSolucionError(Request $request){
        try {
            db::beginTransaction();
            $error = ErrorHistorial::findOrFail($request->codErrorHistorial);
            $error->razon = $request->razon;
            $error->solucion = $request->solucion;
            $error->save();

            db::commit();
            return redirect()->route('HistorialErrores.Listar')->with('datos','Razón del error guardada');

        } catch (\Throwable $th) {
            Debug::mensajeError('ERROR HISTORIAL CONTROLLER guardarRazonError',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('HistorialErrores.Listar')->with('datos',ErrorHistorial::mensajeError($codErrorHistorial));
        }


    }

    /* SE EJECUTA DESDE JS */
    public function cambiarEstadoError($codError){

        try {
            $error=ErrorHistorial::findOrFail($codError);
            if($error->estadoError==0){
                $error->estadoError=1;
            }else{
                $error->estadoError=0;
            }
            $error->save();
            return 1;
        } catch (\Throwable $th) {
            Debug::mensajeError('ERROR HISTORIAL CONTROLLER cambiar estado error',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codError);
            return 0;
        }   
        
        

    }
    

    public function ver($codError){
        $error = ErrorHistorial::findOrFail($codError);

        return view('HistorialErrores.VerError',compact('error'));

    }

}
