<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndicadorActividad extends Model
{
    public $timestamps = false;

    protected $table = 'indicador_actividad';

    protected $primaryKey = 'codIndicadorActividad';

    protected $fillable = [
        'descripcion','meta','unidadMedida','codActividad','saldoPendiente'
    ];


    public function getVistaGrafico(){
        
        $listaMetas = MetaEjecutada::where('codIndicadorActividad','=',$this->codIndicadorActividad)->get();

        $arrFaltante=[];
        $arr=[];
        $indices=[];
        $colores=[];
        $constantes=[];
        foreach ($listaMetas as $itemmeta) {
            $arr[]=array('y'=>date('Y-m',strtotime($itemmeta->mesAñoObjetivo)), 'a'=>$itemmeta->cantidadProgramada, 'b'=>$itemmeta->cantidadEjecutada);
                $indices[]=$itemmeta->codMetaEjecutada;
                $colores[]=$itemmeta->getColor();
                $constantes[]=array($itemmeta->desviacion,$itemmeta->tasaEjecucion);
            
            if($itemmeta->cantidadEjecutada==0){
                $arrFaltante[]=array($itemmeta->codMetaEjecutada,date('Y-m',strtotime($itemmeta->mesAñoObjetivo)),$itemmeta->cantidadProgramada);
            }
        }
       
        return view('Proyectos.ReporteGraficoIndicador',compact('arr','arrFaltante','indices','colores','constantes'));

    }

    public function getMetas(){
        return MetaEjecutada::where('codIndicadorActividad','=',$this->codIndicadorActividad)->get();

    }


    //retorna la suma de todas las metas programadas de este indicador
    public function getMetaTotal(){
        $lista = $this->getMetas();
        $suma = 0;
        foreach ($lista as $meta) {
            if($meta->esReprogramada==0) //si no es reprogramada (si es normal) se suma la cantidad
                $suma = $suma + $meta->cantidadProgramada;
        }

        return $suma;

    }

    public function getCantidadEjecutada(){
        $lista = $this->getMetas();
        $suma = 0;
        foreach ($lista as $meta) {
            $suma = $suma + $meta->cantidadEjecutada;
        }

        return $suma;
        
    }

    /* Le ingresa el año en formato YYYY 2021 y el mes en MM 01 */
    public function tieneMetaEn($año,$mes){
        $fecha = $año."-".$mes."-01";
        $metas = MetaEjecutada::where('mesAñoObjetivo','=',$fecha)
        ->where('codIndicadorActividad','=',$this->codIndicadorActividad)
        ->get();
        return count($metas)!=0;
    }

    public function getMetaYUnid(){
        
        return "[".$this->meta."] ".$this->unidadMedida ;

    }

    public function getActividad(){
        return ActividadResultado::findOrFail($this->codActividad);

    }

    public function getResultadoEsperado(){
        return $this->getActividad()->getResultadoEsperado();

    }

    public function getProyecto(){
        return $this->getResultadoEsperado()->getProyecto();
    }


    /* SALDO PENDIENTE DE UN INDICADOR =
    Sumatoria de las cantidades programadas - sumatoria cantidades ejecutadas
        */
    public function calcularSaldoPendiente(){
        $lista = $this->getMetas();
        $saldoTotal = 0;
        foreach ($lista as $meta) {
            $saldoTotal = $saldoTotal - $meta->cantidadEjecutada;
            if($meta->esReprogramada==0) //si no es reprogramada (si es normal) se suma la cantidad programada
                $saldoTotal += $meta->cantidadProgramada;

        }
        return $saldoTotal;

    }

    public function calcularPorcentajeEjecucion(){
        /*      Lo total ejecutado entre lo total programado */
        if($this->meta==0){
            return "0%";
        }


        $tasa = 100*($this->meta-$this->saldoPendiente)/$this->meta;
        return number_format($tasa,0)."%";
    }

    public function getColorPorcentajeEjecucion(){
        $tasa = $this->calcularPorcentajeEjecucion();
        $color='';
        if($tasa>=0 && $tasa<=50){
            $color='red';
        }else if($tasa<=80){
            $color='rgb(168, 168, 0)';
        }else $color='green';

        if(is_null($tasa)){
            $color='white';
        }

        return $color;
    }

    /* ESTA FUNCION FELIX, RETORNAN UN OBJETO MetaEjecutada
    le entra un mes con esta estructura:
        $mes1 = ['codigoMes'=>'1', 'año'=>2021,'nombreMes'=> 'Enero'];


    le sale un objeto metaEjecutada con la meta que le corresponde a ese mes
    */
    public function getMeta($mes){
        
        $fecha = $mes['año']."-".$mes['codigoMes']."-01";
        $metas =  MetaEjecutada::where('mesAñoObjetivo','=',$fecha)
            ->where('codIndicadorActividad','=',$this->codIndicadorActividad)
            ->get();
        
        if(count($metas)==0){/* Si este indicador no tienen ninguna meta en ese mes, lo retorna como cero */
            return new MetaEjecutada();
        }
        
        return $metas[0];
        
    }
    

}
