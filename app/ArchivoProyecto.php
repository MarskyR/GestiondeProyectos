<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;



class ArchivoProyecto extends Model
{
    protected $table = "archivo_proyecto";
    protected $primaryKey ="codArchivoProyecto";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    //                      con este nombre se guarda en la carpeta
    protected $fillable = [ 'nombreDeGuardado', 
    'nombreAparente', //este es el nombre con el que se sube el archivo, y se guarda en la bd
     'codProyecto','fechaHoraSubida'];   

     //FORMATO PARA NOMBRE DE GUARDADO
     //ArchProy-000000-00.marac

    public function getTipoArchivo(){
        return TipoArchivoProyecto::findOrFail($this->codTipoArchivoProyecto);
    }
    
     public function eliminarArchivo(){
        $proyecto = Proyecto::findOrFail($this->codProyecto);

        $nombreArchivoABorrar = $this->nombreDeGuardado;
        $this->delete(); //primero eliminamos el registro del archivo de la base de datos
        Storage::rename('proyectos/'.$nombreArchivoABorrar, 'proyectos/EsteSeBorrara.marac');    //nombre momentaneo para no tener problema al renombrar los otros 

        //ahora recorremos los archivos de la reposicion para asignarles el nuevo nombre (para los que estaban adelante de esta, si antes eran 3 ahora serán 2)
        //eliminacion clasica de vector
        $listaArchivos = ArchivoProyecto::where('codProyecto','=',$proyecto->codProyecto)->get();
        
        
        $j = 1;
        foreach ($listaArchivos as $itemArchivo) {
            $nombreViejo = $itemArchivo->nombreDeGuardado;
            $nombreNuevo = $proyecto->getNombreGuardadoNuevoArchivo($j);
            //Debug::mensajeSimple("j=".$j.'Nombre viejo='.$nombreViejo."  nombreNuevo=".$nombreNuevo);

            $itemArchivo->nombreDeGuardado = $nombreNuevo;
            $itemArchivo->save();
            if($nombreNuevo!=$nombreViejo)
                Storage::rename('proyectos/'.$nombreViejo, 'proyectos/'.$nombreNuevo);     

            $j++;

        }

        Storage::disk('proyectos')->delete("EsteSeBorrara.marac"); //ahora borramos el archivo en sí del sistema


    }

    
}
