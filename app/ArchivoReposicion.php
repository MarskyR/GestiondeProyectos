<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class ArchivoReposicion extends Model
{
    protected $table = "archivo_repo";
    protected $primaryKey ="codArchivoRepo";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['codArchivoRepo','nombreDeGuardado','nombreAparente','codReposicionGastos'];

    public function getReposicion(){
        return ReposicionGastos::findOrFail($this->codReposicionGastos);
    }

    public function eliminarArchivo(){

        $reposicion = ReposicionGastos::findOrFail($this->codReposicionGastos);

        
        $nombreArchivoABorrar = $this->nombreDeGuardado;
        $this->delete(); //primero eliminamos el archivo de la base de datos
        Storage::rename('comprobantes/reposiciones/'.$nombreArchivoABorrar, 'comprobantes/reposiciones/EsteSeBorrara.marac');    //nombre momentaneo para no tener problema al renombrar los otros 
                

        //ahora recorremos los archivos de la reposicion para asignarles el nuevo nombre (para los que estaban adelante de esta, si antes eran 3 ahora serán 2)
        //eliminacion clasica de vector
        $listaArchivos = ArchivoReposicion::where('codReposicionGastos','=',$reposicion->codReposicionGastos)->get();
        
        
        $j = 1;
        foreach ($listaArchivos as $itemArchivo) {
            $nombreViejo = $itemArchivo->nombreDeGuardado;
            $nombreNuevo = $reposicion->getNombreGuardadoNuevoArchivo($j);
            //Debug::mensajeSimple("j=".$j.'Nombre viejo='.$nombreViejo."  nombreNuevo=".$nombreNuevo);

            $itemArchivo->nombreDeGuardado = $nombreNuevo;
            $itemArchivo->save();
            if($nombreNuevo!=$nombreViejo)
                Storage::rename('comprobantes/reposiciones/'.$nombreViejo, 'comprobantes/reposiciones/'.$nombreNuevo);     

            $j++;

        }

        Storage::disk('reposiciones')->delete("EsteSeBorrara.marac"); //ahora borramos el archivo en sí del sistema


    }

}
