<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelacionProyectoObj extends Model
{
    public $timestamps = false;

    protected $table = 'relacion_proyecto_objestrategicos';

    protected $primaryKey = 'codRelacion';

    protected $fillable = [
        'codObjetivoEstrategico','codProyecto','porcentajeDeAporte'
    ];

    public function getObjetivoEstrategico(){
        return ObjetivoEstrategico::findOrFail($this->codObjetivoEstrategico);
    }


    
}
