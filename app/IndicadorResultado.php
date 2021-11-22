<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndicadorResultado extends Model
{
    protected $table = "indicador_resultado";
    protected $primaryKey ="codIndicadorResultado";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    const raizArchivo = "MedioVerif-";
    // le indicamos los campos de la tabla 
    protected $fillable = ['descripcion','codResultadoEsperado'];

    public function getResultadoEsperado(){

        return ResultadoEsperado::findOrFail($this->codResultadoEsperado);
    }

    public function getDescripcionAbreviada(){

        return Debug::abreviar($this->descripcion,60);
    }

    /* CUANDO ES ARCHIVO */
    public function getNombreGuardadoNuevoArchivo($j){
        //$j = $this->getCantidadMediosVerificacion() + 1; //para que inicie en 1
        
        return  
            IndicadorResultado::raizArchivo.
            IndicadorResultado::rellernarCerosIzq($this->codIndicadorResultado,6).
            '-'.
            IndicadorResultado::rellernarCerosIzq($j,2).
            '.marac';

        
    }


    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
        
    }









    public function getCantidadMediosVerificacion(){
      
        return count($this->getMediosVerificacion());
       
    }



    public function getMediosVerificacion(){
        return MedioVerificacionResultado::where('codIndicadorResultado','=',$this->codIndicadorResultado)->get();

    }




}
