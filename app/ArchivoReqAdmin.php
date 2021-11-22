<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivoReqAdmin extends Model
{
    protected $table = "archivo_req_admin";
    protected $primaryKey ="codArchivoReqAdmin";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codArchivoReqAdmin','nombreDeGuardado','nombreAparente','codRequerimiento'];

    
}
