<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelacionPersonaJuridicaPoblacion extends Model
{
// ESTA TABLA ES PRODUCTO DE LA RELACION MUCHSO A MUCHOS ENTRE POB BENEFICIARIA Y personanatural poblacion
    
    public $timestamps = false;

    protected $table = 'relacion_personajur_poblacion';

    protected $primaryKey = 'codRelacionJur';

    protected $fillable = [
        'codPersonaJuridica','codPoblacionBeneficiaria'
    ];/* Cambiar a codPersonaNatural */

   
    public function getPoblacion(){
        return PoblacionBeneficiaria::findOrFail($this->codPoblacionBeneficiaria);

    }

    public function getPersona(){
        return PersonaJuridicaPoblacion::findOrFail($this->codPersonaJuridica);
    }

    
}
