<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = "tipo_documento";
    protected $primaryKey ="codTipoDocumento";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    public function getColor(){
        switch ($this->abreviacion) {
            case 'SOL':
                $color = "rgb(148, 186, 255)";
                break;
            case 'REN':
                $color = "rgb(255, 138, 138)";
                break;
            case 'REP':
                $color = "cyan";
                break;
            case 'REQ':
                $color = "rgb(155, 255, 146)";
                break;
                    
        }
        return $color;
    }

}
