<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjetivoMilenio extends Model
{
    protected $table = "objetivo_milenio";
    protected $primaryKey ="codObjetivoMilenio";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = [''];
    
}
