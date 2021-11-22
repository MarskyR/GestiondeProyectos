<?php

namespace App\Http\Controllers;

use App\Configuracion;
use App\Debug;
use App\Empleado;
use App\ErrorHistorial;
use App\Fecha;
use App\Http\Controllers\Controller;
use App\IndicadorActividad;
use App\MedioVerificacionMeta;
use App\Mes;
use App\MetaEjecutada;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/* EN ESTE INDICADOR SE GESTIONAN LAS MESTAS */
class IndicadorActividadController extends Controller
{
    

    /* GERENTE */
    /* Despliega la vista de las metas con su gráfico  */
    public function verSeguimientoGrafico($codIndicador){

        date_default_timezone_set('America/Lima');
        setlocale(LC_TIME, "es_PE");
        $indicador = IndicadorActividad::findOrFail($codIndicador);
        $listaMetas = MetaEjecutada::where('codIndicadorActividad','=',$indicador->codIndicadorActividad)->get();

        $arrFaltante=[];
        $arr=[];
        $indices=[];
        $colores=[];
        $constantes=[];
        foreach ($listaMetas as $itemmeta) {
            $arr[]=array('y'=>date('Y-m',strtotime($itemmeta->mesAñoObjetivo)), 'a'=>$itemmeta->cantidadProgramada, 'b'=>$itemmeta->cantidadEjecutada);
                $indices[]=$itemmeta->codMetaEjecutada;
                $colores[]=$itemmeta->getColor();
                $constantes[]=array($itemmeta->desviacion,$itemmeta->tasaEjecucion);
            if($itemmeta->cantidadEjecutada==0){
                $arrFaltante[]=array($itemmeta->codMetaEjecutada,date('Y-m',strtotime($itemmeta->mesAñoObjetivo)),$itemmeta->cantidadProgramada);
            }
        }
       
    
        return view('Proyectos.Gerente.SeguimientoGraficoIndicador',compact('arr','arrFaltante','indices','colores','constantes','indicador'));
    }



    /* VISTA DE LA UGE PARA CREAR LOS REGISTROS DE LAS METAS PROGRAMADAS */
    public function RegistrarMetas($codIndicador){
        
        $indicador = IndicadorActividad::findOrFail($codIndicador);
        $listaMetas = MetaEjecutada::where('codIndicadorActividad','=',$indicador->codIndicadorActividad)
        ->orderBy('mesAñoObjetivo','ASC')
        ->get();

        $listaMeses = Mes::All();
        $meses=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $arrFaltante=[];
        $temp=[];

        $arr=[];
        $indices=[];
        foreach ($listaMetas as $itemmeta) {
            $temp[]=date('n',strtotime($itemmeta->mesAñoObjetivo));
            $arr[]=array('y'=>date('Y-m',strtotime($itemmeta->mesAñoObjetivo)), 'a'=>$itemmeta->cantidadProgramada);
            $indices[]=$itemmeta->codMetaEjecutada;
        }
        for ($i=1; $i <= 12; $i++) { 
            $band=true;
            for ($j=0; $j < count($temp); $j++) { 
                if($temp[$j]==$i) $band=false;
            }
            if($band) $arrFaltante[]=array($i,$meses[$i-1]);
        }

        /* 
        
        

        
        
        
        
        */

    
        return view('Proyectos.UGE.ProgramarMetas',compact('arr','arrFaltante','indices','indicador','listaMeses','listaMetas'));
    }



    /* 
    PROGRAMAR METAS
    Crea una nueva meta programada 
    Crea un registro en meta_ejecutada
    */
    public function registrarMetaProgramada(Request $request){
        
        $indicador = IndicadorActividad::findOrFail($request->codIndicadorActividad);
        /*
         Validamos que no haya una meta ya en ese mes 
        */
        if($indicador->tieneMetaEn($request->añoNuevaMeta,$request->mesNuevaMeta)){
            return redirect()->route('IndicadorActividad.RegistrarMetas',$indicador->codIndicadorActividad)->with('datos','ERROR: El mes seleccionado ya tiene una meta programada.');
        }

        $fechaObjetivo =$request->añoNuevaMeta."-".$request->mesNuevaMeta."-01";
        $proyecto = $indicador->getProyecto();

        if(!Fecha::dentroDeFechasProyecto($fechaObjetivo,$proyecto)){
            return redirect()->route('IndicadorActividad.RegistrarMetas',$indicador->codIndicadorActividad)->with('datos','ERROR : La meta debe ser programada dentro de los meses de ejecución del proyecto.');

        }

        $meta=new MetaEjecutada();
        $meta->cantidadProgramada=$request->cantidadProgramada;
        $meta->ejecutada = 0;
        $meta->cantidadEjecutada=0; //ESTANDARIZAMOS CANTIDAD EJECUTADA 0 cuando aun no se la ha ingresado
        $meta->codEmpleado=Empleado::getEmpleadoLogeado()->codEmpleado;
        $meta->fechaRegistroProgramacion=new DateTime();

        $meta->mesAñoObjetivo=$fechaObjetivo;

        $meta->codIndicadorActividad=$indicador->codIndicadorActividad;
        $meta->desviacion=null;
        $meta->tasaEjecucion=null;
        $meta->esReprogramada = 0;
        if($request->esReprogramada == "on"){
            $meta->esReprogramada = 1;
        }
        $meta->save();


        $indicador->meta = $indicador->getMetaTotal();
        $indicador->saldoPendiente = $indicador->calcularSaldoPendiente();
        $indicador->save();


        return redirect()->route('IndicadorActividad.RegistrarMetas',$indicador->codIndicadorActividad)->with('datos','Se ha registrado la meta programada.');
    
    
    }


    /* Funcion consumida desde JS, retorna una vista pequeña con los archivos de la meta */
    public function vistaArchivosDeMeta($codMeta){
        $meta = MetaEjecutada::findOrFail($codMeta);
        return $meta->getVistaArchivos();
    }
    
    /* Editar meta de la tabla */
    public function editarMeta($id){
        $vector = explode('*',$id);

        $meta=MetaEjecutada::findOrFail($vector[0]);

        $nombreMes = $meta->getMesYAñoEscrito();

        $meta->mesAñoObjetivo=$vector[1]."-".$vector[2]."-01";
        $meta->cantidadProgramada=$vector[3];
        $meta->save();

        
        /*
        $indicador = IndicadorActividad::findOrFail($meta->codIndicadorActividad);
        $nombreMes = $meta->getMesYAñoEscrito();
        $meta->delete();

        $indicador->meta = $indicador->getMetaTotal();
        $indicador->save();
        */

        return redirect()->route('IndicadorActividad.RegistrarMetas',$meta->codIndicadorActividad)
        ->with('datos','Se ha editado la meta de '.$nombreMes.' exitosamente.');
    }

    /* Elimina todo el registro de la tabla */
    public function eliminarMeta($id){
        $meta=MetaEjecutada::findOrFail($id);
        
        $indicador = IndicadorActividad::findOrFail($meta->codIndicadorActividad);
        $nombreMes = $meta->getMesYAñoEscrito();
        $meta->delete();

        $indicador->meta = $indicador->getMetaTotal();
        $indicador->save();


        return redirect()->route('IndicadorActividad.RegistrarMetas',$meta->codIndicadorActividad)
        ->with('datos','Se ha eliminado la meta de '.$nombreMes.' exitosamente.');
    }





    //para registrar solo el dato de cantidad programada (lo pase de get a post)
    public function registrarCantidadEjecutada(Request $request){
        try {
            DB::beginTransaction(); 
            $meta=MetaEjecutada::findOrFail($request->modalCodMetaEjecutada);
            $meta->cantidadEjecutada=$request->modalCantidadEjecutada;
            $meta->fechaRegistroEjecucion=new DateTime();
            $meta->desviacion=$meta->cantidadProgramada-$meta->cantidadEjecutada;
            $meta->tasaEjecucion=$meta->cantidadEjecutada/$meta->cantidadProgramada*100.0;
            $meta->ejecutada = 1;
            $meta->save();

            $indicador = IndicadorActividad::findOrFail($meta->codIndicadorActividad);
            $indicador->saldoPendiente = $indicador->calcularSaldoPendiente();
            $indicador->save();

            
            if( $request->nombresArchivos!='' ){
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0;
                foreach ($request->file('filenames') as $archivo)
                {
                    
                    $nombreArchivoGuardado = $meta->getNombreGuardadoNuevoArchivo($j+1);
                    Debug::mensajeSimple('el nombre de guardado de la imagen es:'.$nombreArchivoGuardado);
                    
                    
                    $medioVer = new MedioVerificacionMeta();
                    $medioVer->codMetaEjecutada = $meta->codMetaEjecutada;
                    $medioVer->nombreGuardado = $nombreArchivoGuardado;
                    $medioVer->nombreAparente = $nombresArchivos[$j];
                    $medioVer->save();

                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('mediosVerificacionMetas')
                    ->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
            }


            db::commit();
        
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$meta->getProyecto()->codProyecto)
            ->with('datos','Meta registrada exitosamente.');

        } catch (\Throwable $th) {
             
            Debug::mensajeError('INDICADOR ACTIVIDAD CONTROLLER : registrarCantidadEjecutada',$th);
            
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$meta->getProyecto()->codProyecto)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));


        }



    }


    public function updateMeta(Request $request){
    /* copiar codigo del update de rendicion */
        
        try {
            DB::beginTransaction();
            $meta = MetaEjecutada::findOrFail($request->codMetaParaArchivos);
            $meta->cantidadEjecutada = $request->nuevaCantidadEjecutada;
            $meta->desviacion=$meta->cantidadProgramada-$meta->cantidadEjecutada;
            $meta->tasaEjecucion=$meta->cantidadEjecutada/$meta->cantidadProgramada*100.0;
            $meta->save();

            $indicador = IndicadorActividad::findOrFail($meta->codIndicadorActividad);
            $indicador->saldoPendiente = $indicador->calcularSaldoPendiente();
            $indicador->save();
            

            //SOLO BORRAMOS TODO E INSERTAMOS NUEVOS ARCHIVOS SI ES QUE SE INGRESÓ NUEVOS
            if( $request->nombresArchivos!='' ){
                Debug::mensajeSimple("o yara/".$request->tipoIngresoArchivos);
                if($request->tipoIngresoArchivos=="1")
                {//AÑADIR
                    
                }else{//SOBRESRIBIR
                    $meta->borrarArchivos();  //A
                }

                $cantidadArchivosYaExistentes = $meta->getCantidadArchivos();

                
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0; //A
                

                foreach ($request->file('filenames') as $archivo)
                {   
                    
                    $nombreArchivoGuardado = $meta->getNombreGuardadoNuevoArchivo($cantidadArchivosYaExistentes + $j+1);
                    Debug::mensajeSimple('el nombre de guardado de la imagen es:'.$nombreArchivoGuardado);

                    $medioVer = new MedioVerificacionMeta();
                    $medioVer->codMetaEjecutada = $meta->codMetaEjecutada;
                    $medioVer->nombreGuardado = $nombreArchivoGuardado;
                    $medioVer->nombreAparente = $nombresArchivos[$j];
                    $medioVer->save();


                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('mediosVerificacionMetas')
                    ->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
            }

            $meta->save();

            DB::commit();
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$meta->getProyecto()->codProyecto)
            ->with('datos','Meta actualizada exitosamente.');

        } catch (\Throwable $th) {
            Debug::mensajeError('INDICADOR ACTIVIDAD CONTROLLER : update meta',$th);
            
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$meta->getProyecto()->codProyecto)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }



    }

    public function eliminar($id){
        $meta=MetaEjecutada::findOrFail($id);
        $meta->cantidadEjecutada=0;
        $meta->desviacion=null;
        $meta->tasaEjecucion=null;
        $meta->save();

        return redirect()->route('IndicadorActividad.Ver',$meta->codIndicadorActividad);
    }




    //se le pasa el codigo del archivo 
    function descargarMedioVerificacion($codMedioVerificacion){
        $archivo = MedioVerificacionMeta::findOrFail($codMedioVerificacion);
        return Storage::download("/proyectos/mediosVerificacion/Metas/".$archivo->nombreGuardado,$archivo->nombreAparente);

    }


    //se le pasa el codigo del archivo 
    function eliminarMedioVerificacion($codMedioVerificacion){
        
        

        try {
            db::beginTransaction();
            $archivo = MedioVerificacionMeta::findOrFail($codMedioVerificacion);
        
            $nombreArchivEliminado = $archivo->nombreAparente;
            $meta = MetaEjecutada::findOrFail($archivo->codMetaEjecutada);
            $archivo->eliminarArchivo();
            DB::commit();
        
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$meta->getProyecto()->codProyecto)
            ->with('datos','Archivo '.$nombreArchivEliminado. ' eliminado exitosamente de la meta.');
        } catch (\Throwable $th) {
            Debug::mensajeError(' REPOSICION GASTOS CONTROLLER Eliminar archivo' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codMedioVerificacion);
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$meta->getProyecto()->codProyecto)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }












    }





}
