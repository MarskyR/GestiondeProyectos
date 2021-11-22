<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ActividadPrincipal;
use App\Configuracion;
use App\Debug;
use App\ErrorHistorial;
use Illuminate\Support\Facades\DB;

class ActividadPrincipalController extends Controller
{
    const PAGINATION = '20';

    public function listar(Request $request){
        
        
        $listaActividades = ActividadPrincipal::All();
        return view('ActividadPrincipal.ListarActividadPrincipal',compact('listaActividades'));

    }

    public function guardarEditarActividad(Request $request){
        try {
            DB::beginTransaction();
            
            if($request->codActividadPrincipal == "0"){
                $actividad = new ActividadPrincipal();
                $mensaje = "creada";
            }else{
                $actividad = ActividadPrincipal::findOrFail($request->codActividadPrincipal);
                $mensaje = "editada";
            }

            $actividad->descripcion = $request->descripcion; 
            $actividad->save();

            DB::commit();
            return redirect()
                ->route('ActividadPrincipal.Listar')
                ->with('datos','Actividad "'.$actividad->descripcion.'" '.$mensaje.' exitosamente.');

        } catch (\Throwable $th) {
            Debug::mensajeError('ACTIVIDAD PRINCIPAL CONTROLLER :guardarEditarActividad ',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('ActividadPrincipal.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }
}
