<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProyectoContador extends Model
{
    public $timestamps = false;

    protected $table = 'proyecto_contador';

    protected $primaryKey = 'codProyectoContador';

    protected $fillable = [
        'codEmpleadoContador','codProyecto'
    ];

    public function getContador(){
        return Empleado::findOrFail($this->codEmpleadoContador);
    }
    public function getProyecto(){

        return Proyecto::findOrFail($this->codProyecto);
    }
}
