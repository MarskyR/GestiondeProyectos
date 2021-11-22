<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArchivoSolicitud extends Model
{
    protected $table = "archivo_solicitud";
    protected $primaryKey ="codArchivoSolicitud";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombreDeGuardado','nombreAparente','codSolicitud'];


    public function eliminarArchivo(){
        $solicitud = SolicitudFondos::findOrFail($this->codSolicitud);

        $nombreArchivoABorrar = $this->nombreDeGuardado;
        $this->delete(); //primero eliminamos el archivo de la base de datos
        Storage::rename('solicitudes/'.$nombreArchivoABorrar, 'solicitudes/EsteSeBorrara.marac');    //nombre momentaneo para no tener problema al renombrar los otros 
        

        //ahora recorremos los archivos de la reposicion para asignarles el nuevo nombre (para los que estaban adelante de esta, si antes eran 3 ahora serán 2)
        //eliminacion clasica de vector
        $listaArchivos = ArchivoSolicitud::where('codSolicitud','=',$solicitud->codSolicitud)->get();
        
        
        $j = 1;
        foreach ($listaArchivos as $itemArchivo) {
            $nombreViejo = $itemArchivo->nombreDeGuardado;
            $nombreNuevo = $solicitud->getNombreGuardadoNuevoArchivo($j);
            //Debug::mensajeSimple("j=".$j.'Nombre viejo='.$nombreViejo."  nombreNuevo=".$nombreNuevo);

            $itemArchivo->nombreDeGuardado = $nombreNuevo;
            $itemArchivo->save();
            if($nombreNuevo!=$nombreViejo)
                Storage::rename('solicitudes/'.$nombreViejo, 'solicitudes/'.$nombreNuevo);     

            $j++;

        }

        Storage::disk('solicitudes')->delete("EsteSeBorrara.marac"); //ahora borramos el archivo en sí del sistema


    }

}
