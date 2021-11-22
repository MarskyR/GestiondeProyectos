<?php

namespace App\Http\Controllers;

use App\Configuracion;
use App\Debug;
use App\ErrorHistorial;
use App\Http\Controllers\Controller;
use App\IndicadorResultado;
use App\MedioVerificacionResultado;
use App\RespuestaAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class MedioVerificacionResultadoController extends Controller
{
    //

    public function descargar($codMedio){
        $medio = MedioVerificacionResultado::findOrFail($codMedio);
        return Storage::download("/proyectos/mediosVerificacion/Resultados/".$medio->nombreGuardado,
            $medio->nombreAparente);
    }



    /* AGREGAR Y EDITAR */
    /* 

    
    */
    public function store(Request $request){
        try {
            DB::beginTransaction();
            if($request->codMedioVerificacion=="0"){
                $medio = new MedioVerificacionResultado();
                $indicador = IndicadorResultado::findOrFail($request->ComboBoxIndicadoresResultados);
                $medio->descripcion           = $request->descripcionNuevoMedio;                    
                $medio->codIndicadorResultado = $request->ComboBoxIndicadoresResultados;    
                $j = $indicador->getCantidadMediosVerificacion();
                $medio->nombreGuardado        = $indicador->getNombreGuardadoNuevoArchivo($j+1);    
                $medio->nombreAparente = "";
                $mensaje  ="agregado";
            }else{//SE ESTÁ EDITANDO
                $medio = MedioVerificacionResultado::findOrFail($request->codMedioVerificacion);
                $indicador = IndicadorResultado::findOrFail($request->ComboBoxIndicadoresResultados);
                $medio->descripcion           = $request->descripcionNuevoMedio;                                 
                $medio->codIndicadorResultado = $request->ComboBoxIndicadoresResultados;    
                $mensaje = "editado";
            }   
            $medio->save();
            //Debug::mensajeSimple('22a '.$request->nombreArchivoMedioVerificacion);
            //si se ingresó un archivo
            if($request->nombreArchivoMedioVerificacion!=""){

                $medio->nombreAparente        = $request->nombreArchivoMedioVerificacion; 
                $medio->save();
                $archivo = $request->file('filenamesMedio');
            
                $fileget = \File::get( $archivo );
                Storage::disk('mediosVerificacionResultados')
                    ->put($medio->nombreGuardado,$fileget );
                DB::commit();
                //aquí sí redirigimos a la página
                return redirect()->route('GestiónProyectos.editar',$medio->getProyecto()->codProyecto)
                ->with('datos','Se ha '.$mensaje.' el medio de verificación "'.$medio->descripcion.'" exitosamente.');


            }else{
                DB::commit();
                //retornamos solo un mensaje para ser mostrado en javascript
                return RespuestaAPI::respuestaOk('Se ha '.$mensaje.' el medio de verificación "'.$medio->descripcion.'" exitosamente.');
            }
        } catch (\Throwable $th) {
            Debug::mensajeError(' MEDIO VERIFICACION RESULTADO CONTROLLER store' ,$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
        }
    }

    

    public function eliminar($codMedio){
        try {
            DB::beginTransaction();
            $medio = MedioVerificacionResultado::findOrFail($codMedio);
            $nombre = $medio->descripcion;
            if($medio->tieneArchivo()) //si tiene archivo
                $medio->eliminarArchivo();
            $medio->delete();
            db::commit();

            return RespuestaAPI::respuestaOk('Se ha Eliminado el medio de verificacion "'.$nombre.'" exitosamente.');
        } catch (\Throwable $th) {
            Debug::mensajeError(' MEDIO VERIFICACION RESULTADO CONTROLLER eliminar' ,$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codMedio);
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));

        }

        /*  */

        
    }

    



}
