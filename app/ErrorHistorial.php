<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class ErrorHistorial extends Model
{
    protected $table = "error_historial";
    protected $primaryKey ="codErrorHistorial";

    public $timestamps = false;   
    protected $fillable = ['codEmpleado','controllerDondeOcurrio','fechaHora','ipEmpleado',
    'descripcionError','funcionDondeOcurrio','estadoError'];


    public function getChecked(){
        if($this->estadoError==1)
            return "checked";

        return "";
      
    }
    public function getFechaHora(){
        return date("d/m/Y h:i:s",strtotime($this->fechaHora));
    }

    public function getErrorAbreviado(){
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = 100;
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


    public static function registrarError($th, $action,$request){//$action = app('request')->route()->getAction();
        
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

        $error->estadoError=0;
        $error->descripcionError=ErrorHistorial::acortarError($th);
        
        //A ESTA INSTANCIA REQUEST YA LLEGA COMO UN STRING (ya jsoneado)
        $error->formulario = $request;
        
        $error->save();
            
        MaracsoftBot::enviarMensaje("CodError #".$error->codErrorHistorial."            EnProduccion?: ".Configuracion::estaEnProduccion().
        "  Empleado ".Empleado::getEmpleadoLogeado()->getNombreCompleto()." (".Empleado::getEmpleadoLogeado()->getNombrePuesto(). 
                ") generó el error en ".$controllerName." -> ".$action.
                " DESCRIPCION DEL ERROR:                                                " .ErrorHistorial::acortarParaTelegram($error->descripcionError) );
            

        //Debug::mensajeSimple('Codigo de error generado: '.$error->codErrorHistorial);

        return $error->codErrorHistorial;
    }
 
    

    public function getNombreEmpleado(){
        $empleado=Empleado::findOrFail($this->codEmpleado);
        return $empleado->getNombreCompleto();
    }

    const tamañoMaximoError = 5000;
 
    public static function acortarError($descripcionError){
        
        // Si la longitud es mayor que el límite...
        $limiteCaracteres = ErrorHistorial::tamañoMaximoError;

        if(strlen($descripcionError) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($descripcionError, 0, $limiteCaracteres) . '... error acortado';
        }

        // Si no, entonces devuelve la cadena normal
        return $descripcionError;
    

    }

    const tamañoParaTelegram = 1000;
    //telegram permite maximo 4096 asi que lo limitaremos a 3500
    public static function acortarParaTelegram($descripcionError){

        // Si la longitud es mayor que el límite...
        $limiteCaracteres = ErrorHistorial::tamañoParaTelegram;

        if(strlen($descripcionError) > $limiteCaracteres){
            // Entonces corta la cadena y ponle el sufijo
            return substr($descripcionError, 0, $limiteCaracteres) . '... error acortado';
        }

        // Si no, entonces devuelve la cadena normal
        return $descripcionError;

    }

}
