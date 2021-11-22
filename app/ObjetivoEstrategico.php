<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjetivoEstrategico extends Model
{
    


    protected $table = "objetivo_estrategico_cedepas";
    protected $primaryKey ="codObjetivoEstrategico";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['descripcion','año','esActual'];

    public function esActual(){

        return $this->esActual=='1';

    }

    public function esActualMsj(){
        if($this->esActual())
                  return "Sí";
        return "No";

    }
}
