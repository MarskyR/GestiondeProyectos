<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleDJGastosMovilidad extends Model
{
    protected $table = "detalle_dj_gastosmovilidad";
    protected $primaryKey ="codDetalleMovilidad";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['fecha','lugar','detalle','importe','codDJGastosMovilidad'];

    public function getFecha(){
        return date("d/m/Y",strtotime($this->fecha));
        //return str_replace('-','/',$this->fecha);
    }


}
