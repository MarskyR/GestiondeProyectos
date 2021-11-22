<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PoblacionBeneficiaria extends Model
{
    protected $table = "poblacion_beneficiaria";
    protected $primaryKey ="codPoblacionBeneficiaria";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['descripcion','codProyecto'];

    public function getProyecto(){
        return Proyecto::findOrFail($this->codProyecto);

    }
    public function getCantidadTotalDePersonas(){

        return count($this->getPersonasNaturales()) 
            + count($this->getPersonasJuridicas());
    }

    public function getPersonasNaturales(){
        //return PersonaNaturalPoblacion::where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)->get();

        $listaPersonasNaturales = RelacionPersonaNaturalPoblacion::where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)->get();
        $arr=[];
        foreach ($listaPersonasNaturales as $item) {
            $arr[]=$item->codPersonaNatural;
        }
        $listaPersonasNaturales = PersonaNaturalPoblacion::whereIn('codPersonaNatural',$arr)->get();

        return $listaPersonasNaturales;
    }
    public function getPersonasJuridicas(){
        //return PersonaJuridicaPoblacion::where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)->get();

        $listaPersonasJuridicas = RelacionPersonaJuridicaPoblacion::where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)->get();
        $arr=[];
        foreach ($listaPersonasJuridicas as $item) {
            $arr[]=$item->codPersonaJuridica;
        }
        $listaPersonasJuridicas = PersonaJuridicaPoblacion::whereIn('codPersonaJuridica',$arr)->get();

        return $listaPersonasJuridicas;
    }






    public function getPersonasNaturalesAjenas(){
        //primer obtenemos la lista de personas total de la bd
        $personas = PersonaNaturalPoblacion::All();
        $listaAjenas = new Collection();

        foreach ($personas as $persona) {
            
            $res = RelacionPersonaNaturalPoblacion::where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)
                ->where('codPersonaNatural','=',$persona->codPersonaNatural)->get();

            if( count($res) == 0 ){//si no está, añadimos esta persona a la lista de ajenas
                $listaAjenas->push($persona);
            }
        }
        return $listaAjenas;
    }

    
    public function getPersonasJuridicasAjenas(){
        //primer obtenemos la lista de personas total de la bd
        $personas = PersonaJuridicaPoblacion::All();
        $listaAjenas = new Collection();

        foreach ($personas as $persona) {
            
            $res = RelacionPersonaJuridicaPoblacion::where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)
                ->where('codPersonaJuridica','=',$persona->codPersonaJuridica)->get();

            if( count($res) == 0 ){//si no está, añadimos esta persona a la lista de ajenas
                $listaAjenas->push($persona);
            }
        }
        return $listaAjenas;
    }

    /* Le pasamos un codigo de persona natural, retorna si es que la persona ya está en la poblacion beneficiaria */
    public function buscarPorCodPersonaNatural($codPersonaNatural){
        $lista = RelacionPersonaNaturalPoblacion::where('codPersonaNatural','=',$codPersonaNatural)
            ->where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)
            ->get();



        return count($lista) > 0;

    }
    public function buscarPorCodPersonaJuridica($codPersonaJuridica){
        $lista = RelacionPersonaJuridicaPoblacion::where('codPersonaJuridica','=',$codPersonaJuridica)
            ->where('codPoblacionBeneficiaria','=',$this->codPoblacionBeneficiaria)
            ->get();

        return count($lista) > 0;

    }


}
