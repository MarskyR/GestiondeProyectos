<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    
    protected $table = "unidad_medida";
    protected $primaryKey ="codUnidadMedida";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codUnidadMedida','nombre'];



    

}
