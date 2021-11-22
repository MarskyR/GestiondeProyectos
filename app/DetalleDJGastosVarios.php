<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleDJGastosVarios extends Model
{
    protected $table = "detalle_dj_gastosvarios";
    protected $primaryKey ="codDetalleVarios";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['fecha','concepto','importe','codDJGastosVarios'];

    public function getFecha(){
        return date("d/m/Y",strtotime($this->fecha));


        //return str_replace('-','/',$this->fecha);

    }
    
}
