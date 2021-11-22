<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DJGastosVarios extends Model
{
    protected $table = "dj_gastosvarios";
    protected $primaryKey ="codDJGastosVarios";

    const RaizCodigoCedepas = "VAR";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['fechaHoraCreacion','domicilio','importeTotal','codMoneda','codEmpleado'];

    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleado);
    }

    public function getMoneda(){
        return Moneda::findOrFail($this->codMoneda);
    }

    public function getMontoConMoneda(){
        return $this->getMoneda()->simbolo.' '.number_format($this->importeTotal,2);

    }

    public function getDetallesAbreviados(){//falta editar

        $cadena = "";
        $cadenaTemp = "";
        foreach ($this->getDetalles() as $det ) {
            $cadenaTemp = $cadenaTemp.", ".$det->concepto;
            if(strlen($cadenaTemp)>140){
                break;
            }
            $cadena = $cadena.", ".$det->concepto;
        }

        $cadena = trim($cadena,",");
        return $cadena;
    }
    public function getDetalles(){
        return DetalleDJGastosVarios::where('codDJGastosVarios','=',$this->codDJGastosVarios)->get();
    }

    public static function calcularCodigoCedepasLibre(){
        $objNumeracion = Numeracion::getNumeracionDJ_VAR();
        return  DJGastosVarios::RaizCodigoCedepas.
            substr($objNumeracion->año,2,2).
            '-'.
            DJGastosVarios::rellernarCerosIzq($objNumeracion->numeroLibreActual,4);
        
    }
    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
        
    }

    public function getPDF(){
        $listaItems = DetalleDJGastosVarios::where('codDJGastosVarios','=',$this->codDJGastosVarios)->get();
        $pdf = \PDF::loadview('DJ.Varios.PdfDJVar',
            array('DJ'=>$this,'listaItems'=>$listaItems)
                            )->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    public function getFechaHoraCreacion(){
        //return $this->fechaHoraCreacion;
        return date("d/m/Y h:i:s",strtotime($this->fechaHoraCreacion));
    }
    
}
