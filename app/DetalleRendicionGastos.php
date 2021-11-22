<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleRendicionGastos extends Model
{
    protected $table = "detalle_rendicion_gastos";
    protected $primaryKey ="codDetalleRendicion";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codRendicionGastos','fecha','nroComprobante',
    'concepto','importe',
    'codigoPresupuestal','codTipoCDP','nroEnRendicion','contabilizado'];

    public function getFecha(){

        return Fecha::formatoParaVistas($this->fecha);
    }

    public function getRendicion(){
        return RendicionGastos::findOrFail($this->codRendicionGastos);

    }

    public function setTipoCDPPorNombre($nombreCDP){
        $listacdp = CDP::where('nombreCDP','=',$nombreCDP)->get();
        $cdp = $listacdp[0];
        $this->codTipoCDP = $cdp->codTipoCDP;
        
    }
    public function getNombreTipoCDP(){
        $cdp = CDP::findOrFail($this->codTipoCDP);
        return $cdp->nombreCDP;
    }

    public function getCDP(){
        return CDP::findOrFail($this->codTipoCDP);

    }


    public function getContabilizado(){
        if($this->contabilizado)
            return "SÍ";
        return "NO";
    }

    public function getColor(){

        $a = $this->getRendicion()->codRendicionGastos;
        $b = $this->getRendicion()->totalImporteRecibido;
        $c = $this->getRendicion()->totalImporteRendido;
        

        $R = 5222*$a + 7001*$b*$a + 1275*$c;
        $G = $a + $b*9899 + 12878*$c*$b;
        $B = 1500*$a + $b + 1625*$c*$a ;


        $R = ($R+170)%255;
        $G = ($G+170)%255;
        $B = ($B+170)%255;
        
        $color = "rgb(".$R.",".$G.",".$B.")";

        return $color;
    }


    //retorna background-color:red si es que la rend ya está contabilizdaa y ese gasto no lo está
    public function getEstadoDeAlerta(){
        if($this->getRendicion()->verificarEstado('Contabilizada') && $this->contabilizado==0)
            return "background-color:red";

        return "";
    }



     /* Convierte el objeto en un vector con elementos leibles directamente por la API */
     public function getVectorParaAPI(){
        $itemActual = $this;
        $itemActual['fechaFormateada'] = $this->getFecha();
        $itemActual['nombreCDP'] = $this->getNombreTipoCDP();

        return $itemActual;
    }
    
}
