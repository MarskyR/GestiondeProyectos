<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{
    
    protected $table = "estado_proyecto";
    protected $primaryKey ="codEstadoProyecto";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = [ 'codEstadoProyecto', 'nombre'];
}
