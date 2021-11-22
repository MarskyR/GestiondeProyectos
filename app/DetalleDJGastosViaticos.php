<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleDJGastosViaticos extends Model
{
    protected $table = "detalle_dj_gastosviaticos";
    protected $primaryKey ="codDetalleViaticos";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['fecha','lugar','montoDesayuno','montoAlmuerzo','montoCena','totalDia','codDJGastosViaticos'];

    public function getDJ(){
        return DJGastosViaticos::findOrFail($this->codDJGastosViaticos);
    }

    public function getMontoDesayunoFormateado(){
        return $this->getDJ()->getMoneda()->simbolo." ".number_format($this->montoDesayuno,2);

    }

    public function getMontoAlmuerzoFormateado(){
        return $this->getDJ()->getMoneda()->simbolo." ".number_format($this->montoAlmuerzo,2);

    }

    public function getMontoCenaFormateado(){
        return $this->getDJ()->getMoneda()->simbolo." ".number_format($this->montoCena,2);

    }

    public function getTotalDiaFormateado(){
        return $this->getDJ()->getMoneda()->simbolo." ".number_format($this->totalDia,2);

    }

    public function getFecha(){
        return date("d/m/Y",strtotime($this->fecha));
        //return str_replace('-','/',$this->fecha);
    }
}
