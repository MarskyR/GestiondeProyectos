<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPersonaJuridica extends Model
{
    protected $table = "tipo_persona_juridica";
    protected $primaryKey ="codTipoPersonaJuridica";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre','siglas'];

    public function getDescripcion(){
        return $this->nombre." ".$this->siglas;
    }

}
