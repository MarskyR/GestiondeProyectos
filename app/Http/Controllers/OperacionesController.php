<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ArchivoOrdenCompra;
use App\ArchivoProyecto;
use App\ArchivoRendicion;
use App\ArchivoReposicion;
use App\ArchivoReqAdmin;
use App\ArchivoReqEmp;
use App\ArchivoSolicitud;
use App\Configuracion;
use App\Empleado;
use App\ErrorHistorial;
use App\EstadoRendicionGastos;
use App\EstadoReposicionGastos;
use App\EstadoRequerimientoBS;
use App\EstadoSolicitudFondos;
use App\OperacionDocumento;
use App\Puesto;
use App\RendicionGastos;
use App\ReposicionGastos;
use App\RequerimientoBS;
use App\RespuestaAPI;
use App\SolicitudFondos;
use App\TipoOperacion;
use Illuminate\Support\Facades\DB;

class OperacionesController extends Controller
{
    const paginacion = 75;

    public function ListarOperaciones(){
        $listaOperaciones = OperacionDocumento::orderBy('fechaHora','DESC')
            ->orderBy('codOperacionDocumento','DESC')
            ->paginate(static::paginacion);
        
        return view('Operaciones.ListarOperaciones',compact('listaOperaciones'));

    }



    /* BUSCADOR MAESTRO DE DOCUMENTOS */
    public function buscadorMaestro(){

        $listaEmpleados = Empleado::orderBy('apellidos')->get();

        return view('Operaciones.ListarMaestro',compact('listaEmpleados'));
        
    }


    function GetListadoBusqueda(Request $request){
        $buscar_codigoCedepas = $request->buscar_codigoCedepas;
        $buscar_tipoDocumento = $request->buscar_tipoDocumento;
        $buscar_codEmpleadoEmisor = $request->buscar_codEmpleadoEmisor;
        
        $listaSOF = SolicitudFondos::where('codigoCedepas','!=','0');
        $listaREN = RendicionGastos::where('codigoCedepas','!=','0');
        $listaREP = ReposicionGastos::where('codigoCedepas','!=','0');
        $listaREQ = RequerimientoBS::where('codigoCedepas','!=','0');

        if($buscar_codigoCedepas!=""){
            error_log('rar');
            $listaSOF = $listaSOF->where('codigoCedepas','like',"%$buscar_codigoCedepas%");
            $listaREN = $listaREN->where('codigoCedepas','like',"%$buscar_codigoCedepas%");
            $listaREP = $listaREP->where('codigoCedepas','like',"%$buscar_codigoCedepas%");
            $listaREQ = $listaREQ->where('codigoCedepas','like',"%$buscar_codigoCedepas%");
        }


        if($buscar_codEmpleadoEmisor!="-1"){
            $listaSOF = $listaSOF->where('codEmpleadoSolicitante','=',$buscar_codEmpleadoEmisor);
            $listaREN = $listaREN->where('codEmpleadoSolicitante','=',$buscar_codEmpleadoEmisor);
            $listaREP = $listaREP->where('codEmpleadoSolicitante','=',$buscar_codEmpleadoEmisor);
            $listaREQ = $listaREQ->where('codEmpleadoSolicitante','=',$buscar_codEmpleadoEmisor);
        }



        switch($buscar_tipoDocumento){
            case 'SOF':
                $listaSOF = $listaSOF->get(); 
                $listaREN = []; 
                $listaREP = []; 
                $listaREQ = []; 
                break;
            case 'REN':
                $listaSOF = [];
                $listaREN = $listaREN->get(); 
                $listaREP = [];
                $listaREQ = [];
                break;
            
            case 'REP':
                $listaSOF = []; 
                $listaREN = [];
                $listaREP = $listaREP->get(); 
                $listaREQ = [];
                break;
            case 'REQ':
                $listaSOF = [];
                $listaREN = [];
                $listaREP = [];
                $listaREQ = $listaREQ->get(); 
                break;
            case 'TODOS': 
                $listaSOF = $listaSOF->get(); 
                $listaREN = $listaREN->get(); 
                $listaREP = $listaREP->get(); 
                $listaREQ = $listaREQ->get(); 
                break;
            
        }


        $sof_estados = EstadoSolicitudFondos::All();
        $ren_estados = EstadoRendicionGastos::All();
        $rep_estados = EstadoReposicionGastos::All();
        $req_estados = EstadoRequerimientoBS::All();

        
        return view('Operaciones.Invocables.inv_listadoBuscadorMaestro',compact('listaSOF','listaREN','listaREP','listaREQ',
            'buscar_codigoCedepas','sof_estados','ren_estados','rep_estados','req_estados'));

    }


    function CambiarEstadoDocumento(Request $request){


        try{
            db::beginTransaction();
            $codNuevoEstado = $request->codNuevoEstado;
            $tipoDoc = $request->tipoDoc;
            $idDocumento = $request->idDocumento;
            switch($tipoDoc){
                case 'SOF': 
                    $documento = SolicitudFondos::findOrFail($idDocumento);
                    break;
                case 'REN': 
                    $documento = RendicionGastos::findOrFail($idDocumento);
                    break;
                case 'REP': 
                    $documento = ReposicionGastos::findOrFail($idDocumento);
                    break;
                case 'REQ': 
                    $documento = RequerimientoBS::findOrFail($idDocumento);
                    break;
                                            
            }

            $documento->setEstado($codNuevoEstado);
            $documento->save();

            $codCedepas = $documento->codigoCedepas;
            $nombreNuevoEstado = $documento->getNombreEstado();

            db::commit();

            return RespuestaAPI::respuestaOk("Se ha cambiado el estado del documento $codCedepas a $nombreNuevoEstado. <br> VERIFIQUE QUE EL NUEVO ESTADO SEA MENOR AL ANTERIOR PARA EVITAR PROBLEMAS");
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
        }


    }





    /* ---------- EN ESTE CONTROLLER ESTARÁN FUNCIONALIDADES DE MANTENIMIENTO DEL SISTEMA ---------- */
    /* Lo ideal es que cada una se ejecute una vez noma, pero lo tendré guardado por si acaso */

    function migrarAUTF8Archivos(){
        try {
                    
            DB::beginTransaction();
            $listaArchivos = ArchivoOrdenCompra::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            $listaArchivos = ArchivoProyecto::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            $listaArchivos = ArchivoRendicion::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            $listaArchivos = ArchivoReposicion::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            $listaArchivos = ArchivoReqAdmin::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            $listaArchivos = ArchivoReqEmp::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            $listaArchivos = ArchivoSolicitud::All();
            foreach($listaArchivos as $archivo){
                $archivo->nombreAparente8 = $archivo->nombreAparente;
                $archivo->save();
            }
            db::commit();
            return "nombre aparente copiado exitosamente a nombreAparente8";
        } catch (\Throwable $th) {
            
            db::rollBack();
            throw $th;
        }
         
    }



    /* En todas las operaciones que tengan como tipo de op aprobar, setea el codigo de actor en gerente */
    function arreglarErrorContadorGerente(){
        try {
                    
            DB::beginTransaction();
            $listaTOAprobar = TipoOperacion::where('nombre','=','Aprobar')->get();
            foreach($listaTOAprobar as $to){
                $vectorCodTipoOperacion[] =  $to->codTipoOperacion;
            }
            
            $listaOperaciones = OperacionDocumento::whereIn('codTipoOperacion',$vectorCodTipoOperacion)->get();
            foreach ($listaOperaciones as $op) {
                $op->codPuesto = Puesto::getCodPuesto_Gerente(); 
                $op->save();
            }
            db::commit();
            return "se ha cambiado los de contador a gerente";
        } catch (\Throwable $th) {
            
            db::rollBack();
            throw $th;
        }
       


    }



    /* Esta funcion obtiene datos de los documentos administrativos (fecha de creacion y los actores), 
    y los inserta en mi tabla de operacion */
    function poblarHistorialOperaciones(){
        

    }
}
