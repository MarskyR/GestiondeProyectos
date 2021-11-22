<?php

namespace App;

use Carbon\Carbon;
use DateTime;

class Fecha
{
    
    var $fechaEnSQL; /* Valor  de la fecha en formato SQL  YYYY-MM-DD*/
    var $fechaEnLegible;/* Valor de la fecha en formato legible perú DD/MM/YYYY */
    


    public function nuevaFechaSQL($legible){
        $this->fechaEnLegible = $legible ;
        
        $sql = substr($legible,6,4).'-'.substr($legible,3,2).'-'.substr($legible,0,2);
        $this->fechaEnSQL = $sql;
    }


    /* funcion tipo libreria
    ingresa una fecha en formato peruano DD/MM/YYYY
    sale una fecha en formato sql YYYY-MM-DD

    */
    public static function formatoParaSQL($fecha){

        // date('d/m/Y', strtotime($this->fechaInicio));
        /*              año                 mes                 dia*/
        return substr($fecha,6,4).'-'.substr($fecha,3,2).'-'.substr($fecha,0,2);
    }
    

    /* funcion tipo libreria 
        ingresa una fecha en formato sql YYYY-MM-DD
        sale una fecha en formato peruano DD/MM/YYYY
    */
    public static function formatoParaVistas($fecha){
        return date('d/m/Y', strtotime($fecha));

    }

    public static function formatoFechaHoraParaVistas($fecha){
        return date('d/m/Y H:i:s', strtotime($fecha));


    }

    public static function getFechaHoraActual(){
        $fecha = Carbon::now();
        return Fecha::formatoFechaHoraParaVistas($fecha);
    }


    public static function getFechaAntigua(){
        return "01/01/2000";

    }


    /* 
        funcion para IndicadorActividadController registrarMetaProgramada
        compara 2 fechas que llegan en vectores 
        $fecha1 = 
        [
            'año'=>2000,
            'mes'=>5
        ]
    */
    public static function compararFechas($fecha1,$fecha2){


    }

    //llega string y objeto proyecto
    // 2020-01-20
    public static function dentroDeFechasProyecto($fechaMetaString,$proyecto){
        $fechaMinima = strtotime(substr($proyecto->fechaInicio,0,8)."01");
        $fechaMaxima = strtotime(substr($proyecto->fechaFinalizacion,0,8)."31");



        $fechaMeta = strtotime($fechaMetaString);
 
    
        Debug::mensajeSimple($proyecto->fechaInicio." () ".$proyecto->fechaFinalizacion);
        

        return ($fechaMeta <= $fechaMaxima && $fechaMeta >= $fechaMinima);

    }

}
