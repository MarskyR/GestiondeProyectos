<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleRequerimientoBS extends Model
{
    protected $table = "detalle_requerimiento_bs";
    protected $primaryKey ="codDetalleRequerimiento";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codDetalleRequerimiento','codRequerimiento','cantidad',
        'codUnidadMedida','descripcion','codigoPresupuestal'];

    
    public function getNombreTipoUnidad(){
        $unidad = UnidadMedida::findOrFail($this->codUnidadMedida);
        return $unidad->nombre;
    }

    /* Convierte el objeto en un vector con elementos leibles directamente por la API */
    public function getVectorParaAPI(){
        $itemActual = $this;
        $itemActual['nombreUnidad'] = $this->getNombreTipoUnidad();
        return $itemActual;
    }


}
