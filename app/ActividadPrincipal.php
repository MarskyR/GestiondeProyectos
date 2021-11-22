<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActividadPrincipal extends Model
{
    protected $table = "actividad_principal";
    protected $primaryKey ="codActividadPrincipal";

    public $timestamps = false;

    protected $fillable = ['descripcionActividad'];
}
