<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanEstrategicoInstitucional extends Model
{
    public $timestamps = false;

    protected $table = 'plan_estrategico_institucional';

    protected $primaryKey = 'codPEI';

    protected $fillable = [
        'añoInicio','añoFin'
    ];

    public function getPeriodo(){
        return $this->añoInicio.'-'.$this->añoFin;
    }

    public function eliminarObjetivos(){

        ObjetivoEstrategico::where('codPEI','=',$this->codPEI)->delete();


    }

    public function getListaObj(){
        return ObjetivoEstrategico::where('codPEI','=',$this->codPEI)->get();

    }
}
