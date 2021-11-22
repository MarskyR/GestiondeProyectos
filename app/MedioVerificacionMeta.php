<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MedioVerificacionMeta extends Model
{
    protected $table = "medio_verificacion_meta";
    protected $primaryKey ="codMedioVerificacion";
    
    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['descripcion','nombreGuardado','nombreAparente','codMetaEjecutada'];
    
    public function eliminarArchivo(){
        $meta = MetaEjecutada::findOrFail($this->codMetaEjecutada);

        $nombreArchivoABorrar = $this->nombreGuardado;
        $this->delete(); //primero eliminamos el archivo de la base de datos
        Storage::rename('proyectos/mediosVerificacion/Metas/'.$nombreArchivoABorrar, 'proyectos/mediosVerificacion/Metas/EsteSeBorrara.marac');    //nombre momentaneo para no tener problema al renombrar los otros 
                

        //ahora recorremos los archivos de la reposicion para asignarles el nuevo nombre (para los que estaban adelante de esta, si antes eran 3 ahora serán 2)
        //eliminacion clasica de vector
        $listaArchivos = MedioVerificacionMeta::where('codMetaEjecutada','=',$meta->codMetaEjecutada)->get();
        
        
        $j = 1;
        foreach ($listaArchivos as $itemArchivo) {
            $nombreViejo = $itemArchivo->nombreGuardado;
            $nombreNuevo = $meta->getNombreGuardadoNuevoArchivo($j);
            //Debug::mensajeSimple("j=".$j.'Nombre viejo='.$nombreViejo."  nombreNuevo=".$nombreNuevo);

            $itemArchivo->nombreGuardado = $nombreNuevo;
            $itemArchivo->save();
            if($nombreNuevo!=$nombreViejo)
                Storage::rename('proyectos/mediosVerificacion/Metas/'.$nombreViejo, 'proyectos/mediosVerificacion/Metas/'.$nombreNuevo);     

            $j++;

        }

        Storage::disk('mediosVerificacionMetas')->delete("EsteSeBorrara.marac"); //ahora borramos el archivo en sí del sistema


    }

    
    public function getProyecto(){
        return $this->getMetaEjecutada()->getIndicadorActividad()->getActividad()->getResultadoEsperado()->getProyecto();
    }

    public function getMetaEjecutada(){
        return MetaEjecutada::findOrFail($this->codMetaEjecutada);
    }
}
