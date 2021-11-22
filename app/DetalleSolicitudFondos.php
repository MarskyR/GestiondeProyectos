<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleSolicitudFondos extends Model
{
    protected $table = "detalle_solicitud_fondos";
    protected $primaryKey ="codDetalleSolicitud";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codSolicitud','nroItem','concepto',
    'importe','codigoPresupuestal'];

}
