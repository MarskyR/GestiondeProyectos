<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelacionPersonaNaturalPoblacion extends Model
{
    // ESTA TABLA ES PRODUCTO DE LA RELACION MUCHSO A MUCHOS ENTRE POB BENEFICIARIA Y personanatural poblacion

    public $timestamps = false;

    protected $table = 'relacion_personanat_poblacion';

    protected $primaryKey = 'codRelacionNat';

    protected $fillable = [
        'codPersonaNatural','codPoblacionBeneficiaria'
    ];/* Cambiar a codPersonaNatural */

    public function getPoblacion(){
        return PoblacionBeneficiaria::findOrFail($this->codPoblacionBeneficiaria);

    }

    public function getPersona(){
        return PersonaNaturalPoblacion::findOrFail($this->codPersonaNatural);
    }


}
