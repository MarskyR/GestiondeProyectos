<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActividadResultado extends Model
{
    protected $table = "actividad_res";
    protected $primaryKey ="codActividad";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre','descripcion','meta','unidadMedida','codResultadoEsperado'];


    

    public function getResultadoEsperado(){
        return ResultadoEsperado::findOrFail($this->codResultadoEsperado);
    }
    
    



    public function getListaIndicadores(){

        return IndicadorActividad::where('codActividad','=',$this->codActividad)->get();

    }

    public function getCantidadIndicadores(){
        return count($this->getListaIndicadores());
    }

    
    public function getDescripcionAbreviada(){

        

       
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = 70;
        $cadena = $this->descripcion;
        if(strlen($cadena) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($cadena, 0, $limiteCaracteres) . '...';
        }

        // Si no, entonces devuelve la cadena normal
        return $cadena;
    
    
    }
}
