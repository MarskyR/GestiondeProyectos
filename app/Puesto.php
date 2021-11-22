<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    public $timestamps = false;

    protected $table = 'puesto';

    protected $primaryKey = 'codPuesto';

    protected $fillable = [
        'nombre','estado'
    ];

    public function getActivo(){
        if($this->estado=='1')
            return "SÍ";

        return "NO";
    }
    public static function getCodigo($nombrePuesto){
        $lista = Puesto::where('nombre','=',$nombrePuesto)->get();
        if(count($lista)==0)
            return "";
        return $lista[0]->codPuesto;
    }

    public function area(){//singular pq un producto es de una cateoria
        return $this->hasOne('App\Area','codArea','codArea');//el tercer parametro es de Producto
    }

    private static function getPuestoPorNombre($nombrePuesto){
        return Puesto::where('nombre','=',$nombrePuesto)->get()[0];
    } 

    public static function getCodPuesto_Empleado(){
        return static::getPuestoPorNombre('Empleado')->codPuesto;
    }
    public static function getCodPuesto_Gerente(){
        return static::getPuestoPorNombre('Gerente')->codPuesto;
    }
    public static function getCodPuesto_Contador(){
        return static::getPuestoPorNombre('Contador')->codPuesto;
    }
    public static function getCodPuesto_Administrador(){
        return static::getPuestoPorNombre('Administrador')->codPuesto;
    }
    public static function getCodPuesto_UGE(){
        return static::getPuestoPorNombre('UGE')->codPuesto;
    }
    
}
