<?php

namespace App;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MetaEjecutada extends Model
{
    public $timestamps = false;

    protected $table = 'meta_ejecutada';

    protected $primaryKey = 'codMetaEjecutada';
    const raizArchivo ="MetaEjec-";
    protected $fillable = ['cantidadProgramada','cantidadEjecutada','codEmpleado',
        'fechaRegistroEjecucion','fechaRegistroProgramacion','mesAñoObjetivo','codIndicadorActividad','desviacion','ejecucion','esReprogramada'];



    public function borrarArchivos(){
        foreach ($this->getListaArchivos() as $itemArchivo) {
            $nombre = $itemArchivo->nombreGuardado;
            Storage::disk('mediosVerificacionMetas')->delete($nombre);
            Debug::mensajeSimple('Se acaba de borrar el archivo:'.$nombre);
        } 
        return MedioVerificacionMeta::where('codMetaEjecutada','=',$this->codMetaEjecutada)->delete();
    }

    /* Booleana,retorna true si la meta se puede editar
    se puede editar si ha sido creada hace N días. 
    N puede ser configurada desde App\Configuracion::
    */
    public function sePuedeEditar(){
        $fechaActual = new DateTime();
        $fechaRegistroEjecucion = new DateTime($this->fechaRegistroEjecucion);

        $diferenciaDias = $fechaRegistroEjecucion->diff($fechaActual);

        

        return $diferenciaDias->days<=Configuracion::maximoDiasEdicionMetas;
    }

    public function getDiaMaximoActualizacion(){
        

        //sumo 1 día
        return date("d/m/Y",strtotime($this->fechaRegistroEjecucion."+ ".Configuracion::maximoDiasEdicionMetas." days")); 
        

    }


    //retorna si la instancia de este obj esta vacia o no (es para la vista de registroEjecucionMetas, pq ahi se crean instancias vacias para cada celda)
    public function estaVacia(){
        return $this->codMetaEjecutada=="";
    }

    function getCantidadSiEjecutada(){
        if($this->ejecutada==0)
            return "No Ejec";

        return $this->cantidadEjecutada;

    }

    public function getAñoObjetivo(){
        $año = new DateTime($this->mesAñoObjetivo);
        return $año->format('Y');/* */
    }

    public function getMesYAñoEscrito(){
        return $this->getNombreMesObjetivo()."-".$this->getAñoObjetivo();
    }
    public function getNombreMesObjetivo(){
        $mes = new DateTime($this->mesAñoObjetivo);
        $mes = $mes->format('m');/* en mes estaria el numero del 1 al 12 */

        return Mes::findOrFail($mes)->nombre;

    }

    public function getFechaRegistroProgramacion(){
        $date = new DateTime($this->fechaRegistroProgramacion);
        return $date->format('d/m/Y H:i:s');
    }
    public function getFechaRegistroEjecucion(){
        $date = new DateTime($this->fechaRegistroEjecucion);
        return $date->format('d/m/Y H:i:s');
    }
    

    public function getNombreGuardadoNuevoArchivo($j){
        return  MetaEjecutada::raizArchivo.
        MetaEjecutada::rellernarCerosIzq($this->codMetaEjecutada,6).
        '-'.
        MetaEjecutada::rellernarCerosIzq($j,2).
        '.marac';
    }

    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
        
    }

    public function getVistaArchivos(){
        $meta = $this;
        return view('Proyectos.Gerente.VerArchivosMeta',compact('meta'));
       
    }


    public function getListaArchivos(){
        return MedioVerificacionMeta::where('codMetaEjecutada','=',$this->codMetaEjecutada)->get();
    }
    public function getCantidadArchivos(){
        return count($this->getListaArchivos());
    }


    public function getIndicadorActividad(){
        return IndicadorActividad::findOrFail($this->codIndicadorActividad);

    }
    public function esReprogramada(){
        return ($this->esReprogramada=='1');
    }
    
    public function getColor(){
        $color='';
        if($this->tasaEjecucion>=0 && $this->tasaEjecucion<=50){
            $color='red';
        }else if($this->tasaEjecucion<=80){
            $color='rgb(168, 168, 0)';
        }else $color='green';

        if(is_null($this->tasaEjecucion) || is_null($this->desviacion)){
            $color='black';
        }

        return $color;
    }
    
    public function getEjecucion(){
        
        /* Si el registro no existe */
        if(is_null($this->cantidadEjecutada) ){
            //Debug::mensajeSimple('se ejecuta primer if:'. $this);
            return "";
        }

        /* Si se programó pero la cantidad ejecutada es 0 */
        if($this->cantidadEjecutada=="0" && $this->cantidadProgramada!="0"  ){
            return "0%";
        }

        /* Si se ha programado pero no ejecutado*/
        if($this->cantidadEjecutada=="-1" && $this->cantidadProgramada!="-0"  ){
            //Debug::mensajeSimple('se ejecuta segundo if:'. $this);
            return "";
        }
        


        $redoneado = number_format($this->tasaEjecucion,0);
        return $redoneado."%";
    }


    public function puedeRegistrarEjecutada(){
        if($this->ejecutada=="0")
            return true;
            
        return false;
    }


    public function getProyecto(){
        $indicador=IndicadorActividad::findOrFail($this->codIndicadorActividad);
        $actividad=ActividadResultado::findOrFail($indicador->codActividad);
        $resultado=ResultadoEsperado::findOrFail($actividad->codResultadoEsperado);

        return Proyecto::findOrFail($resultado->codProyecto);
    }


    public function pintarSiVacia(){
        if($this->cantidadProgramada=="" && $this->cantidadEjecutada=="")
            return "SinRegistro";

        return " ";
    }   
    
}
