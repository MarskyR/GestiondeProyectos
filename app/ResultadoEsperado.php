<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultadoEsperado extends Model
{
    protected $table = "resultado_esperado";
    protected $primaryKey ="codResultadoEsperado";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['descripcion','codProyecto'];


    public function getPrimeraActividad(){
        return $this->getListaActividades()[0];

    }

    public function getOtrasActividades(){
        $lista = $this->getListaActividades();
        $lista->shift();
        return $lista;
    }

    public function getOtrosIndicadores(){
        $lista = $this->getListaIndicadoresResultados();
        $lista->shift();
        return $lista;
    }

    public function getDescripcionAbreviada(){
        
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = 60;
        $cadena = $this->descripcion;
        if(strlen($cadena) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($cadena, 0, $limiteCaracteres) . '...';
        }

        // Si no, entonces devuelve la cadena normal
        return $cadena;
    
    }

    public function getCantidadActividades(){
        return count($this->getListaActividades());
    

    }

    public function getListaActividades(){
        return ActividadResultado::where('codResultadoEsperado','=',$this->codResultadoEsperado)->get();
        
    }

    

    public function getPrimerIndicador(){
        if(count($this->getListaIndicadoresResultados())==0)
            return "";
        return $this->getListaIndicadoresResultados()[0];

    }






    public function getListaIndicadoresResultados(){
        return IndicadorResultado::where('codResultadoEsperado','=',$this->codResultadoEsperado)->get();

    }

    public function getCantidadIndicadoresResultados(){
        return count($this->getListaIndicadoresResultados());

    }

    public function getProyecto(){
        return Proyecto::findOrFail($this->codProyecto);


    }

}
