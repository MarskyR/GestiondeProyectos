<?php

namespace App\Http\Controllers;
use App\Configuracion;
use App\ArchivoReqAdmin;
use App\ArchivoReqEmp;
use App\Http\Controllers\Controller;
use App\RequerimientoBS;
use App\Empleado;
use App\Proyecto;
use Illuminate\Http\Request;
use App\DetalleSolicitudFondos;
use App\Banco;
use App\Sede;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;
use App\CDP;
use App\EstadoOrden;
use App\EstadoSolicitudFondos;
use App\SolicitudFalta;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Throw_;
use App\Moneda;
use App\Debug;
use App\DetalleRequerimientoBS;
use App\ErrorHistorial;
use App\Fecha;
use App\Numeracion;
use App\ProyectoContador;
use App\Puesto;
use App\TipoOperacion;
use App\UnidadMedida;
use DateTime;

class RequerimientoBSController extends Controller
{
    //

    const PAGINATION = 30;
    public function listarOfEmpleado(Request $request){
        //filtros
        $codProyectoBuscar=$request->codProyectoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';



        $empleado = Empleado::getEmpleadoLogeado();

        if($codProyectoBuscar==0){
            $requerimientos= RequerimientoBS::where('codEmpleadoSolicitante','=',$empleado->codEmpleado);
        }else
            $requerimientos= RequerimientoBS::where('codEmpleadoSolicitante','=',$empleado->codEmpleado)->where('codProyecto','=',$codProyectoBuscar);
            
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $requerimientos=$requerimientos->where('fechaHoraEmision','>',$fechaInicio)
                ->where('fechaHoraEmision','<',$fechaFin);
        }

        $requerimientos = $requerimientos->orderBy('fechaHoraEmision','DESC')->get();
        $requerimientos = RequerimientoBS::ordenarParaEmpleado($requerimientos)->paginate($this::PAGINATION);
        

        $proyectos = Proyecto::getProyectosActivos();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;

        return view('RequerimientoBS.Empleado.ListarRequerimientos',
            compact('requerimientos','fechaInicio','fechaFin','proyectos','codProyectoBuscar'));
    }


    //FUNCION CUELLO DE BOTELLA
    public function listarRequerimientos(){

        $empleado = Empleado::getEmpleadoLogeado();
        $msj = session('datos');
        $datos='';
        if($msj!='')
            $datos = 'datos';

        if($empleado->esGerente()){
            return redirect()->route('RequerimientoBS.Gerente.Listar')->with($datos,$msj);
        }
        if($empleado->esJefeAdmin()){
            return redirect()->route('RequerimientoBS.Administrador.Listar')->with($datos,$msj);
        }
        if($empleado->esContador()){
            return redirect()->route('RequerimientoBS.Contador.Listar')->with($datos,$msj);
        }
        return redirect()->route('RequerimientoBS.Empleado.Listar')->with($datos,$msj);

    }

    //para consumirlo en js
    public function listarDetalles($idRequerimiento){
        $vector = [];
        $listaDetalles = DetalleRequerimientoBS::where('codRequerimiento','=',$idRequerimiento)->get();
        for ($i=0; $i < count($listaDetalles) ; $i++) { 
            
            $itemDet = $listaDetalles[$i];
            $itemDet['codUnidadMedida'] = UnidadMedida::findOrFail($itemDet->codUnidadMedida)->nombre; //tengo que pasarlo aqui pq en el javascript no hay manera de calcularlo, de todas maneras no lo usaré como Modelo (objeto)
            array_push($vector,$itemDet);            
        }
        return $vector  ;
    }



    public function crear(){
        $listaUnidadMedida = UnidadMedida::All();
        $proyectos = Proyecto::getProyectosActivos();
        $empleadoLogeado = Empleado::getEmpleadoLogeado();
        $objNumeracion = Numeracion::getNumeracionREQ();

        return view('RequerimientoBS.Empleado.CrearRequerimientoBS',
            compact('empleadoLogeado','listaUnidadMedida','proyectos','objNumeracion'));

    }
    public function store(Request $request){
        try{
            DB::beginTransaction(); 
            $requerimiento=new RequerimientoBS();
            $requerimiento->codEstadoRequerimiento=1;
            $requerimiento->codEmpleadoSolicitante=Empleado::getEmpleadoLogeado()->codEmpleado;
            
            $cuenta = $request->cuentaBancariaProveedor;
            if(is_null($cuenta) )  
                $cuenta = "No ingresada";

            $requerimiento->cuentaBancariaProveedor = $cuenta;
            $requerimiento->codProyecto = $request->codProyecto;
            $requerimiento->fechaHoraEmision=Carbon::now();
            $requerimiento->justificacion=$request->justificacion;//cambiar a justificacion (se tiene que cambiar en la vista xdxdxd)
            $requerimiento->fechaHoraRevision=null;
            $requerimiento->fechaHoraAtendido=null;//sisi xd
            $requerimiento->fechaHoraConta=null;
            $requerimiento->observacion=null;
    
            $requerimiento->codigoCedepas = RequerimientoBS::calcularCodigoCedepas(Numeracion::getNumeracionREQ());
            Numeracion::aumentarNumeracionREQ();

            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Crear'),
                null, 
                Puesto::getCodPuesto_Empleado()); //siempre creará el empleado

            
            //creacion de detalles
            $vec[] = '';
            $codREQRecienInsertado = $requerimiento->codRequerimiento;
                
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
                

            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) 
            {
                    $detalle=new DetalleRequerimientoBS();
                    $detalle->codRequerimiento=$requerimiento->codRequerimiento ;//ultimo insertad            
                    $detalle->codUnidadMedida=UnidadMedida::where('nombre','=',$request->get('colTipo'.$i))->get()[0]->codUnidadMedida;

                    $detalle->descripcion=              $request->get('colDescripcion'.$i);
                    $detalle->cantidad=               $request->get('colCantidad'.$i);
                    $detalle->codigoPresupuestal  =  $request->get('colCodigoPresupuestal'.$i);   
                    
                    $detalle->save();  
                    $i=$i+1;
            }
           
            $requerimiento->save();
            

            if( $request->nombresArchivos!='' ){
                
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0;
               
                foreach ($request->file('filenames') as $archivo)
                {   
                    
                    
                    
                    //               CDP-   000002                           -   5   .  jpg

                    $nombreArchivoGuardado = $requerimiento->getNombreGuardadoNuevoArchivoEmp($j+1);
                    Debug::mensajeSimple('el nombre de la imagen es:'.$nombreArchivoGuardado);

                    $archivoReqEmp = new ArchivoReqEmp();
                    $archivoReqEmp->codRequerimiento = $requerimiento->codRequerimiento;
                    $archivoReqEmp->nombreDeGuardado = $nombreArchivoGuardado;
                    $archivoReqEmp->nombreAparente = $nombresArchivos[$j];
                    $archivoReqEmp->save();

                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('requerimientos')->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
                    
            $requerimiento->cantArchivosEmp = $j;
            $requerimiento->nombresArchivosEmp="";
            }
            /* 
            else{
                throw new Exception("No se ingresó ningún archivo.", 1);
            } */
            
            $requerimiento->save();

            DB::commit();
            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','Se ha Registrado el requerimiento N°'.$requerimiento->codigoCedepas);
        }catch(\Throwable $th){
            
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER STORE', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RequerimientoBS.Empleado.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
        
    }

    //empleado
    public function ver($id){


        $requerimiento=RequerimientoBS::findOrFail($id);
        $detalles=$requerimiento->detalles();
        $empleadoLogeado = Empleado::getEmpleadoLogeado();

        if($requerimiento->codEmpleadoSolicitante != $empleadoLogeado->codEmpleado){
            return redirect()->route('error')->with('datos','Los requerimientos solo pueden ser vistos por su creador.');
        }

        return view('RequerimientoBS.Empleado.VerRequerimientoBS',
        compact('requerimiento','empleadoLogeado','detalles'));

    }


    //$cadena = "6*5" para descargar el quinto archivo del req numero 6
    public function descargarArchivoEmp($codArchivoReqEmp){

        $archivo = ArchivoReqEmp::findOrFail($codArchivoReqEmp);
       
        $nombreArchivoGuardado = $archivo->nombreDeGuardado;

        //                          UBICACION                       NOMBRE CON EL QUE SE DESCARGA
        return Storage::download("/requerimientos/".$nombreArchivoGuardado,$archivo->nombreAparente );


    }

    
    function eliminarArchivo($codArchivoReqEmp){
       
        try{
            $archivo = ArchivoReqEmp::findOrFail($codArchivoReqEmp);
        }catch (\Throwable $th) {
            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','ERROR: El archivo que desea eliminar no existe o ya ha sido eliminado, vuelva a la página de editar Requerimiento.');
        }

        try {
            db::beginTransaction();
            
            $nombreArchivEliminado = $archivo->nombreAparente;
            $req = $archivo->getRequerimiento();

            if($req->codEmpleadoSolicitante != Empleado::getEmpleadoLogeado()->codEmpleado)
                throw new Exception("Solo el dueño del Requerimiento puede eliminar sus archivos.", 1);
            

            $archivo->eliminarArchivo();
            DB::commit();
        
            return redirect()->route('RequerimientoBS.Empleado.EditarRequerimientoBS',$req->codRequerimiento)
                ->with('datos','Archivo "'.$nombreArchivEliminado.'" eliminado exitosamente');
        } catch (\Throwable $th) {
            Debug::mensajeError(' REQUERIMIENTO BS ELIMINAR archivo EMP' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codArchivoReqEmp);
            return redirect()->route('RequerimientoBS.Empleado.EditarRequerimientoBS',$req->codRequerimiento)->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }


    //desde una cuenta de contador, eliminar un archivo que haya subido un empleado
    function ContadorEliminarArchivoDelEmpleado($codArchivoReqEmp){
        try {
            db::beginTransaction();
            $archivo = ArchivoReqEmp::findOrFail($codArchivoReqEmp);
            $nombreArchivEliminado = $archivo->nombreAparente;
            $req = $archivo->getRequerimiento();

            $archivo->eliminarArchivo();
            DB::commit();
            
            return redirect()->route('RequerimientoBS.Contador.ver',$req->codRequerimiento)
                ->with('datos','Archivo "'.$nombreArchivEliminado.'" eliminado exitosamente');
        } catch (\Throwable $th) {
            Debug::mensajeError(' REQUERIMIENTO BS ELIMINAR archivo EMP' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codArchivoReqEmp);
            return redirect()->route('RequerimientoBS.Contador.ver',$req->codRequerimiento)
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }






    public function descargarArchivoAdm($codArchivoReqAdm){
        $archivo = ArchivoReqAdmin::findOrFail($codArchivoReqAdm);
        $nombreArchivoGuardado = $archivo->nombreDeGuardado;
        //                          UBICACION                       NOMBRE CON EL QUE SE DESCARGA
        return Storage::download("/requerimientos/".$nombreArchivoGuardado,$archivo->nombreAparente );
    }

    public function editar($id){
        $requerimiento=RequerimientoBS::findOrFail($id);


        $listaUnidadMedida = UnidadMedida::All();
        $proyectos = Proyecto::getProyectosActivos();
        $empleadoLogeado = Empleado::getEmpleadoLogeado();
        
        if($requerimiento->codEmpleadoSolicitante != $empleadoLogeado->codEmpleado){
            return redirect()->route('error')->with('datos','Los requerimientos solo pueden ser editados por el creador.');
        }
        
        return view('RequerimientoBS.Empleado.EditarRequerimientoBS',
            compact('empleadoLogeado','listaUnidadMedida','proyectos','requerimiento'));
    }
    public function update( Request $request){        
        try {
            $requerimiento=RequerimientoBS::findOrFail($request->codRequerimiento);

            if($requerimiento->codEmpleadoSolicitante != Empleado::getEmpleadoLogeado()->codEmpleado)
            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','Error: El requerimiento no puede ser actualizado por un empleado distinto al que la creó.');

            if(!$requerimiento->listaParaActualizar())
            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','Error: el requerimeinto no puede ser actualizado ahora puesto que está en otro proceso.');

            $requerimiento->codProyecto=$request->codProyecto;
            $requerimiento->justificacion=$request->justificacion;

            $cuenta = $request->cuentaBancariaProveedor;
            if(is_null($cuenta) )  
                $cuenta = "No ingresada";
            
            $requerimiento->cuentaBancariaProveedor = $cuenta;

            //si estaba observada, pasa a subsanada
            
            if($requerimiento->verificarEstado('Observada'))
                $requerimiento-> codEstadoRequerimiento = RequerimientoBS::getCodEstado('Subsanada');
            else
                $requerimiento-> codEstadoRequerimiento = RequerimientoBS::getCodEstado('Creada');
            
            $requerimiento-> save();        
            
            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Editar'),
                null, 
                Puesto::getCodPuesto_Empleado()); //siempre creará el empleado



            //$total=0;
            //borramos todos los detalles pq los ingresaremos again
            //DB::select('delete from detalle_requerimiento_bs where codRequerimiento=" '.$requerimiento->codRequerimiento.'"');
            DetalleRequerimientoBS::where('codRequerimiento','=',$requerimiento->codRequerimiento)->delete();

            //creacion de detalles
            $vec[] = '';
            
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
                 

            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) 
            {
                $detalle=new DetalleRequerimientoBS();
                $detalle->codRequerimiento=$requerimiento->codRequerimiento ;//ultimo insertad            
                $detalle->codUnidadMedida=UnidadMedida::where('nombre','=',$request->get('colTipo'.$i))->get()[0]->codUnidadMedida;
                    
                $detalle->descripcion=              $request->get('colDescripcion'.$i);
                $detalle->cantidad=               $request->get('colCantidad'.$i);
                $detalle->codigoPresupuestal  =  $request->get('colCodigoPresupuestal'.$i);   
                
                $detalle->save();  
                $i=$i+1;
            }
           
            $requerimiento->save();


            //SOLO BORRAMOS TODO E INSERTAMOS NUEVOS ARCHIVOS SI ES QUE SE INGRESÓ NUEVOS
            
            if( $request->nombresArchivos!='' ){
                Debug::mensajeSimple("o yara/".$request->tipoIngresoArchivos);
                if($request->tipoIngresoArchivos=="1")
                {//AÑADIR
                    
                }else{//SOBRESRIBIR
                    $requerimiento->borrarArchivosEmp();
                }

                $cantidadArchivosYaExistentes = $requerimiento->getCantidadArchivosEmp();

                
                
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0;
               
                foreach ($request->file('filenames') as $archivo)
                {   
                    
                    $nombreArchivoGuardado = $requerimiento->getNombreGuardadoNuevoArchivoEmp($cantidadArchivosYaExistentes +  $j+1);
                    Debug::mensajeSimple('el nombre de la imagen es:'.$nombreArchivoGuardado);
                    
                    $archivoReqEmp = new ArchivoReqEmp();
                    $archivoReqEmp->codRequerimiento = $requerimiento->codRequerimiento;
                    $archivoReqEmp->nombreDeGuardado = $nombreArchivoGuardado;
                    $archivoReqEmp->nombreAparente = $nombresArchivos[$j];
                    $archivoReqEmp->save();

                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('requerimientos')->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
            }
            


            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','Se ha editado el requerimiento N°'.$requerimiento->codigoCedepas);
        }catch(\Throwable $th){
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER UPDATE', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RequerimientoBS.Empleado.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

        

    }


    public function cancelar($id){
        try {
            DB::beginTransaction();
    
            $requerimiento = RequerimientoBS::findOrFail($id);
            
            if(!$requerimiento->listaParaCancelar())
            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','Error: el requerimiento no puede ser cancelado ahora puesto que está en otro proceso.');
            

            $requerimiento->codEstadoRequerimiento =  RequerimientoBS::getCodEstado('Cancelada');
            $requerimiento->save();
     
            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Cancelar'),
                null, 
                Puesto::getCodPuesto_Empleado()); //siempre contabiliza cont
            

            DB::commit();
            return redirect()->route('RequerimientoBS.Empleado.Listar')
                ->with('datos','Se canceló correctamente el requerimiento '.$requerimiento->codigoCedepas);
        } catch (\Throwable $th) {
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER CANCELAR', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$id);
            return redirect()->route('RequerimientoBS.Empleado.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }



    /**GERENTE DE PROYECTOS */
    public function listarOfGerente(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $codProyectoBuscar=$request->codProyectoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';


        $empleado=Empleado::getEmpleadoLogeado();
        $proyectos= $empleado->getListaProyectos();
        
        if(count($proyectos)==0)
            return redirect()->route('error')->with('datos',"No tiene ningún proyecto asignado...");
        
        $arr=[];
        foreach ($proyectos as $itemproyecto) {
            $arr[]=$itemproyecto->codProyecto;
        }
        

        if($codProyectoBuscar==0){
            $requerimientos=RequerimientoBS::whereIn('codProyecto',$arr);
        }else{
            $requerimientos=RequerimientoBS::where('codProyecto','=',$codProyectoBuscar);
        }
        
        if($codEmpleadoBuscar!=0){
            $requerimientos=$requerimientos->where('codEmpleadoSolicitante','=',$codEmpleadoBuscar);
        }
        
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $requerimientos=$requerimientos->where('fechaHoraEmision','>',$fechaInicio)
                ->where('fechaHoraEmision','<',$fechaFin);
        }

       
        $requerimientos=$requerimientos->orderBy('fechaHoraEmision','DESC')->get();
        $requerimientos= RequerimientoBS::ordenarParaGerente($requerimientos)->paginate($this::PAGINATION);
        

        $empleados=Empleado::getListaEmpleadosPorApellido();
        $proyectos=Proyecto::whereIn('codProyecto',$arr)->get();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;

        return view('RequerimientoBS.Gerente.ListarRequerimientos',compact('requerimientos','empleado','codProyectoBuscar','codEmpleadoBuscar','proyectos','empleados','fechaInicio','fechaFin'));
    }

    public function viewGeren($id){
      
        $requerimiento=RequerimientoBS::findOrFail($id);
        $detalles=$requerimiento->detalles();
        $empleadoLogeado = Empleado::getEmpleadoLogeado();

        return view('RequerimientoBS.Gerente.EvaluarRequerimientoBS',compact('requerimiento','empleadoLogeado','detalles'));
    }

    public function VerAtender($codRequerimiento){
        $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
        $detalles = DetalleRequerimientoBS::where('codRequerimiento','=',$codRequerimiento)->get();
        return view('RequerimientoBS.Administrador.AtenderRequerimientoBS',compact('requerimiento','detalles'));


    }

    public function listarOfAdministrador(Request $request){
        
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $codProyectoBuscar=$request->codProyectoBuscar;
        $filtroTieneFactura = $request->filtroTieneFactura;


        // AÑO                  MES                 DIA
         
        $fechaInicio= Fecha::formatoParaSQL($request->fechaInicio);
        $fechaFin= Fecha::formatoParaSQL($request->fechaFin);


        $empleado=Empleado::getEmpleadoLogeado();
        $proyectos= Proyecto::getProyectosActivos();
        
        $arr=[];
        foreach ($proyectos as $itemproyecto) {
            $arr[]=$itemproyecto->codProyecto;
        }
        

        if($codProyectoBuscar==0){
            $requerimientos=RequerimientoBS::whereIn('codProyecto',$arr);
        }else{
            $requerimientos=RequerimientoBS::where('codProyecto','=',$codProyectoBuscar);
        }

        

        if($filtroTieneFactura == "-1" || is_null($filtroTieneFactura)   ){ //Si es -1, es pq no tomaremos en cuenta el filtro
            $filtroTieneFactura = "-1";
        }else{
            //Debug::mensajeSimple('filtoTieneF='.$filtroTieneFactura);
            if($filtroTieneFactura=="NoRev") $filtroTieneFactura=null;
             
            $requerimientos = $requerimientos->where('tieneFactura','=',$filtroTieneFactura);

            if(is_null($filtroTieneFactura)) $filtroTieneFactura="NoRev"; //revertimos la asignacion anterior
               
            
        }
        
        if($codEmpleadoBuscar!=0){
            $requerimientos=$requerimientos->where('codEmpleadoSolicitante','=',$codEmpleadoBuscar);
        }
        
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $requerimientos=$requerimientos->where('fechaHoraEmision','>',$fechaInicio)
                ->where('fechaHoraEmision','<',$fechaFin);
        }

        $requerimientos=$requerimientos->orderBy('fechaHoraEmision','DESC')->get(); //paginate($this::PAGINATION);
        
        $requerimientos= RequerimientoBS::ordenarParaAdministrador($requerimientos)->paginate($this::PAGINATION);
        
        $empleados=Empleado::getListaEmpleadosPorApellido();
        
         
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;

        return view('RequerimientoBS.Administrador.ListarRequerimientos',
            compact('requerimientos','empleado','codProyectoBuscar','codEmpleadoBuscar',
                    'proyectos','empleados','fechaInicio','fechaFin','filtroTieneFactura'));
    }


    
    /**CONTADOR */
    public function listarOfConta(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $codProyectoBuscar=$request->codProyectoBuscar;
        $filtroTieneFactura = $request->filtroTieneFactura;
        $filtroFacturaContabilizada = $request->filtroFacturaContabilizada;


        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';

        
        $empleado=Empleado::getEmpleadoLogeado();
        $detalles=ProyectoContador::where('codEmpleadoContador','=',$empleado->codEmpleado)->get();
        if(count($detalles)==0)
            return redirect()->route('error')->with('datos',"No tiene ningún proyecto asignado...");
        
        //$proyectos=Proyecto::where('codEmpleadoConta','=',$empleado->codEmpleado)->get();
        $arr2=[];
        foreach ($detalles as $itemproyecto) {
            $arr2[]=$itemproyecto->codProyecto;
        }
        $arr=[3,4,5];
        

        if($codProyectoBuscar==0 || $codProyectoBuscar==null){
            //solo proyectos en el que esta participando
            $requerimientos=RequerimientoBS::whereIn('codEstadoRequerimiento',$arr)->whereIn('codProyecto',$arr2);
        }else{
            $requerimientos=RequerimientoBS::whereIn('codEstadoRequerimiento',$arr)->where('codProyecto','=',$codProyectoBuscar);
        }

        

        if($filtroTieneFactura == "-1" || is_null($filtroTieneFactura)   ){ //Si es -1, es pq no tomaremos en cuenta el filtro
            $filtroTieneFactura = "-1";
        }else{
            //Debug::mensajeSimple('filtoTieneF='.$filtroTieneFactura);
            $requerimientos = $requerimientos->where('tieneFactura','=',$filtroTieneFactura);
        }

        if($filtroFacturaContabilizada == "-1" || is_null($filtroFacturaContabilizada)   ){ //Si es -1, es pq no tomaremos en cuenta el filtro
            $filtroFacturaContabilizada = "-1";
        }else{
            $requerimientos = $requerimientos->where('facturaContabilizada','=',$filtroFacturaContabilizada);
        }

        

        if($codEmpleadoBuscar!=0){
            $requerimientos=$requerimientos->where('codEmpleadoSolicitante','=',$codEmpleadoBuscar);
        }
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $requerimientos=$requerimientos->where('fechaHoraEmision','>',$fechaInicio)
                ->where('fechaHoraEmision','<',$fechaFin);
        }
        $requerimientos=$requerimientos->orderBy('fechaHoraEmision','DESC')->get();
        $requerimientos= RequerimientoBS::ordenarParaContador($requerimientos)->paginate($this::PAGINATION);
        
        

        $proyectos=Proyecto::whereIn('codProyecto',$arr2)->get();
        $empleados=Empleado::getListaEmpleadosPorApellido();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;

        return view('RequerimientoBS.Contador.ListarRequerimientos',compact('requerimientos',
            'empleado','codProyectoBuscar','codEmpleadoBuscar','proyectos','empleados',
            'fechaInicio','fechaFin','filtroTieneFactura','filtroFacturaContabilizada'));
    }
    public function viewConta($id){
      
        $requerimiento=RequerimientoBS::findOrFail($id);
        $detalles=$requerimiento->detalles();
        $empleadoLogeado = Empleado::getEmpleadoLogeado();

        return view('RequerimientoBS.Contador.ContabilizarRequerimientoBS',compact('requerimiento','empleadoLogeado','detalles'));
    }


    /* cambiar a request para subir archivos */
    public function contabilizar(Request $request){
        try{
            DB::beginTransaction();
            
            $requerimiento = RequerimientoBS::findOrFail($request->codRequerimiento);
            
            if(!$requerimiento->listaParaContabilizar())
                return redirect()->route('RequerimientoBS.Listar')
                    ->with('datos','ERROR: El requerimiento ya fue contabilizada o no está apta para serlo.');


            $requerimiento->codEstadoRequerimiento = RequerimientoBS::getCodEstado('Contabilizada');
            $empleadoLogeado = Empleado::getEmpleadoLogeado();  

            $requerimiento->codEmpleadoContador = $empleadoLogeado->codEmpleado;
            $requerimiento->fechaHoraConta=new DateTime();
            
            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Contabilizar'),
                null, 
                Puesto::getCodPuesto_Contador()); //siempre contabiliza cont
            
            
            if( $request->nombresArchivos!='' ){
                Debug::mensajeSimple("Contabilizando /tipoIngresoArchivos = ".$request->tipoIngresoArchivos);
                if($request->tipoIngresoArchivos=="1")
                {//AÑADIR
                    
                }else{//SOBRESRIBIR
                    $requerimiento->borrarArchivosEmp();
                }

                $cantidadArchivosYaExistentes = $requerimiento->getCantidadArchivosEmp();
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0;
               
                foreach ($request->file('filenames') as $archivo)
                {   
                    
                    $nombreArchivoGuardado = $requerimiento->getNombreGuardadoNuevoArchivoEmp($cantidadArchivosYaExistentes +  $j+1);
                    Debug::mensajeSimple('el nombre de la imagen es:'.$nombreArchivoGuardado);
                    
                    $archivoReqEmp = new ArchivoReqEmp();
                    $archivoReqEmp->codRequerimiento = $requerimiento->codRequerimiento;
                    $archivoReqEmp->nombreDeGuardado = $nombreArchivoGuardado;
                    $archivoReqEmp->nombreAparente = $nombresArchivos[$j];
                    $archivoReqEmp->save();

                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('requerimientos')->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
            }
            
            DB::commit();

            return redirect()->route('RequerimientoBS.Contador.Listar')
                ->with('datos','Requerimiento '.$requerimiento->codigoCedepas.' Contabilizado! ');
        } catch (\Throwable $th) {
           Debug::mensajeError('REQUERIMIENTO BS CONTROLLER : CONTABILIZAR',$th);
           DB::rollBack();
           $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
           return redirect()->route('RequerimientoBS.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }


    public function contabilizarFactura($codRequerimiento){
        try{ 
            db::beginTransaction();

            $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
            $requerimiento->facturaContabilizada = 1;
            $requerimiento->save();

            db::commit();
            return redirect()->route('RequerimientoBS.Contador.ver',$codRequerimiento)
                ->with('datos','¡Factura Contabilizada! ');
        }catch(\Throwable $th){
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER : contabilizarFactura',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codRequerimiento);
            return redirect()->route('RequerimientoBS.Contador.ver',$codRequerimiento)
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }

    /**CAMBIO DE ESTADOS */
    public function aprobar(Request $request){//gerente
        //return $request;
        try{
            DB::beginTransaction();
            $requerimiento=RequerimientoBS::find($request->codRequerimiento);

            //AQUI TA EL ERROR
            if(!$requerimiento->listaParaAprobar())
            return redirect()->route('RequerimientoBS.Gerente.Listar')
                ->with('datos','Error: El requerimiento no puede ser aprobado ahora puesto que está en otro proceso.');
            

            $requerimiento->codEstadoRequerimiento =  RequerimientoBS::getCodEstado('Aprobada');
            $requerimiento->codEmpleadoEvaluador = Empleado::getEmpleadoLogeado()->codEmpleado;
            $requerimiento->justificacion = $request->justificacion;
            $requerimiento->fechaHoraRevision=new DateTime();
            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Aprobar'),
                null, 
                Puesto::getCodPuesto_Gerente()); //siempre Aprobar el gerente
            
            
            $listaDetalles = DetalleRequerimientoBS::where('codRequerimiento','=',$requerimiento->codRequerimiento)->get();
            foreach($listaDetalles as $itemDetalle ){
                $itemDetalle->codigoPresupuestal = $request->get('CodigoPresupuestal'.$itemDetalle->codDetalleRequerimiento);
                $itemDetalle->save();
            }
            


            DB::commit();
            
            return redirect()->route('RequerimientoBS.Gerente.Listar')
                ->with('datos','Se aprobó correctamente el requerimiento '.$requerimiento->codigoCedepas);
        }catch(\Throwable $th){
            Debug::mensajeError('REQUERIMIENTO BS APROBAR', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RequerimientoBS.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }

    
    public function rechazar($id){//gerente-administrador (codRequerimiento)
        try{
            DB::beginTransaction();
            $requerimiento=RequerimientoBS::findOrFail($id);

            if(!$requerimiento->listaParaRechazar())
            return redirect()->route('RequerimientoBS.Listar')
                ->with('datos','Error: el requerimiento no puede ser rechazado ahora puesto que está en otro proceso.');


            $empleado=Empleado::getEmpleadoLogeado();
            if($empleado->esJefeAdmin()){
                $requerimiento->codEmpleadoAdministrador=$empleado->codEmpleado;
                $requerimiento->fechaHoraAtendido=new DateTime();
            }
            if($empleado->esGerente()){
                $requerimiento->codEmpleadoEvaluador=$empleado->codEmpleado;
                $requerimiento->fechaHoraRevision=new DateTime();
            }


            $requerimiento->codEstadoRequerimiento=RequerimientoBS::getCodEstado('Rechazada');
            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Rechazar'),
                null, 
                $empleado->codPuesto); //siempre contabiliza cont
            

            DB::commit();
            return redirect()->route('RequerimientoBS.Listar')->with('datos','Se rechazó correctamente el requerimiento '.$requerimiento->codigoCedepas);
        }catch(\Throwable $th){
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER RECHAZAR', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$id);
            return redirect()->route('RequerimientoBS.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }
    public function observar(Request $request){//gerente-administracion
        try{
            DB::beginTransaction();
            $empleado = Empleado::getEmpleadoLogeado();
             
            $requerimiento=RequerimientoBS::find($request->codRequerimientoModal);

            if(!$requerimiento->listaParaObservar())
            return redirect()->route('RequerimientoBS.Listar')
                ->with('datos','Error: El requerimiento no puede ser observado ahora puesto que está en otro proceso.');


            
            if($empleado->esJefeAdmin()){
                $requerimiento->codEmpleadoAdministrador=$empleado->codEmpleado;
                //$requerimiento->fechaHoraAtendido=new DateTime();
            }
            if($empleado->esGerente()){
                $requerimiento->codEmpleadoEvaluador=$empleado->codEmpleado;
                //$requerimiento->fechaHoraRevision=new DateTime();
            }
            $requerimiento->codEstadoRequerimiento=RequerimientoBS::getCodEstado('Observada');

            //para cuando se manda una observacion con '*'
            $txtObservacion=$request->observacion;
            
            $requerimiento->observacion=$txtObservacion;
            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Observar'),
                $txtObservacion, 
                $empleado->codPuesto); //siempre contabiliza cont
            

            DB::commit();
           
            return redirect()->route('RequerimientoBS.Listar')->with('datos','Se observó correctamente el requerimiento '.$requerimiento->codigoCedepas);
            
        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER ',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RequerimientoBS.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }


    /* FUNCION DEL ADMIN */
    /* Ahora esta funcion solo será para atender el requerimiento (cambiar su estado), los archivos se subiran aparte

    */
    public function atender($codRequerimiento){
        try {
            DB::beginTransaction();
            $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
            
            if(!$requerimiento->listaParaAtender())
                return redirect()->route('RequerimientoBS.Administrador.Listar')
                    ->with('datos','ERROR: El requerimiento ya fue atendido o no está apto para serlo.');

            $requerimiento->codEstadoRequerimiento = RequerimientoBS::getCodEstado('Atendida');
            $requerimiento->codEmpleadoAdministrador = Empleado::getEmpleadoLogeado()->codEmpleado;
            $requerimiento->fechaHoraAtendido = Carbon::now();
            
            //Si la factura estaba como No revisada (null), se marca como NO HABIDA. 
            //Si estaba como SI HABIDA, no se cambia 
            if(is_null($requerimiento->tieneFactura))
                $requerimiento->tieneFactura = 0;
            
            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Atender'),
                null, 
                Puesto::getCodPuesto_Administrador()); //siempre atendera el administrad
            

            DB::commit();
            return redirect()->route('RequerimientoBS.Administrador.Listar')
                ->with('datos','Requerimiento '.$requerimiento->codigoCedepas.' Atendido satisfactoriamente.');
        } catch (\Throwable $th) {
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER ATENDER',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codRequerimiento);
            return redirect()->route('RequerimientoBS.Administrador.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }
    

    /* Ahora la funcionalidad para subir los archivos del admin estará separada del ATENDER, proceso que solo cambiará el estado 
    
    Esta funcion estará disponible hasta que se suba la factura
    */
    public function subirArchivosAdministrador(Request $request){
        try{
            db::beginTransaction();
            $requerimiento = RequerimientoBS::findOrFail($request->codRequerimiento);

            $nombresArchivos = explode(', ',$request->nombresArchivos);
            $j=0;

            if($request->tipoIngresoArchivos=="1")
            {//AÑADIR
                
            }else{//SOBRESRIBIR
                $requerimiento->borrarArchivosAdmin();
            }
            $cantidadArchivosYaExistentes = $requerimiento->getCantidadArchivosAdmin();

            foreach ($request->file('filenames') as $archivo){   
                //               CDP-   000002                           -   5   .  jpg
                $nombreArchivoGuardado = $requerimiento->getNombreGuardadoNuevoArchivoAdm($cantidadArchivosYaExistentes + $j+1);
                Debug::mensajeSimple('el nombre de la imagen es:'.$nombreArchivoGuardado);

                $archivoReqAdmin = new ArchivoReqAdmin();
                $archivoReqAdmin->codRequerimiento = $requerimiento->codRequerimiento;
                $archivoReqAdmin->nombreDeGuardado = $nombreArchivoGuardado;
                $archivoReqAdmin->nombreAparente = $nombresArchivos[$j];
                $archivoReqAdmin->save();

                $fileget = \File::get( $archivo );
                
                Storage::disk('requerimientos')->put($nombreArchivoGuardado,$fileget );
                $j++;
            }

            $requerimiento->cantArchivosAdmin = $j;

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Subir archivos de administrador'),
                null, 
                Puesto::getCodPuesto_Administrador()); //siempre contabiliza cont
            
            db::commit();
            return redirect()->route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento)
                ->with('datos','Archivos subidos exitosamente.');
        
        }catch (\Throwable $th) {
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER subirArchivosAdministrador',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RequerimientoBS.Administrador.VerAtender')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }


    public function eliminarArchivosAdmin($codRequerimiento){
        try{
            db::beginTransaction();
            $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);


            $requerimiento->borrarArchivosAdmin();
            $requerimiento->cantArchivosAdmin = 0;

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Eliminar archivos administrador'),
                null, 
                Puesto::getCodPuesto_Administrador()); 
            
            db::commit();
            return redirect()->route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento)
                ->with('datos','Archivos de administrador borrados exitosamente.');
        
        }catch (\Throwable $th) {
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER eliminarArchivosAdmin',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             $codRequerimiento
                                                            );
            return redirect()->route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento)
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }



    }


    /* Funcion exclusiva para marcar si un requerimiento tiene factura o no 
        no se puede marcar que ya tiene factura si no hay ningun archivo admin ingresado en ese req     

    */
    public function marcarQueYaTieneFactura($codRequerimiento){
        try{
            db::beginTransaction();
            $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
            $cant = $requerimiento->getCantidadArchivosAdmin();
            
            
            /*
            DESACTIVADO A PEDIDO DEL CLIENTE 
            if($cant==0)
                return redirect()->route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento)
                    ->with('datos','No se ha subido ninguna factura.');
            */
            $requerimiento->tieneFactura = 1;
            $requerimiento->save();

            $requerimiento->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REQ','Marcar factura'),
                null, 
                Puesto::getCodPuesto_Administrador()); //siempre contabiliza cont
            

            db::commit();
            
            return redirect()->route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento)
                ->with('datos','Factura marcada como HABIDA exitosamente.');
                
        } catch (\Throwable $th) {
            Debug::mensajeError('REQUERIMIENTO BS CONTROLLER marcarSiTieneFactura',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codRequerimiento);
            return redirect()->route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento)
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }








    public function descargarPDF($codRequerimiento){
        $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
        $pdf = $requerimiento->getPDF();
        return $pdf->download('Requerimiento de Bienes y servicios '.$requerimiento->codigoCedepas.'.Pdf');
    }   
    
    public function verPDF($codRequerimiento){
        $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
        $pdf = $requerimiento->getPDF();
        return $pdf->stream('Requerimiento de Bienes y servicios '.$requerimiento->codigoCedepas.'.Pdf');
    }


    
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */
    /* --------------- API -------------- API ---------------- API -------------- API ---------------- API  */


    
    function API_listarREQDeEmpleado($codEmpleado){
         
        $listaRequerimientos = RequerimientoBS::
            where('codEmpleadoSolicitante','=',$codEmpleado)
            ->orderBy('fechaHoraEmision','DESC')->get();
        $listaRequerimientos = RequerimientoBS::ordenarParaEmpleado($listaRequerimientos);

        $listaPreparada = [];
        foreach ($listaRequerimientos as $req) {
            $listaPreparada[] = $req->getVectorParaAPI();
        }

        return $listaPreparada;
         
    }

    function API_getREQ($codRequerimiento){
        $requerimiento = RequerimientoBS::findOrFail($codRequerimiento);
        $listaDetalles = $requerimiento->getDetallesParaAPI();
        
        $reqPreparada = $requerimiento->getVectorParaAPI();
        $reqPreparada['listaDetalles'] = json_encode($listaDetalles);

        return json_encode($reqPreparada);
    }


}
