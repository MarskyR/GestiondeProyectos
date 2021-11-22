<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrdenCompra extends Model
{
    public $timestamps = false;
    protected $table = 'orden_compra';

    protected $primaryKey = 'codOrdenCompra';

    const RaizCodigoCedepas = "OC";

    const raizArchivo = "OC-";
    protected $fillable = [
        'señores','	ruc','direccion','atencion','referencia','total','partidaPresupuestal','observacion','codProyecto','codEmpleadoCreador'
        ,'fechaHoraCreacion','codigoCedepas','codMoneda'
    ];

    /* Booleana,retorna true si la meta se puede editar
    se puede editar si ha sido creada hace N días. 
    N puede ser configurada desde App\Configuracion::
    */
    public function sePuedeEditar(){
        $fechaActual = new DateTime();
        $fechaHoraCreacion = new DateTime($this->fechaHoraCreacion);
        $diferenciaDias = $fechaHoraCreacion->diff($fechaActual);

        Debug::mensajeSimple($diferenciaDias->days);
        return $diferenciaDias->days<Configuracion::maximoDiasEdicionOrdenCompra;
    }

    public function getColorSePuedeEditar(){
        if($this->sePuedeEditar())
            return "green";

            return "red";
    
    }

    //retorna los detalles abreviados y separados por coma
    public function getResumenDetalles(){
        $lista = $this->getDetalles();
        $cadena="";
        foreach($lista as $det){
            $cadena .= $det->getDescripcionAbreviada().",";

        }
    

        
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = 150;
        
        if(strlen($cadena) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($cadena, 0, $limiteCaracteres) . '..';
        }
        // Si no, entonces devuelve la cadena normal
        

        return $cadena;

    }


    public function getUltimoDiaDeEdicion(){
        $fechaHoraCreacion = $this->fechaHoraCreacion;
        return date("d/m/Y",strtotime($fechaHoraCreacion."+ ".Configuracion::maximoDiasEdicionOrdenCompra." days")); 
        

    }

    public function getSede(){
        
        return Sede::findOrFail($this->codSede);

    }

    

    public function getEmpleadoCreador(){
        return Empleado::findOrFail($this->codEmpleadoCreador);
    }


    public function borrarArchivos(){

        foreach ($this->getListaArchivos() as $itemArchivo) {
            $nombre = $itemArchivo->nombreGuardado;
            Storage::disk('ordenes')->delete($nombre);
            Debug::mensajeSimple('Se acaba de borrar el archivo:'.$nombre);
        } 

        return ArchivoOrdenCompra::where('codOrdenCompra','=',$this->codOrdenCompra)->delete();


    }

    
    public function getCantidadArchivos(){

        return count($this->getListaArchivos());
    }

    public function getListaArchivos(){

        return ArchivoOrdenCompra::where('codOrdenCompra','=',$this->codOrdenCompra)->get();
    }

    public function getNombreGuardadoNuevoArchivo($j){
        return  OrdenCompra::raizArchivo.
        OrdenCompra::rellernarCerosIzq($this->codOrdenCompra,6).
        '-'.
        OrdenCompra::rellernarCerosIzq($j,2).
        '.marac';


    }

    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleadoCreador);
    }

    public function getProyecto(){
        return Proyecto::findOrFail($this->codProyecto);
    }

    public function getMoneda(){
        return Moneda::findOrFail($this->codMoneda);
    }


    public function getDetalles(){
        return DetalleOrdenCompra::where('codOrdenCompra','=',$this->codOrdenCompra)->get();
    }

    public static function calcularCodigoCedepasLibre(){
        $objNumeracion = Numeracion::getNumeracionOC();
        return  OrdenCompra::RaizCodigoCedepas.
            substr($objNumeracion->año,2,2).
            '-'.
            OrdenCompra::rellernarCerosIzq($objNumeracion->numeroLibreActual,4);
        
    }
    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
    }

    public function getFechaHoraCreacion(){
        return date("d/m/Y h:i:s",strtotime($this->fechaHoraCreacion));
    }


    public function getPDF(){
        $listaItems = DetalleOrdenCompra::where('codOrdenCompra','=',$this->codOrdenCompra)->get();
        $pdf = \PDF::loadview('OrdenCompra.PdfOC',
            array('orden'=>$this,'listaItems'=>$listaItems)
                            )->setPaper('a4', 'portrait');
        
        return $pdf;
    }


    /**ESCRIBIR NUMEROSSSSS */
    function escribirTotalSolicitado(){
        return Numeros::escribirNumero($this->total);
    }

}
