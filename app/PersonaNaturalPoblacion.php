<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PersonaNaturalPoblacion extends Model
{
    
    protected $table = "persona_natural_poblacion";
    protected $primaryKey ="codPersonaNatural";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['dni','nombres','apellidos','fechaNacimiento','edadMomentanea','codPoblacionBeneficiaria','sexo','direccion','nroTelefono','actividadPrincipal'];

    public function getLugarEjecucion(){
        return LugarEjecucion::findOrFail($this->codLugarEjecucion);
    }
    public function getNombreCompleto(){
        return $this->nombres. " ".$this->apellidos;

    }
    public function getNombreCompletoYDNI(){
        return $this->getNombreCompleto()." [".$this->dni."]";
    }

    public function getListaPoblaciones(){
        $lista = RelacionPersonaNaturalPoblacion::where('codPersonaNatural','=',$this->codPersonaNatural)->get();
        $vector = [];
        foreach ($lista as $item) {
            array_push($vector,$item->getPoblacion()->descripcion." [".$item->getPoblacion()->codProyecto."] ");
        }
        return implode(',',$vector);

    }

    public function getFechaNacimiento(){
        return Fecha::formatoParaVistas($this->fechaNacimiento);
    }


    /* retorna a una persona que tenga ese dni, si no retorna "" */
    public static function buscarPorDNI($dni){
        $lista = PersonaNaturalPoblacion::where('dni','=',$dni)->get();
        if(count($lista) > 0)
            return $lista[0];
        

        return "";
    }

    public function getResumenActividades(){
        $lista = $this->getListaActividades();
        $cadena = "";
        foreach($lista as $item)
            $cadena = $cadena.",".$item->descripcion;
        $cadena = trim($cadena,",");
        return  $cadena ;

    }

    public function getListaActividades(){
        //obtenemos el vector con los codigos de las actividades de esta persona natural
        $listaRelaciones = RelacionPersonaNaturalActividad::where('codPersonaNatural','=',$this->codPersonaNatural)->get();
        $vector = [];
        foreach ($listaRelaciones as $relacion)
            array_push($vector,$relacion->codActividadPrincipal);
         
        return ActividadPrincipal::whereIn('codActividadPrincipal',$vector)->get();

    }

    public function getVectorActividades(){
        $lista = $this->getListaActividades();
        $vector = [];
        foreach($lista as $item)
           array_push($vector,$item->codActividadPrincipal); 
        return json_encode($vector);
    }



}
