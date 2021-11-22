<?php

namespace App\Http\Controllers;

use App\Configuracion;
use App\ErrorHistorial;
use App\Http\Controllers\Controller;
use App\ObjetivoEstrategico;
use App\PlanEstrategicoInstitucional;
use App\Proyecto;
use App\RelacionProyectoObj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanEstrategicoInstitucionalController extends Controller
{
    
    public function listar(){
        $planesEstrategicos = PlanEstrategicoInstitucional::All();
        return view('PlanesEstrategicos.ListarPlanesEstrategicos',compact('planesEstrategicos'));
    }

    public function editar($codPEI){
        $PEI = PlanEstrategicoInstitucional::findOrFail($codPEI);
        $listaObjetivos = ObjetivoEstrategico::where('codPEI','=',$codPEI)
            ->orderBy('item','ASC')
            ->get();
        
        return view('PlanesEstrategicos.EditarPlanEstrategico',compact('PEI','listaObjetivos'));
    }

    public function crear(){


        return view('PlanesEstrategicos.CrearPlanEstrategico');
    }

    public function listarObjetivos($codPEI){

        $objetivos = ObjetivoEstrategico::where('codPEI','=',$codPEI)->get();
        return $objetivos;

    }


    public function guardar(Request $request){
        $PEI = new PlanEstrategicoInstitucional();
        $PEI->añoInicio = $request->añoInicio;
        $PEI->añoFin = $request->añoFin;
        $PEI->save();
        
        $codPEI = (PlanEstrategicoInstitucional::latest('codPEI')->first())->codPEI;
        
        $i = 0;
        $cantidadFilas = $request->cantElementos;
        while ($i< $cantidadFilas ) {
            $detalle=new ObjetivoEstrategico();
            $detalle->codPEI= $codPEI;
            $detalle->descripcion= $request->get('descripcion'.$i);
            $detalle->item= $request->get('item'.$i);
            $detalle->nombre= $request->get('nombre'.$i);
            
            $detalle->save();
                    
            $i++;
        }    
        
        return redirect()->route('PlanEstrategico.listar')->with('datos','PEI '.$PEI->getPeriodo().' creado exitosamente');

    }



    public function actualizar(Request $request){
        
        $codPEI = $request->codPEI;
        $PEI = PlanEstrategicoInstitucional::findOrFail($codPEI);
        $PEI->añoInicio = $request->añoInicio;
        $PEI->añoFin = $request->añoFin;
        
        $PEI->save();
        $PEI->eliminarObjetivos();

        $i = 0;
        $cantidadFilas = $request->cantElementos;
        while ($i< $cantidadFilas ) {

            $detalle=new ObjetivoEstrategico();
            $detalle->codPEI= $codPEI;
            $detalle->descripcion= $request->get('descripcion'.$i);
            $detalle->item= $request->get('item'.$i);
            $detalle->nombre= $request->get('nombre'.$i);
            
            $detalle->save();
                    
            $i++;
        }    
        
        return redirect()->route('PlanEstrategico.listar')->with('datos','PEI '.$PEI->getPeriodo().' Actualizado');

    }




    public function eliminar($codPEI){
        
        $pei = PlanEstrategicoInstitucional::findOrFail($codPEI);
        $listaProyectosQueUsanPEI = Proyecto::where('codPEI','=',$codPEI)->get();
        if(count($listaProyectosQueUsanPEI)!=0){
            return redirect()->route('PlanEstrategico.listar')->with('datos','ERROR: El pei está siendo usado por el proyecto "'.$listaProyectosQueUsanPEI[0]->nombre.'" .');

        }
        $pei->delete();
        return redirect()->route('PlanEstrategico.listar')->with('datos','PEI eliminado exitosamente');

    }



    /* 
        Recorre todos los proyectos
        en cada uno, genera las relaciones  (proyecto - objEstrategico) (tabla relacion), 
        que sean necesarias para completar la cantidad de obj estrategicos de su PEI    
    */
    public function generarRelacionesProyectosYObjetivosEstrategicos(){
        try{
            DB::beginTransaction();
            $listaProyectos = Proyecto::All();
            $msj = "";
            foreach ($listaProyectos as $proyecto) {
                $msj.=$proyecto->nombre."->";
                $objetivosDeSuPEI = $proyecto->getPEI()->getListaObj();
                foreach ($objetivosDeSuPEI as $obj) { //cada uno debe tener un registro en la tabla relacion
                    $relaciones = RelacionProyectoObj::where('codProyecto','=',$proyecto->codProyecto)
                        ->where('codObjetivoEstrategico','=',$obj->codObjetivoEstrategico)
                        ->get();
                    if(count($relaciones)==0){ //SI NO EXISTE ESA RELACION, LA CREAMOS
                        $nuevaRel = new RelacionProyectoObj();
                        $nuevaRel->codProyecto = $proyecto->codProyecto;
                        $nuevaRel->codObjetivoEstrategico = $obj->codObjetivoEstrategico;
                        $nuevaRel->porcentajeDeAporte =  0;
                        $nuevaRel->save();
                        $msj.=$obj->codObjetivoEstrategico.",";
                    }
                }
                $msj.=" //";
            }
            
            DB::commit();
            return redirect()->route('PlanEstrategico.listar')->with('datos',"Se han generado las relaciones faltantes exitosamente. $msj");

        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                            app('request')->route()->getAction(),
                                                            ""
                                                            );
            return redirect()->route('PlanEstrategico.listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }

    }


}
