<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistorialLogeo extends Model
{
    protected $table = "historial_logueo";
    protected $primaryKey ="codErrorHistorial";

    public $timestamps = false;   
    protected $fillable = ['codEmpleado','controllerDondeOcurrio','fechaHora','ipEmpleado',
    'descripcionError','funcionDondeOcurrio'];

    public function getErrorAbreviado(){
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = 150;
        $cadena = $this->descripcionError;
        if(strlen($cadena) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($cadena, 0, $limiteCaracteres) . '...';
        }

        // Si no, entonces devuelve la cadena normal
        return $cadena;

    }

    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleado);

    }


    public static function registrarError($th, $action){//$action = app('request')->route()->getAction();
        date_default_timezone_set('America/Lima');
        $error = new ErrorHistorial();
        $error->codEmpleado=Empleado::getEmpleadoLogeado()->codEmpleado;

            $controller = class_basename($action['controller']); // obtiene el nombre base de la clase : "HomeController@index"
            list($controllerName,$action) = explode('@', $controller);
            //explode : {$controllerName : "HomeController", $action : "index"}
            /***************************/
            $error->controllerDondeOcurrio=$controllerName;
            $error->funcionDondeOcurrio=$action;

        $error->fechaHora=new DateTime();

            if(!empty($_SERVER['HTTP_CLIENT_IP'])){
                $error->ipEmpleado=$_SERVER['HTTP_CLIENT_IP'];
            }
            else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $error->ipEmpleado=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else $error->ipEmpleado=$_SERVER['REMOTE_ADDR'];

        //$error->ipEmpleado=$this->getRealIP();
        $error->descripcionError=$th;
        $error->save();
    }
    /*
    public function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
           
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
       
        return $_SERVER['REMOTE_ADDR'];
    }
    */

    public function getNombreEmpleado(){
        $empleado=Empleado::findOrFail($this->codEmpleado);
        return $empleado->getNombreCompleto();
    }
}
