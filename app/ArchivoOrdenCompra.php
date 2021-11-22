<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArchivoOrdenCompra extends Model
{
    protected $table = "archivo_orden";
    protected $primaryKey ="codArchivoOrden";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombreGuardado','nombreAparente','codOrdenCompra'];


    public function eliminarArchivo(){
        $orden = OrdenCompra::findOrFail($this->codOrdenCompra);

        $nombreArchivoABorrar = $this->nombreGuardado;
        $this->delete(); //primero eliminamos el archivo de la base de datos
        Storage::rename('ordenes/'.$nombreArchivoABorrar, 'ordenes/EsteSeBorrara.marac');    //nombre momentaneo para no tener problema al renombrar los otros 
        

        //ahora recorremos los archivos de la reposicion para asignarles el nuevo nombre (para los que estaban adelante de esta, si antes eran 3 ahora serán 2)
        //eliminacion clasica de vector
        $listaArchivos = ArchivoOrdenCompra::where('codOrdenCompra','=',$orden->codOrdenCompra)->get();
        
        
        $j = 1;
        foreach ($listaArchivos as $itemArchivo) {
            $nombreViejo = $itemArchivo->nombreGuardado;
            $nombreNuevo = $orden->getNombreGuardadoNuevoArchivo($j);
            //Debug::mensajeSimple("j=".$j.'Nombre viejo='.$nombreViejo."  nombreNuevo=".$nombreNuevo);

            $itemArchivo->nombreGuardado = $nombreNuevo;
            $itemArchivo->save();
            if($nombreNuevo!=$nombreViejo)
                Storage::rename('ordenes/'.$nombreViejo, 'ordenes/'.$nombreNuevo);     

            $j++;

        }

        Storage::disk('ordenes')->delete("EsteSeBorrara.marac"); //ahora borramos el archivo en sí del sistema


    }

}
