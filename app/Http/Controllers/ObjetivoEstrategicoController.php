<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\ObjetivoEstrategico;

use App\Configuracion;
use App\ErrorHistorial;
use App\PlanEstrategicoInstitucional;
use App\Proyecto;
use App\RelacionProyectoObj;
use Illuminate\Support\Facades\DB;

class ObjetivoEstrategicoController extends Controller
{
    
  
  



    
    public function agregarEditarObjetivoEstrategico(Request $request){
        try{
            DB::beginTransaction();
            $PEI = PlanEstrategicoInstitucional::findOrFail($request->codPEI);
            if($request->codObjetivoEstrategico=="0"){//NUEVO REGISTRO
                $obj = new ObjetivoEstrategico();
                $obj->codPEI= $request->codPEI;
                $mensaje = "creado";

            }else{ //registro ya existente estamos editando
                $obj = ObjetivoEstrategico::findOrFail($request->codObjetivoEstrategico);
                $mensaje = "editado";
            }
            $item =  $request->item;
            $obj->item = $request->item;
            $obj->nombre = $request->nombreObjetivoEstrategico;
            $obj->descripcion = $request->descripcionObjetivoEstrategico;

            $obj->save();
            db::commit();

            return redirect()->route('PlanEstrategico.editar',$PEI->codPEI)
                ->with('datos',"Se ha $mensaje el Objetivo estratégico $item");
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('PlanEstrategico.editar',$PEI->codPEI)
                ->with('datos', Configuracion::getMensajeError($codErrorHistorial));
        }


    }

    public function eliminar($codObjetivoEstrategico){
        try {
            DB::beginTransaction();
            $registro =  ObjetivoEstrategico::findOrFail($codObjetivoEstrategico); 
            $nombre = $registro->nombre;
            $PEI  = PlanEstrategicoInstitucional::findOrFail($registro->codPEI);

            //verificamos si está siendo usado
            $listaRelaciones = RelacionProyectoObj::where('codObjetivoEstrategico','=',$codObjetivoEstrategico)->get();
            if(count($listaRelaciones)!=0){
                $relacion = $listaRelaciones[0];
                $nombreProyecto = Proyecto::findOrFail($relacion->codProyecto)->nombre;
                return redirect()->route('PlanEstrategico.editar',$PEI->codPEI)
                    ->with('datos',"El objetivo estratégico está siendo usado por el proyecto '$nombreProyecto'.");
            }

            $registro->delete();
            DB::commit();
            return redirect()->route('PlanEstrategico.editar',$PEI->codPEI)
                ->with('datos',"Se ha eliminado el Objetivo estratégico $nombre");
        } catch (\Throwable $th) {
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             ['codObjetivoEstrategico' => $codObjetivoEstrategico]
                                                            );
            return redirect()->route('PlanEstrategico.editar',$PEI->codPEI)
                ->with('datos', Configuracion::getMensajeError($codErrorHistorial));
        }
       


    }



}
