<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DJGastosViaticos extends Model
{
    protected $table = "dj_gastosviaticos";
    protected $primaryKey ="codDJGastosViaticos";

    public $timestamps = false;  //para que no trabaje con los campos fecha     
    
    // le indicamos los campos de la tabla 
    protected $fillable = ['fechaHoraCreacion','domicilio','importeTotal','codMoneda','codEmpleado'];

    const RaizCodigoCedepas = "VIA";
    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleado);
    }

    public function getMoneda(){
        return Moneda::findOrFail($this->codMoneda);
    }

    public static function calcularCodigoCedepasLibre(){
        $objNumeracion = Numeracion::getNumeracionDJ_VIA();
        return  DJGastosViaticos::RaizCodigoCedepas.
            substr($objNumeracion->año,2,2).
            '-'.
            DJGastosViaticos::rellernarCerosIzq($objNumeracion->numeroLibreActual,4);
        
    }
    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
        
    }

    public function getMontoConMoneda(){
        return $this->getMoneda()->simbolo.' '.number_format($this->importeTotal,2);

    }

    public function getDetallesAbreviados(){

        $cadena = "";
        foreach ($this->getDetalles() as $det ) {
            $cadena = $cadena.", ".$det->lugar;
        }

        $cadena = trim($cadena,",");
        return $cadena;
    }

    public function getDetalles(){
        return DetalleDJGastosViaticos::where('codDJGastosViaticos','=',$this->codDJGastosViaticos)->get();
    }

    public function getPDF(){
        $listaItems = DetalleDJGastosViaticos::where('codDJGastosViaticos','=',$this->codDJGastosViaticos)->get();
        $pdf = \PDF::loadview('DJ.Viaticos.PdfDJVia',
            array('DJ'=>$this,'listaItems'=>$listaItems)
                            )->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    //para el pdf
    public function getFechaHoraCreacion(){
        return date("d/m/Y h:i:s",strtotime($this->fechaHoraCreacion));
    }


    
}
