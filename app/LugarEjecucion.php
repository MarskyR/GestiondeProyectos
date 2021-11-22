<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LugarEjecucion extends Model
{
    protected $table = "lugar_ejecucion";
    protected $primaryKey ="codLugarEjecucion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['codProyecto','codDistrito','descripcion'];


    //Retorna la zona y el distrito
    public function getZonaDistrito(){
        return $this->zona.",".$this->getDistrito()->nombre;
    }

    public function getDistrito(){

        return Distrito::findOrFail($this->codDistrito);

    }

    public function getProvincia(){
        return $this->getDistrito()->getProvincia();

    }

    public function getDepartamento(){
        return $this->getProvincia()->getDepartamento();

    }

    
}
