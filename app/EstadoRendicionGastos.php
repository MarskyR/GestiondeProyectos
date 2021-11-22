<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoRendicionGastos extends Model
{
    protected $table = "estado_rendicion_gastos";
    protected $primaryKey ="codEstadoRendicion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre'];


}
