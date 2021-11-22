<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelacionProyectoObjMilenio extends Model
{
    protected $table = "relacion_proyecto_objmilenio";
    protected $primaryKey ="codRelacion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['','nombre'];

    public function getProyecto(){
        return Proyecto::findOrFail($this->codProyecto);
    }
    public function getObjetivoMilenio(){
        return ObjetivoMilenio::findOrFail($this->codObjetivoMilenio);
    }
}
