<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MedioVerificacionResultado extends Model
{
    protected $table = "medio_verificacion_resultado";
    protected $primaryKey ="codMedioVerificacion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['descripcion','nombreGuardado','nombreAparente','codIndicadorResultado'];
    

    /* 
        SOLICITUD -> INDICADOR
        ARCHIVOSOL-> MEDIO VER
    
    */
    public function eliminarArchivo(){
        $indicador = IndicadorResultado::findOrFail($this->codIndicadorResultado);
        Debug::mensajeSimple('indicador antes tiene'. $indicador->getCantidadMediosVerificacion());

        $nombreArchivoABorrar = $this->nombreGuardado;
        $this->delete(); //primero eliminamos el archivo de la base de datos
        Debug::mensajeSimple('indicador luego tiene'. $indicador->getCantidadMediosVerificacion());
        
        Storage::rename('proyectos/mediosVerificacion/Resultados/'.$nombreArchivoABorrar, 'proyectos/mediosVerificacion/Resultados/EsteSeBorrara.marac');    //nombre momentaneo para no tener problema al renombrar los otros 
        

        //ahora recorremos los archivos de la reposicion para asignarles el nuevo nombre (para los que estaban adelante de esta, si antes eran 3 ahora serán 2)
        //eliminacion clasica de vector
        $listaMediosVerificacion = MedioVerificacionResultado::where('codIndicadorResultado','=',$indicador->codIndicadorResultado)->get();
        
        Debug::mensajeSimple('lista =', json_encode($listaMediosVerificacion) );
        $j = 1;
        foreach ($listaMediosVerificacion as $itemArchivo) {
            $nombreViejo = $itemArchivo->nombreGuardado;
            $nombreNuevo = $indicador->getNombreGuardadoNuevoArchivo($j);
            Debug::mensajeSimple("j=".$j.' Nombre viejo='.$nombreViejo."  nombreNuevo=".$nombreNuevo);
            
            $itemArchivo->nombreGuardado = $nombreNuevo;
            $itemArchivo->save();
            if($nombreNuevo!=$nombreViejo)
                Storage::rename('proyectos/mediosVerificacion/Resultados/'.$nombreViejo, 'proyectos/mediosVerificacion/Resultados/'.$nombreNuevo);     

            $j++;

        }

        Storage::disk('mediosVerificacionResultados')->delete("EsteSeBorrara.marac"); //ahora borramos el archivo en sí del sistema


    }
    

    
    public function tieneArchivo(){
        return $this->nombreAparente!="";
    }
    public function getProyecto(){
        return $this->getIndicadorResultado()->getResultadoEsperado()->getProyecto();
    }

    public function getIndicadorResultado(){
        return IndicadorResultado::findOrFail($this->codIndicadorResultado);
    }
    
}
