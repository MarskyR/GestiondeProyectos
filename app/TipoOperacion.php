<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoOperacion extends Model
{
    protected $table = "tipo_operacion";
    protected $primaryKey ="codTipoOperacion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    public function getTipoDocumento(){
        return TipoDocumento::findOrFail($this->codTipoDocumento);
    }

    public static function getCodTipoOperacion($abreviacion,$nombreTipoOperacion){
        $tipoDoc = TipoDocumento::where('abreviacion','=',$abreviacion)->get()[0];
        $tipoOperacion = TipoOperacion::where('codTipoDocumento','=',$tipoDoc->codTipoDocumento)
            ->where('nombre','=',$nombreTipoOperacion)
            ->get()
            [0];
        
        return $tipoOperacion->codTipoOperacion;

    }

    
    
}
