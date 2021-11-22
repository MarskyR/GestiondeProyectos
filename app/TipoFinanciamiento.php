<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoFinanciamiento extends Model
{
    

    protected $table = "tipo_financiamiento";
    protected $primaryKey ="codTipoFinanciamiento";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre'];


}
