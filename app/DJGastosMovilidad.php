<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DJGastosMovilidad extends Model
{
    protected $table = "dj_gastosmovilidad";
    protected $primaryKey ="codDJGastosMovilidad";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    const RaizCodigoCedepas = "MOV";

    // le indicamos los campos de la tabla 
    protected $fillable = ['fechaHoraCreacion','domicilio','importeTotal','codMoneda','codEmpleado'];

    public function getTotalFormateado(){
        return $this->getMoneda()->simbolo." ".number_format($this->importeTotal,2);

    }

    public function getPDF(){
        $listaItems = DetalleDJGastosMovilidad::where('codDJGastosMovilidad','=',$this->codDJGastosMovilidad)->get();
        $pdf = \PDF::loadview('DJ.Movilidad.PdfDJMov',
            array('DJ'=>$this,'listaItems'=>$listaItems)
                            )->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleado);
    }

    public function getMoneda(){
        return Moneda::findOrFail($this->codMoneda);
    }

    public function getMontoConMoneda(){
        return $this->getMoneda()->simbolo.' '.$this->importeTotal;

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

        return DetalleDJGastosMovilidad::where('codDJGastosMovilidad','=',$this->codDJGastosMovilidad)->get();
    }

    public static function calcularCodigoCedepasLibre(){
        $objNumeracion = Numeracion::getNumeracionDJ_MOV();
        return  DJGastosMovilidad::RaizCodigoCedepas.
            substr($objNumeracion->año,2,2).
            '-'.
            DJGastosMovilidad::rellernarCerosIzq($objNumeracion->numeroLibreActual,4);
        
    }


    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
        
    }


    //para pdf
    public function getFechaHoraCreacion(){
            return date("d/m/Y h:i:s",strtotime($this->fechaHoraCreacion));
    }






}
