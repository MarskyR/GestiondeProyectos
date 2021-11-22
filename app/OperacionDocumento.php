<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperacionDocumento extends Model
{
    protected $table = "operacion_documento";
    protected $primaryKey ="codOperacionDocumento";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    /* Esto es solo para el listar operaciones, para que se diferencien cuales son del mismo 
    Elegiré colores claros (cercanos al 255,255,255 )
    el color se basará en el codTipoDocumento y el codDocumento
    */
    public function getColorFondo(){
        $codTipoDocumento = $this->codTipoDocumento;
        $codDocumento = $this->codDocumento;
        

        $valorR = pow($codDocumento +1,$codTipoDocumento) + 3*$codTipoDocumento - $codDocumento;
        $valorG = pow($codDocumento +3,$codTipoDocumento) + 4*$codDocumento + 2*$codTipoDocumento;
        $valorB = pow($codDocumento + 5,$codTipoDocumento +1 ) + 5*$codTipoDocumento - 2*$codDocumento;
        
        
        /* Normalizamos los valores para que sean menores a 128 */
        $R = 125 + fmod($valorR,128);
        $G = 125 + fmod($valorG,128);
        $B = 125 + fmod($valorB,128);
        return "rgb($R,$G,$B)";
    }

    public function getTipoDocumento(){
        return TipoDocumento::findOrFail($this->codTipoDocumento);
    }

    public function getTipoOperacion(){
        return TipoOperacion::findOrFail($this->codTipoOperacion);

    }

    public function getPuesto(){
        return Puesto::findOrFail($this->codPuesto);
    }
    public function getFechaHora(){
        return Fecha::formatoFechaHoraParaVistas($this->fechaHora);
    }
    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleado);
    }
    
    public function getDocumento(){

        $id = $this->codDocumento;
        switch($this->codTipoDocumento){
            case 1:
                $doc = SolicitudFondos::findOrFail($id);
                break;
            case 2:
                $doc = RendicionGastos::findOrFail($id);
                break;
            case 3:
                $doc = ReposicionGastos::findOrFail($id);
                break;
            case 4:
                $doc = RequerimientoBS::findOrFail($id);
                break;
        }

        return $doc;
    }

}
