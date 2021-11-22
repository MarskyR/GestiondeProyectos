<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class LogeoHistorial extends Model
{
    protected $table = "logeo_historial";
    protected $primaryKey ="codLogeoHistorial";

    public $timestamps = false;   
    protected $fillable = ['codEmpleado','fechaHoraLogeo','ipLogeo'];

    public function getEmpleado(){
        return Empleado::findOrFail($this->codEmpleado);
    }

    public static function registrarLogeo(){
        date_default_timezone_set('America/Lima');
        $logeo = new LogeoHistorial();
        $logeo->codEmpleado=Empleado::getEmpleadoLogeado()->codEmpleado;

        $logeo->fechaHoraLogeo=new DateTime();

            if(!empty($_SERVER['HTTP_CLIENT_IP'])){
                $logeo->ipLogeo=$_SERVER['HTTP_CLIENT_IP'];
            }
            else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $logeo->ipLogeo=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else $logeo->ipLogeo=$_SERVER['REMOTE_ADDR'];

        $logeo->save();
    }

    public function getNombreEmpleado(){
        $empleado=Empleado::findOrFail($this->codEmpleado);
        return $empleado->getNombreCompleto();
    }

    public function getFechaHora(){
        
        return date('d/m/Y H:i:s', strtotime($this->fechaHoraLogeo));
        

    }
}
