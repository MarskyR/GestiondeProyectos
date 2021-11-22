<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoArchivoProyecto extends Model
{
    
    protected $table = "tipo_archivo_proyecto";
    protected $primaryKey ="codTipoArchivoProyecto";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre'];
}
