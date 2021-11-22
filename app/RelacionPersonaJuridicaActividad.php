<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelacionPersonaJuridicaActividad extends Model
{
    public $timestamps = false;

    protected $table = 'relacion_personajuridica_actividad';

    protected $primaryKey = 'codRelacion';

    protected $fillable = ['codActividadPrincipal','codPersonaNatural'];
    
    public function getActividad(){
        return ActividadPrincipal::findOrFail($this->codActividadPrincipal);

    }

    public function getPersona(){
        return PersonaJuridicaPoblacion::findOrFail($this->codPersonaJuridica);
    }

}
