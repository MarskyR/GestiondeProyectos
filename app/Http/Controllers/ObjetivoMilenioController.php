<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Configuracion;
use App\ErrorHistorial;
use App\ObjetivoMilenio;
use App\PlanEstrategicoInstitucional;
use App\Proyecto;
use App\RelacionProyectoObj;
use App\RelacionProyectoObjMilenio;
use Illuminate\Support\Facades\DB;

class ObjetivoMilenioController extends Controller
{
    //

    public function listar(){
        $objetivosMilenio = ObjetivoMilenio::All();

        return view('ObjetivosMilenio.ListarObjetivosMilenio',compact('objetivosMilenio'));
    }


    public function agregarEditarObjetivo(Request $request){
        try{
            
            DB::beginTransaction();
            
            if($request->codObjetivoMilenio=="0"){//NUEVO REGISTRO
                $obj = new ObjetivoMilenio();
                $mensaje = "creado";

            }else{ //registro ya existente estamos editando
                $obj = ObjetivoMilenio::findOrFail($request->codObjetivoMilenio);
                $mensaje = "editado";
            }
            $obj->descripcion= $request->descripcion;
            $obj->item= $request->item;
            $item = $request->item;

            $obj->save();
            db::commit();

            return redirect()->route('ObjetivoMilenio.listar')
                ->with('datos',"Se ha $mensaje el Objetivo del milenio $item");
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('ObjetivoMilenio.listar')
                ->with('datos', Configuracion::getMensajeError($codErrorHistorial));
        }
    }

    public function eliminar($codObjetivoMilenio){
        try {
            DB::beginTransaction();
            $registro =  ObjetivoMilenio::findOrFail($codObjetivoMilenio); 
            $descripcion = $registro->descripcion;
            
            //verificamos si está siendo usado
            $listaRelaciones = RelacionProyectoObjMilenio::where('codObjetivoMilenio','=',$codObjetivoMilenio)->get();
            if(count($listaRelaciones)!=0){
                $relacion = $listaRelaciones[0];
                $nombreProyecto = Proyecto::findOrFail($relacion->codProyecto)->nombre;
                return redirect()->route('ObjetivoMilenio.listar')
                    ->with('datos',"El objetivo del milenio está siendo usado por el proyecto '$nombreProyecto'.");
            }

            $registro->delete();
            DB::commit();
            return redirect()->route('ObjetivoMilenio.listar')
                ->with('datos',"Se ha eliminado el Objetivo del milenio $descripcion");
        } catch (\Throwable $th) {
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             $codObjetivoMilenio
                                                            );
            return redirect()->route('ObjetivoMilenio.listar')
                ->with('datos', Configuracion::getMensajeError($codErrorHistorial));
        }

    }


    /* 
        Recorre todos los proyectos
        en cada uno, genera las relaciones  (proyecto - objMilenio) (tabla relacion), 
        que sean necesarias para completar la cantidad de obj milenio de su PEI    
    */
    public function generarRelacionesProyectosYObjMilenio(){
        try{
            DB::beginTransaction();
            $listaProyectos = Proyecto::All();
            $msj = "";
            foreach ($listaProyectos as $proyecto) {
                $msj.=$proyecto->nombre."->";
                $objetivosMilenio = ObjetivoMilenio::All();

                foreach ($objetivosMilenio as $obj) { //cada uno debe tener un registro en la tabla relacion
                    $relaciones = RelacionProyectoObjMilenio::where('codProyecto','=',$proyecto->codProyecto)
                        ->where('codObjetivoMilenio','=',$obj->codObjetivoMilenio)
                        ->get();

                    if(count($relaciones)==0){ //SI NO EXISTE ESA RELACION, LA CREAMOS
                        $nuevaRel = new RelacionProyectoObjMilenio();
                        $nuevaRel->codProyecto = $proyecto->codProyecto;
                        $nuevaRel->codObjetivoMilenio = $obj->codObjetivoMilenio;
                        $nuevaRel->porcentaje =  0;
                        $nuevaRel->save();

                        $msj.=$obj->codObjetivoMilenio.",";
                    }
                }
                $msj.=" //";
            }
            
            DB::commit();
            return redirect()->route('ObjetivoMilenio.listar')->with('datos',"Se han generado las relaciones faltantes exitosamente. $msj");

        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                            app('request')->route()->getAction(),
                                                            ""
                                                            );
            return redirect()->route('ObjetivoMilenio.listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }

    }


}
