<?php

namespace App\Http\Controllers;

use App\Configuracion;
use App\Debug;
use App\Empleado;
use App\ErrorHistorial;
use App\Http\Controllers\Controller;
use App\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SedeController extends Controller
{
    /* despliega vista de listar sede */
    public function listarSedes(){

        $listaSedes = Sede::all();
        $listaAdministradores = Empleado::where('codPuesto','=',3)->get();
        
        return view('Sedes.ListarSedes',compact('listaSedes','listaAdministradores'));
    }




    public function GuardarEditar(Request $request){
        try {
            db::beginTransaction();

            if($request->codSede == "0"){//nueva
                $sede = new Sede();
                $mensaje = "creada";
            }else{ //editar

                $sede = Sede::findOrFail($request->codSede);
                $mensaje = "editada";
            }

            $sede->codSede = $request->codSede;
            $sede->nombre = $request->nombre;
            $sede->codEmpleadoAdministrador = $request->codEmpleadoAdministrador;
            $sede->save();

            db::commit();
            return redirect()->route('Sede.ListarSedes')
                ->with('datos','Sede "'.$sede->nombre.'" '.$mensaje.' exitosamente.');
        } catch (\Throwable $th) {
            Debug::mensajeError('SEDE CONTROLLER cambiarAdministrador',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('Sede.ListarSedes')
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }

    /* cadena tiene formato 52*16
        donde 52 es el codigo de la sede
        y 16 el codigo del nuevo administrador
    
    */
    public function cambiarAdministrador($cadena){
        try {
            db::beginTransaction();
            $vector = explode('*',$cadena);
            $codSede = $vector[0];
            $codEmpleado = $vector[1];

            $sede = Sede::findOrFail($codSede);
            $empleado = Empleado::findOrFail($codEmpleado);

            $sede->codEmpleadoAdministrador = $codEmpleado;
            $sede->save();
            db::commit();

            return 1;
        } catch (\Throwable $th) {
            Debug::mensajeError('SEDE CONTROLLER cambiarAdministrador',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena);
            return 0;
        }


    }



}
