<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelacionPersonaNaturalActividad extends Model
{
    public $timestamps = false;

    protected $table = 'relacion_personanatural_actividad';

    protected $primaryKey = 'codRelacion';

    protected $fillable = ['codActividadPrincipal','codPersonaNatural'];

    public function getActividad(){
        return ActividadPrincipal::findOrFail($this->codActividadPrincipal);

    }

    public function getPersona(){
        return PersonaNaturalPoblacion::findOrFail($this->codPersonaNatural);
    }
}
