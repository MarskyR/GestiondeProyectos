<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenCompra extends Model
{
    protected $table = "detalle_orden_compra";
    protected $primaryKey ="codDetalleOrdenCompra ";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['cantidad','descripcion','valorDeVenta','precioVenta','subtotal','codOrdenCompra','exoneradoIGV','codUnidadMedida'];

    public function getDescripcionAbreviada(){

        
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = 25;
        $cadena = $this->descripcion;
        if(strlen($cadena) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($cadena, 0, $limiteCaracteres) . '..';
        }

        // Si no, entonces devuelve la cadena normal
        return $cadena;

    }

    public function getUnidadMedida(){
        return UnidadMedida::findOrFail($this->codUnidadMedida);
    }

    public function tieneIGV(){
        if($this->exoneradoIGV==1){
            return '';
        }else return 'checked';
    }

    public function getOrdenCompra(){
        return OrdenCompra::findOrFail($this->codOrdenCompra);
    }

}
