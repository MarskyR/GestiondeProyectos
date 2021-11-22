<?php

namespace App\Http\Controllers;
use App\Configuracion;
use App\ArchivoRendicion;
use App\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\SolicitudFondos;
use App\Banco;
use App\DetalleSolicitudFondos;
use App\Proyecto;
use App\Sede;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;
use App\CDP;
use App\DetalleRendicionGastos;
use App\RendicionGastos;
use App\SolicitudFalta;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;


use App\Debug;
use App\ErrorHistorial;
use App\Numeracion;
use App\ProyectoContador;
use App\Puesto;
use App\TipoOperacion;
use DateTime;
use PhpOffice\PhpWord;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Style\Font;
use Throwable;

class RendicionGastosController extends Controller
{
    

    const PAGINATION = 20;
    const raizArchivo = "RendGast-CDP-";

    //RUTA MAESTRA QUE REDIRIJE A LOS INDEX DE LOS 3 ACTORES
    public function listarRendiciones(){

        $empleado = Empleado::getEmpleadoLogeado();
        $msj = session('datos');
        $datos='';
        if($msj!='')
            $datos = 'datos';

        if($empleado->esGerente()){
            //lo enrutamos hacia su index
            return redirect()->route('RendicionGastos.Gerente.Listar')->with($datos,$msj);
        }

        if($empleado->esJefeAdmin())//si es jefe de Administracion
        {
            return redirect()->route('RendicionGastos.Administracion.Listar')->with($datos,$msj);
        }

        if($empleado->esContador())//si es jefe de Administracion
        {
            return redirect()->route('RendicionGastos.Contador.Listar')->with($datos,$msj);
        }

        


        return redirect()->route('RendicionGastos.Empleado.Listar')->with($datos,$msj);


    }

    public function listarContador(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $codProyectoBuscar=$request->codProyectoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';


        $empleado = Empleado::getEmpleadoLogeado();

        //solicitudes de un proyecto (filtro)
        $solicitudesDeProyecto=SolicitudFondos::where('codProyecto','=',$codProyectoBuscar)->get();
        $arr1=[];
        foreach ($solicitudesDeProyecto as $item) {
            $arr1[]=$item->codSolicitud;
        }

        //proyectos del Contador
        $detalles=ProyectoContador::where('codEmpleadoContador','=',$empleado->codEmpleado)->get();
        if(count($detalles)==0)
            return redirect()->route('error')->with('datos',"No tiene ningún proyecto asignado...");
         
        $arr2=[];
        foreach ($detalles as $itemproyecto) {
            $solicitudesDeProyecto=SolicitudFondos::where('codProyecto','=',$itemproyecto->codProyecto)->get();
            foreach ($solicitudesDeProyecto as $item) {
                $arr2[]=$item->codSolicitud;
            }
        }

        $estados =[];
        array_push($estados,RendicionGastos::getCodEstado('Aprobada') );
        array_push($estados,RendicionGastos::getCodEstado('Contabilizada') );
        
        $listaRendiciones = RendicionGastos::whereIn('codEstadoRendicion',$estados);
        if($codProyectoBuscar==0){
            $listaRendiciones=$listaRendiciones->whereIn('codSolicitud',$arr2);
        }else{
            $listaRendiciones=$listaRendiciones->whereIn('codSolicitud',$arr1);
        }
        if($codEmpleadoBuscar!=0){
            $listaRendiciones=$listaRendiciones->where('codEmpleadoSolicitante','=',$codEmpleadoBuscar);
        }
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaRendiciones=$listaRendiciones->where('fechaHoraRendicion','>',$fechaInicio)
                ->where('fechaHoraRendicion','<',$fechaFin);
        }
        $listaRendiciones=$listaRendiciones->orderBy('fechaHoraRendicion','DESC')->get();
        
        $listaRendiciones = RendicionGastos::ordenarParaContador($listaRendiciones)
            ->paginate($this::PAGINATION);
          

        //proyectos disponibles
        $arr2=[];
        foreach ($detalles as $itemproyecto) {
            $arr2[]=$itemproyecto->codProyecto;
        }
        $proyectos=Proyecto::whereIn('codProyecto',$arr2)->get();
        $empleados=Empleado::getListaEmpleadosPorApellido();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;




        return view('RendicionGastos.Contador.ListarRendiciones',compact('listaRendiciones','empleado','codEmpleadoBuscar','codProyectoBuscar','empleados','proyectos','fechaInicio','fechaFin'));
    }


    //retorna todas las rendiciones, tienen prioridad de ordenamiento las que están esperando reposicion
    public function listarJefeAdmin(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $codProyectoBuscar=$request->codProyectoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';


        $empleado = Empleado::getEmpleadoLogeado();
        //solo considera reposiciones hechas por empleados de su misma sede
        $empleados = Empleado::getListaEmpleadosPorApellido();
        $arr2=[];
        foreach ($empleados as $itemempleado) {
            $arr2[]=$itemempleado->codEmpleado;
        }

        //solicitudes de un proyecto (filtro)
        $solicitudesDeProyecto=SolicitudFondos::where('codProyecto','=',$codProyectoBuscar)->get();
        $arr1=[];
        foreach ($solicitudesDeProyecto as $item) {
            $arr1[]=$item->codSolicitud;
        }

        $listaRendiciones = RendicionGastos::where('codEstadoRendicion','>','0');
        if($codProyectoBuscar!=0){
            $listaRendiciones=$listaRendiciones->whereIn('codSolicitud',$arr1);
        }

        /* CORREGIR ESTO, NO DEBERIA TOMAR EN CUENTA A SOLO LOS DE SU SEDE, SINO A TODO EL MUNDO */

        if($codEmpleadoBuscar==0){
            //$listaRendiciones=$listaRendiciones->whereIn('codEmpleadoSolicitante',$arr2);
        }else{
            $listaRendiciones=$listaRendiciones->where('codEmpleadoSolicitante','=',$codEmpleadoBuscar);
        }
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaRendiciones=$listaRendiciones->where('fechaHoraRendicion','>',$fechaInicio)
                ->where('fechaHoraRendicion','<',$fechaFin);
        }
        $listaRendiciones=$listaRendiciones->orderby('fechaHoraRendicion','DESC')->get();

        $listaRendiciones = RendicionGastos::ordenarParaGerente($listaRendiciones)
            ->paginate($this::PAGINATION);
          

        
        $proyectos=Proyecto::getProyectosActivos();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;
        
        return view('RendicionGastos.Administracion.ListarRendiciones',compact(
            'listaRendiciones','empleado','empleados','proyectos','codEmpleadoBuscar','codProyectoBuscar','fechaInicio','fechaFin'));
        
    }


    //lista todas las rendiciones del gerente (pertenecientes a los  proyectos que este lidera)
    public function listarDelGerente(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        $codProyectoBuscar=$request->codProyectoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';

        
        $empleado = Empleado::getEmpleadoLogeado();
        $proyectos= $empleado->getListaProyectos();
        
        if(count($proyectos)==0)
            return redirect()->route('error')->with('datos',"No tiene ningún proyecto asignado...");
        
        //solicitudes de un proyecto (filtro)
        $solicitudesDeProyecto=SolicitudFondos::where('codProyecto','=',$codProyectoBuscar)->get();
        $arr1=[];
        foreach ($solicitudesDeProyecto as $item) {
            $arr1[]=$item->codSolicitud;
        }
        //proyectos del gerente
      
        
        $arr2=[];
        foreach ($proyectos as $itemproyecto) {
            $solicitudesDeProyecto=SolicitudFondos::where('codProyecto','=',$itemproyecto->codProyecto)->get();
            foreach ($solicitudesDeProyecto as $item) {
                $arr2[]=$item->codSolicitud;
            }
        }

        if($codProyectoBuscar==0){
            $listaRendiciones=RendicionGastos::whereIn('codSolicitud',$arr2);
        }else{
            $listaRendiciones=RendicionGastos::whereIn('codSolicitud',$arr1);
        }
        if($codEmpleadoBuscar!=0){
            $listaRendiciones=$listaRendiciones->where('codEmpleadoSolicitante','=',$codEmpleadoBuscar);
        }
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaRendiciones=$listaRendiciones->where('fechaHoraRendicion','>',$fechaInicio)
                ->where('fechaHoraRendicion','<',$fechaFin);
        }

        $listaRendiciones=$listaRendiciones->orderBy('fechaHoraRendicion','DESC')->get();

        $listaRendiciones = RendicionGastos::ordenarParaGerente($listaRendiciones)
            ->paginate($this::PAGINATION);
          


        $empleados=Empleado::getListaEmpleadosPorApellido();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;


        return view('RendicionGastos.Gerente.ListarRendiciones',compact('listaRendiciones','empleado','proyectos','empleados','codEmpleadoBuscar','codProyectoBuscar','fechaInicio','fechaFin'));
        
    }

    //retorna las rendiciones del emp logeado, tienen prioridad de ordenamiento las que están esperando reposicion
    public function listarEmpleado(Request $request){
        //filtros
        $codProyectoBuscar=$request->codProyectoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';


        $empleado = Empleado::getEmpleadoLogeado();
        //primero agarramos las solicitudes del empleado logeado
        //$listaSolicitudes = SolicitudFondos::where('codEmpleadoSolicitante','=',$empleado->codEmpleado)->get();

        
        $listaSolicitudesPorRendir = $empleado->getSolicitudesPorRendir();
        //solicitudes de un proyecto
        $solicitudesDeProyecto=SolicitudFondos::where('codProyecto','=',$codProyectoBuscar)->get();
        $arr=[];
        foreach ($solicitudesDeProyecto as $item) {
            $arr[]=$item->codSolicitud;
        }
         
        $listaRendiciones = RendicionGastos::
            where('codEmpleadoSolicitante','=',Empleado::getEmpleadoLogeado()->codEmpleado);
            

        if($codProyectoBuscar==0){
            $listaRendiciones= RendicionGastos::
                where('codEmpleadoSolicitante','=',Empleado::getEmpleadoLogeado()->codEmpleado);
        }else
            $listaRendiciones= RendicionGastos::
                where('codEmpleadoSolicitante','=',Empleado::getEmpleadoLogeado()->codEmpleado)
                ->whereIn('codSolicitud',$arr);

        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaRendiciones=$listaRendiciones->where('fechaHoraRendicion','>',$fechaInicio)
                ->where('fechaHoraRendicion','<',$fechaFin);
        }
        
        $listaRendiciones = $listaRendiciones->orderBy('fechaHoraRendicion','DESC')->get();
        $listaRendiciones = RendicionGastos::ordenarParaEmpleado($listaRendiciones)
            ->paginate($this::PAGINATION);
            
        $proyectos=Proyecto::getProyectosActivos();


        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;
        
        return view('RendicionGastos.Empleado.ListarRendiciones',
            compact('listaRendiciones','empleado','listaSolicitudesPorRendir','proyectos','codProyectoBuscar','fechaInicio','fechaFin'));
        
    }


    
   




    //del empleado
    public function ver($id){ 
        $rendicion = RendicionGastos::findOrFail($id);
        $solicitud = SolicitudFondos::findOrFail($rendicion->codSolicitud);
        $empleado = Empleado::findOrFail($solicitud->codEmpleadoSolicitante);
        $detallesRend = DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->get();
        $detallesSolicitud = DetalleSolicitudFondos::where('codSolicitud','=',$solicitud->codSolicitud)->get();
        

        if($rendicion->getEmpleadoSolicitante()->codEmpleado != Empleado::getEmpleadoLogeado()->codEmpleado){
            return redirect()->route('error')->with('datos','Las Rendiciones solo pueden ser vistas por su creador.');
        }

        return view('RendicionGastos.Empleado.VerRendicion',compact('rendicion','solicitud','empleado','detallesRend','detallesSolicitud'));
    }
    
    //despliuega vista de  rendicion, del admiin
    public function verAdmin($id){ //le pasamos la id de la solicitud de fondos a la que está enlazada
        $rendicion = RendicionGastos::findOrFail($id);

        $solicitud = SolicitudFondos::findOrFail($rendicion->codSolicitud);
        $empleado = Empleado::findOrFail($solicitud->codEmpleadoSolicitante);
        $detallesRend = DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->get();
        
        return view('RendicionGastos.Administracion.VerRendicionGastos',compact('rendicion','solicitud','empleado','detallesRend'));     
    }


    //despliuega vista de  rendicion,
    public function verGerente($codRend){ 
        //le pasamos la id de la solicitud de fondos a la que está enlazada
        
        $rendicion = RendicionGastos::findOrFail($codRend);
        
        $solicitud = SolicitudFondos::findOrFail($rendicion->codSolicitud);
        $empleado = Empleado::findOrFail($solicitud->codEmpleadoSolicitante);
        $detallesRend = DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->get();
        
        return view('RendicionGastos.Gerente.RevisarRendicionGastos',compact('rendicion','solicitud','empleado','detallesRend'));        
    }

    //despliuega vista de  contabilizar rendicion,
    public function verContabilizar($id){ //le pasamos la id de la rend
        $rendicion = RendicionGastos::findOrFail($id);
        $solicitud = SolicitudFondos::findOrFail($rendicion->codSolicitud);
        $empleado = Empleado::findOrFail($solicitud->codEmpleadoSolicitante);
        $detallesRend = DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->get();

        return view('RendicionGastos.Contador.ContabilizarRendicionGastos',compact('rendicion','solicitud','empleado','detallesRend'));
    }




    public function contabilizar($cadena ){
        
        try {
            DB::beginTransaction(); 
            $vector = explode('*',$cadena);
            $codRendicion = $vector[0];
            $listaItemsAContabilizar = explode(',',$vector[1]);

            $rendicion = RendicionGastos::findOrFail($codRendicion);

            if(!$rendicion->listaParaContabilizar())
                return redirect()->route('RendicionGastos.ListarRendiciones')
                    ->with('datos','Error: la rendición ya fue contabilizada o no se encuentra lista para serlo.');

            $rendicion->codEstadoRendicion =  RendicionGastos::getCodEstado('Contabilizada');
            $rendicion->codEmpleadoContador = Empleado::getEmpleadoLogeado()->codEmpleado;
            $rendicion->save();

            $rendicion->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REN','Contabilizar'),
                null,
                 Puesto::getCodPuesto_Contador()); 
            
            $detallesDeRendicion = $rendicion->getDetalles();


            if( $vector[1] != "" )
                foreach ($detallesDeRendicion as $item) { //guardamos como contabilizados los items que nos llegaron
                    $detGasto = DetalleRendicionGastos::findOrFail($item->codDetalleRendicion);

                    if( in_array($item->codDetalleRendicion,$listaItemsAContabilizar)   ) //Si está para contabilizar
                    {
                        $detGasto->contabilizado = 1;
                        $detGasto->pendienteDeVer = 0;                          
                    }else{
                        $detGasto->contabilizado = 0;
                        $detGasto->pendienteDeVer = 1;
                    }
                    
                    $detGasto->save();   
                }
            
            
            DB::commit();
            return redirect()->route('RendicionGastos.Contador.Listar')
                ->with('datos','Se contabilizó correctamente la Rendición '.
                    $rendicion->codigoCedepas.".");

        } catch (\Throwable $th) {
            Debug::mensajeError('RENDICION GASTOS CONTROLLER CONTABILIZAR', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena);
            return redirect()->route('RendicionGastos.Contador.Listar')
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }




    //despliega vista de edicion EMPLEADO de la rend
    public function editar($idRendicion){
        $rendicion = RendicionGastos::findOrFail($idRendicion);
        $solicitud = SolicitudFondos::findOrFail($rendicion->codSolicitud);
        $listaCDP = CDP::All();

        if($rendicion->getEmpleadoSolicitante()->codEmpleado != Empleado::getEmpleadoLogeado()->codEmpleado){
            return redirect()->route('error')->with('datos','Las Rendiciones solo pueden ser editadas por su creador.');
        }

        return view('RendicionGastos.Empleado.EditarRendicionGastos',compact('rendicion','solicitud','listaCDP'));

    }



    public function revisar($id){ //le pasamos la id de la rendicion de gastos
        $rendicion = RendicionGastos::findOrFail($id);

        $solicitud = SolicitudFondos::findOrFail($rendicion->codSolicitud);
        $empleado = Empleado::findOrFail($solicitud->codEmpleadoSolicitante);
        $detallesRend = DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->get();
        
        return view('RendicionGastos.Gerente.RevisarRendicionGastos',compact('rendicion','solicitud','empleado','detallesRend'));
    }

    /* YA NO SE RECHAZAN RENDICIONES , SOLO SE LAS OBSERVA */
    
    public function aprobar(Request $request){
        try{

            DB::beginTransaction();
           
            $rendicion = RendicionGastos::findOrFail($request->codRendicionGastos);

            if(!$rendicion->listaParaAprobar())
                return redirect()->route('RendicionGastos.ListarRendiciones')
                    ->with('datos','Error: la rendición ya fue aprobada o no se encuentra lista para serlo.');

            $rendicion->codEstadoRendicion = RendicionGastos::getCodEstado('Aprobada');
            $empleadoLogeado = Empleado::getEmpleadoLogeado();
            $rendicion->codEmpleadoEvaluador = $empleadoLogeado->codEmpleado;
            $rendicion->fechaHoraRevisado = Carbon::now();
            
            $rendicion->resumenDeActividad = $request->resumen;
            $rendicion->save();

            $rendicion->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REN','Aprobar'),
                null,
                 Puesto::getCodPuesto_Gerente() ); 
            

            $listaDetalles = DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->get();
            foreach($listaDetalles as $itemDet){
                $itemDet->codigoPresupuestal = $request->get('CodigoPresupuestal'.$itemDet->codDetalleRendicion);
                $itemDet->save();
            }

            DB::commit();
            return redirect()->route('RendicionGastos.Gerente.Listar')
            ->with('datos','Rendicion '.$rendicion->codigoCedepas.' Aprobada');

        } catch (\Throwable $th) {
            error_log('
            
                OCURRIO UN ERROR EN RENDICION GASTOS CONTROLLER : APROBAR
            
                '.$th.'

            ');

            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RendicionGastos.Gerente.Listar')
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }




    public function observar(Request $request){
        try{             
            $codRendicion = $request->codRendicionGastosModal;
            $textoObs = $request->observacion;
             
            DB::beginTransaction();
            error_log('cod rend = '.$codRendicion);
            $rendicion = RendicionGastos::findOrFail($codRendicion);

            if(!$rendicion->listaParaObservar())
                return redirect()->route('RendicionGastos.ListarRendiciones')
                    ->with('datos','Error: la rendición no se encuentra lista para ser observada.');

            $rendicion->codEstadoRendicion = RendicionGastos::getCodEstado('Observada');
            $rendicion->observacion = $textoObs;
             
            $empleadoLogeado = Empleado::getEmpleadoLogeado();
            $rendicion->codEmpleadoEvaluador = $empleadoLogeado->codEmpleado;
            //$rendicion->fechaHoraRevisado = Carbon::now();
            

            $rendicion->save();

            $rendicion->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REN','Observar'),
                $textoObs,
                $empleadoLogeado->codPuesto); 
            

            DB::commit();
            return redirect()->route('RendicionGastos.ListarRendiciones')
            ->with('datos','Rendicion '.$rendicion->codigoCedepas.' Observada');

        } catch (\Throwable $th) {
            Debug::mensajeError('RENDICION GASTOS CONTROLLER : OBSERVAR',$th);
      
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('RendicionGastos.ListarRendiciones')
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }











    
    
    
    
    
    
    
    
    
    /* 
    ALMACENAR LOS DATOS Y LOS ARCHIVOS DE DETALLES QUE ESTAN SUBIENDO
    CADA ARCHIVO ES UNA FOTO DE UN CDP, pero están independientes xd o sea un archivo no está ligado necesariamente a un item de gasto 

    https://www.itsolutionstuff.com/post/laravel-7-multiple-file-upload-tutorialexample.html
    */
    public function store( Request $request){

        //return $request;
        try {
           
                DB::beginTransaction();   
            $solicitud = SolicitudFondos::findOrFail($request->codigoSolicitud);


            if($solicitud->estaRendida=='1')
                return redirect()->route('RendicionGastos.ListarRendiciones')
                    ->with('datos','Error: la solicitud ya se ha rendido.');

            if( !($solicitud->verificarEstado('Abonada') || $solicitud->verificarEstado('Contabilizada')) )
                return redirect()->route('RendicionGastos.ListarRendiciones')
                    ->with('datos','Esta solicitud no puede ser rendida puesto que está en el estado '.$solicitud->getNombreEstado().".");

            
            $solicitud ->estaRendida = 1; //cambiamos el estaod de la solicitud a rendida
            $solicitud->save();
            $solicitud->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('SOL','Crear Rendicion'),
                null, 
                Puesto::getCodPuesto_Empleado() ); //aqui depende del que lo esté rechazando
            

            $rendicion = new RendicionGastos();
            $rendicion-> codSolicitud = $solicitud->codSolicitud;
            $rendicion-> codEmpleadoSolicitante = Empleado::getEmpleadoLogeado()->codEmpleado;
            $rendicion-> totalImporteRecibido = $solicitud->totalSolicitado; //ESTE ES EL DE LA SOLICITUD
            $rendicion-> totalImporteRendido = $request->totalRendido;
            $rendicion-> saldoAFavorDeEmpleado = $rendicion->totalImporteRendido - $rendicion->totalImporteRecibido;
            $rendicion-> resumenDeActividad = $request->resumen;
            $rendicion-> fechaHoraRendicion = Carbon::now();
            $rendicion-> codEstadoRendicion = RendicionGastos::getCodEstado('Creada');
            $rendicion->codMoneda = $solicitud->codMoneda;

            $rendicion->codigoCedepas = RendicionGastos::calcularCodigoCedepas(Numeracion::getNumeracionREN());
            Numeracion::aumentarNumeracionREN();
            

            $rendicion-> save();    
            $rendicion->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REN','Crear'),
                null,
                Puesto::getCodPuesto_Empleado() ); 

            

            $codRendRecienInsertada = (RendicionGastos::latest('codRendicionGastos')->first())->codRendicionGastos;
            

            $vec[] = '';

            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
             
            
            //PRIMERO RECORREMOS la tabla con gastos 
            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) {
                $detalle=new DetalleRendicionGastos();
                $detalle->codRendicionGastos=          $codRendRecienInsertada ;//ultimo insertad
                // formato requerido por sql 2021-02-11   
                //formato dado por mi calnedar 12/02/2020
                $fechaDet = $request->get('colFecha'.$i);
                //DAMOS VUELTA A LA FECHA
                                                // AÑO                  MES                 DIA
                $detalle->fecha=                 substr($fechaDet,6,4).'-'.substr($fechaDet,3,2).'-'.substr($fechaDet,0,2);
                $detalle->setTipoCDPPorNombre( $request->get('colTipo'.$i) );
                $detalle->nroComprobante=        $request->get('colComprobante'.$i);
                $detalle->concepto=              $request->get('colConcepto'.$i);
                $detalle->importe=               $request->get('colImporte'.$i);    
                $detalle->codigoPresupuestal  =  $request->get('colCodigoPresupuestal'.$i);   
                $detalle->nroEnRendicion = $i+1;         
                $detalle->save(); 
                $i=$i+1;
            }    
            

            $nombresArchivos = explode(', ',$request->nombresArchivos);
            $j=0;
            
            if( $request->nombresArchivos!='' ){    
                foreach ($request->file('filenames') as $archivo){   
                    
                    //               CDP-   000002                           -   5   .  jpg
                    $nombreArchivoGuardado = $rendicion->getNombreGuardadoNuevoArchivo($j+1);
                    Debug::mensajeSimple('el nombre de guardado de la imagen es:'.$nombreArchivoGuardado);

                    $archivoRend = new ArchivoRendicion();
                    $archivoRend->codRendicionGastos = $rendicion->codRendicionGastos;
                    $archivoRend->nombreDeGuardado = $nombreArchivoGuardado;
                    $archivoRend->nombreAparente = $nombresArchivos[$j];
                    $archivoRend->save();

                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('rendiciones')
                    ->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
            }
            $rendicion->cantArchivos = $j-1; //ESTO YA NO SE USARÁ
            $terminacionesArchivos = "";    //ESTO YA NO SE USARÁ
            
            $rendicion->save();
            Debug::mensajeSimple('LLEGO 5 ');

            DB::commit();  
            return redirect()
                ->route('RendicionGastos.Empleado.Listar')
                ->with('datos','Se ha creado la rendición N°'.$rendicion->codigoCedepas);
        }catch(Throwable $th){
            
            Debug::mensajeError('STORE REND GASTOS',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('RendicionGastos.Empleado.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

        

    }




















    public function prueba(){
        $lista = RendicionGastos::where('codRendicionGastos','>',50)->get(); 
        $lista->push("AAAAAAAAAAAA");
        return $lista;

    }  

    
    


    public function update( Request $request){
        try {            
            DB::beginTransaction();   
            $rendicion = RendicionGastos::findOrFail($request->codRendicion);

            if(!$rendicion->listaParaActualizar())
            return redirect()->route('RendicionGastos.ListarRendiciones')
                ->with('datos','Error: la rendición no puede ser actualizada ahora puesto que está en otro proceso.');

            $rendicion-> totalImporteRendido = $request->totalRendido;
            $rendicion-> saldoAFavorDeEmpleado = $rendicion->totalImporteRendido - $rendicion->totalImporteRecibido;
            $rendicion-> resumenDeActividad = $request->resumen;
            $rendicion->observacion = "";
            //si estaba observada, pasa a subsanada
            if($rendicion->verificarEstado('Observada'))
                $rendicion-> codEstadoRendicion = RendicionGastos::getCodEstado('Subsanada');
            else
                $rendicion-> codEstadoRendicion = RendicionGastos::getCodEstado('Creada');
            $rendicion-> save();    
            
            $rendicion->registrarOperacion(
                TipoOperacion::getCodTipoOperacion('REN','Editar'),
                null,
                 Puesto::getCodPuesto_Empleado() ); 
                 
            
            //borramos todos los detalles pq los ingresaremos again
            //DB::select('delete from detalle_rendicion_gastos where codRendicionGastos=" '.$rendicion->codRendicionGastos.'"');
            DetalleRendicionGastos::where('codRendicionGastos','=',$rendicion->codRendicionGastos)->delete();

            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
             
            //RECORREMOS la tabla con gastos 
            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) {
                $detalle=new DetalleRendicionGastos();
                $detalle->codRendicionGastos=          $rendicion->codRendicionGastos ;//ultimo insertad
                // formato requerido por sql 2021-02-11   
                //formato dado por mi calnedar 12/02/2020
                $fechaDet = $request->get('colFecha'.$i);
                //DAMOS VUELTA A LA FECHA
                                                // AÑO                  MES                 DIA
                $detalle->fecha=                 substr($fechaDet,6,4).'-'.substr($fechaDet,3,2).'-'.substr($fechaDet,0,2);
                $detalle->setTipoCDPPorNombre( $request->get('colTipo'.$i) );
                $detalle->nroComprobante=        $request->get('colComprobante'.$i);
                $detalle->concepto=              $request->get('colConcepto'.$i);
                $detalle->importe=               $request->get('colImporte'.$i);    
                $detalle->codigoPresupuestal  =  $request->get('colCodigoPresupuestal'.$i);   
                $detalle->nroEnRendicion = $i+1;          
                $i=$i+1;
                $detalle->save();

            }    


            //SOLO BORRAMOS TODO E INSERTAMOS NUEVOS ARCHIVOS SI ES QUE SE INGRESÓ NUEVOS
            if( $request->nombresArchivos!='' ){
                Debug::mensajeSimple("o yara/".$request->tipoIngresoArchivos);
                if($request->tipoIngresoArchivos=="1")
                {//AÑADIR
                }else{//SOBRESRIBIR
                    $rendicion->borrarArchivosCDP();
                }
                $cantidadArchivosYaExistentes = $rendicion->getCantidadArchivos();
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0;
                
                foreach ($request->file('filenames') as $archivo)
                {   
                    
                    //               CDP-   000002                           -   5   .  jpg
                    $nombreArchivoGuardado = $rendicion->getNombreGuardadoNuevoArchivo($cantidadArchivosYaExistentes + $j+1);
                    Debug::mensajeSimple('el nombre de guardado de la imagen es:'.$nombreArchivoGuardado);

                    $archivoRend = new ArchivoRendicion();
                    $archivoRend->codRendicionGastos = $rendicion->codRendicionGastos;
                    $archivoRend->nombreDeGuardado = $nombreArchivoGuardado;
                    $archivoRend->nombreAparente = $nombresArchivos[$j];
                    $archivoRend->save();


                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('rendiciones')
                    ->put($nombreArchivoGuardado,$fileget );
                    $j++;if($request->cantElementos==0)
                    throw new Exception("No se ingresó ningún item.", 1);
                 
                }

                $rendicion->cantArchivos = $j-1; //ESTO YA NO SE USARÁ
                
            }
            $rendicion->save();
            


            DB::commit();  
            return redirect()
                ->route('RendicionGastos.Empleado.Listar')
                ->with('datos','Se ha Editado la rendición N°'.$rendicion->codigoCedepas);
        }catch(\Throwable $th){
            Debug::mensajeError(' RENDICION GASTOS CONTROLLER UPDATE' ,$th);
            
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('RendicionGastos.Empleado.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

        

    }


    



    function rellernarCerosIzq($numero, $nDigitos){
       return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);

    }


    //se le pasa el INDEX del archivo 
    function descargarCDP($codArchivoRend){
        $archivoRend = ArchivoRendicion::findOrFail($codArchivoRend);
        $nombreArchivo = $archivoRend->nombreDeGuardado;
        return Storage::download("/comprobantes/rendiciones/".$nombreArchivo,$archivoRend->nombreAparente);

    }








    public function Reportes(Request $request){

        try 
        {
              

            $fechaI = $request->fechaI;
            $fechaF = $request->fechaF;
            
            $tipoInforme = $request->tipoInforme;

           
            switch ($tipoInforme) {
                case '1': //POR SEDES
                    //Reporte de las sumas acumuladas de los gastos de cada sede, con fecha inicio y fecha final
                    $listaX = RendicionGastos::reportePorSedes($fechaI,$fechaF);
                    
                    return view('RendicionGastos.Administracion.Reportes.ReporteSedes',compact('listaX','fechaI','fechaF'));
                    


                    break;
                case '2': //POR EMPLEADOS
                    //Reporte de las sumas acumuladas de los gastos de cada empleado, con fecha inicio y fecha final

                    $listaX = RendicionGastos::reportePorEmpleados($fechaI,$fechaF);
                    
                    return view('RendicionGastos.Administracion.Reportes.ReporteEmpleado',compact('listaX','fechaI','fechaF'));
                    break;
                case '3':

                    $listaX = RendicionGastos::reportePorProyectos($fechaI,$fechaF);
                    return view('RendicionGastos.Administracion.Reportes.ReporteProyectos',compact('listaX','fechaI','fechaF'));
                
                    break;
                
                case '4':
                    $sede  = Sede::findOrFail($request->ComboBoxSede);
                    $listaX = RendicionGastos::reportePorSedeYEmpleados($fechaI,$fechaF,$sede->codSede);

                return view('RendicionGastos.Administracion.Reportes.ReporteEmpleadoXSede',compact('listaX','fechaI','fechaF','sede'));
                    break;

                            
                default:
                    # code...
                    break;
            }

        } catch (\Throwable $th) {
            

            error_log('\\n ----------------------  RENDICION GASTOS : REPORTES
            Ocurrió el error:'.$th->getMessage().'


            ' );
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return $th->getMessage();

        }
    }



    public function descargarReportes($string){
        try 
        {
            error_log('----------------------------------------------------------'.$string);
            $vector = explode('*',$string);

            
            
            $fechaI      = $vector[0];
            $fechaF      = $vector[1];
            $tipoInforme = $vector[2];
            if ($tipoInforme == 4)
                $codSede = $vector[3];

            $nombreVista = '';
            $argumentosVista ='';
            switch ($tipoInforme) {
                case '1': //POR SEDES
                    //Reporte de las sumas acumuladas de los gastos de cada sede, con fecha inicio y fecha final
                    //return $fechaI;
                    //return $fechaF;
                    $listaX = RendicionGastos::reportePorSedes($fechaI,$fechaF);
                    
                // return  $listaX;
                $nombreVista = 'RendicionGastos.Administracion.Reportes.ReporteSedes';
                $argumentosVista = array('listaX'=> $listaX,'fechaI' =>$fechaI,'fechaF' =>$fechaI);


                    break;
                case '2': //POR EMPLEADOS
                    //Reporte de las sumas acumuladas de los gastos de cada empleado, con fecha inicio y fecha final

                    $listaX = RendicionGastos::reportePorEmpleados($fechaI,$fechaF);
                    

                    $nombreVista = 'RendicionGastos.Administracion.Reportes.ReporteEmpleado';
                    $argumentosVista = array('listaX'=> $listaX,'fechaI' =>$fechaI,'fechaF' =>$fechaI);
                    
                    break;
                case '3':

                    $listaX = RendicionGastos::reportePorProyectos($fechaI,$fechaF);
                    

                    $nombreVista = 'RendicionGastos.Administracion.Reportes.ReporteProyectos';
                    $argumentosVista = array('listaX'=> $listaX,'fechaI' =>$fechaI,'fechaF' =>$fechaI);
                    
                    
                break;
                case '4':
                    $sede = Sede::findOrFail($codSede);
                    $listaX = RendicionGastos::reportePorSedeYEmpleados($fechaI,$fechaF,$codSede);


                    $nombreVista = 'RendicionGastos.Administracion.Reportes.ReporteEmpleadoXSede';
                    $argumentosVista = array('listaX'=> $listaX,'fechaI' =>$fechaI,'fechaF' =>$fechaI,'sede'=>$sede);
                            
                    
                    break;

                            
                default:
                    # code...
                    break;
            }
          

            $pdf = new PDF();
       
            
            $pdf = PDF::loadView($nombreVista,$argumentosVista)->setPaper('a4','landscape');
            return $pdf->download('informeMiau.Pdf');

        } catch (\Throwable $th) {
        
            error_log('\\n ----------------------  RENDICIONGASTOS: DESCARGAR REPORTES
            Ocurrió el error:'.$th->getMessage().'


            ' );
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$string);
        }
    }


    
    public function listarMisGastosRendicion(){
        $listaRendiciones = RendicionGastos::where('codEmpleadoSolicitante','=',Empleado::getEmpleadoLogeado()->codEmpleado)->get(); 
        $listaGastos = new Collection();

        foreach($listaRendiciones as $itemRend){
            $detallesDeEstaRend = DetalleRendicionGastos::where('codRendicionGastos','=',$itemRend->codRendicionGastos)->get();
        
            $listaGastos = $listaGastos->concat($detallesDeEstaRend);
        }
        $listaGastos= $listaGastos->paginate($this::PAGINATION);
        return view('RendicionGastos.Empleado.ListarMisGastos',compact('listaGastos'));
    }

    



    public function marcarDetalleComoVisto($codDetalleRendicion){
        
        $det = DetalleRendicionGastos::findOrFail($codDetalleRendicion);
        $det->pendienteDeVer = 0;
        $det->save();

        return redirect()->route('RendicionGastos.Empleado.verMisGastos')
            ->with('datos','Se ha marcado como visto el gasto, ya no aparecerá en notificaciones.');

    }

    






    
    //funcion servicio, será consumida solo por javascript
    public function listarDetalles($idRendicion){
        $vector = [];
        $listaDetalles = DetalleRendicionGastos::where('codRendicionGastos','=',$idRendicion)->get();
        for ($i=0; $i < count($listaDetalles) ; $i++) { 
            
            $itemDet = $listaDetalles[$i];
            $itemDet['nombreTipoCDP'] = $itemDet->getNombreTipoCDP(); //tengo que pasarlo aqui pq en el javascript no hay manera de calcularlo, de todas maneras no lo usaré como Modelo (objeto)
           
                // formato dado por sql 2021-02-11   
                //formato requerido por mi  12/02/2020
                $fechaDet = $itemDet->fecha;
                //DAMOS VUELTA A LA FECHA
                                // DIA                  MES                 AÑO
            $nuevaFecha=substr($fechaDet,8,2).'/'.substr($fechaDet,5,2).'/'.substr($fechaDet,0,4);
            $itemDet['fechaFormateada'] = $nuevaFecha;
            array_push($vector,$itemDet);            
        }
        return $vector  ;
    }

    
    function eliminarArchivo($codArchivoRen){
        try{
            $archivo = ArchivoRendicion::findOrFail($codArchivoRen);
        }catch (\Throwable $th) {
            return redirect()->route('RendicionGastos.Empleado.Listar')
                ->with('datos','ERROR: El archivo que desea eliminar no existe o ya ha sido eliminado, vuelva a la página de editar Rendición.');
        }

        try {
            db::beginTransaction();


            $nombreArchivEliminado = $archivo->nombreAparente;
            $rend = RendicionGastos::findOrFail($archivo->codRendicionGastos);

            if($rend->codEmpleadoSolicitante != Empleado::getEmpleadoLogeado()->codEmpleado)
                throw new Exception("Solo el dueño de la Rendición puede eliminar sus archivos.", 1);
            
            $archivo->eliminarArchivo();
            DB::commit();
        
            return redirect()->route('RendicionGastos.Empleado.Editar',$rend->codRendicionGastos)
                ->with('datos','Archivo "'.$nombreArchivEliminado.'" eliminado exitosamente');
        } catch (\Throwable $th) {
            Debug::mensajeError(' RENDICION GASTOS CONTROLLER Eliminar archivo' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codArchivoRen);
            return redirect()->route('RendicionGastos.Empleado.Editar',$rend->codRendicionGastos)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
        


    }

    public function descargarPDF($codRendicion){
        $rendicion = RendicionGastos::findOrFail($codRendicion);
        $pdf = $rendicion->getPDF();
        return $pdf->download('Rendición de Gastos '.$rendicion->codigoCedepas.'.Pdf');
    }   
    
    public function verPDF($codRendicion){
        $rendicion = RendicionGastos::findOrFail($codRendicion);
        $pdf = $rendicion->getPDF();
        return $pdf->stream('Rendición de Gastos '.$rendicion->codigoCedepas.'.Pdf');
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



    function API_listarRENDeEmpleado($codEmpleado){
         
        $listaRendiciones = RendicionGastos::
            where('codEmpleadoSolicitante','=',$codEmpleado)
            ->orderBy('fechaHoraRendicion','DESC')->get();
        
        $listaRendiciones = RendicionGastos::ordenarParaEmpleado($listaRendiciones);
          

        $listaPreparada = [];
        foreach ($listaRendiciones as $ren) {
            $listaPreparada[] = $ren->getVectorParaAPI();
        }

        return $listaPreparada;
         
    }

    function API_getREN($codRendicionGastos){
        $rendicion = RendicionGastos::findOrFail($codRendicionGastos);
        $listaDetalles = $rendicion->getDetallesParaAPI();
        

        $renPreparada = $rendicion->getVectorParaAPI();
        $renPreparada['listaDetalles'] = json_encode($listaDetalles);

        

        return json_encode($renPreparada);
    }






}
