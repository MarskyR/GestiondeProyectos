<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoReposicionGastos extends Model
{
    protected $table = "estado_reposicion_gastos";
    protected $primaryKey ="codEstadoReposicion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre'];

}
