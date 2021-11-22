<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $table = "sede";
    protected $primaryKey ="codSede";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre','codEmpleadoAdministrador','esSedePrincipal'];

    public function getAdministrador(){
        return Empleado::findOrFail($this->codEmpleadoAdministrador);
    }


    public function getMensajeAdministrador(){
        if($this->esSedePrincipal==1)
            return "Administrador de CEDEPAS Norte";
        return "Administrador de Filial";

    }

    
}
