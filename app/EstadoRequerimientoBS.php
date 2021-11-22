<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoRequerimientoBS extends Model
{
    protected $table = "estado_requerimiento_bs";
    protected $primaryKey ="codEstadoRequerimiento";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codEstadoRequerimiento','nombre'];
}
