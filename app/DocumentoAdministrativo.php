<?php

namespace App;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class DocumentoAdministrativo extends Model
{
    /* Funciones que serán para todos los documentos SOL REN REP REQ */
    public function registrarOperacion($codTipoOperacion,$observacion,$codPuesto){
        
        $empleadoLogeado = Empleado::getEmpleadoLogeado();
        $vector = $this->getVectorDocumento();

        $operacion = new OperacionDocumento();
        $operacion->codTipoDocumento = $vector['codTipoDocumento'];
        $operacion->codTipoOperacion = $codTipoOperacion;
        $operacion->codDocumento = $vector['codDocumento'];
        
        $operacion->codEmpleado = $empleadoLogeado->codEmpleado;
        
        $operacion->fechaHora = Carbon::now();
        $operacion->descripcionObservacion = $observacion;
        $operacion->codPuesto = $codPuesto;
        $operacion->save();
         

    }

    public function getListaOperaciones(){
        $vector = $this->getVectorDocumento();
        $codTipoDocumento = $vector['codTipoDocumento'];
        $codDocumento = $vector['codDocumento'];

        return OperacionDocumento::where('codTipoDocumento','=',$codTipoDocumento)
            ->where('codDocumento','=',$codDocumento)
            ->get();
    }
     
}
