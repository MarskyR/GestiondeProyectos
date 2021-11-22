<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = "distrito";
    protected $primaryKey ="codDistrito";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['nombre','codProvincia'];

    public function getProvincia(){
        return Provincia::findOrFail($this->codProvincia); 
    }

}
