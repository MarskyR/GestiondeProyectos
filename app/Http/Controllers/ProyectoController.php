<?php

namespace App\Http\Controllers;
#region Uses
use App\ActividadResultado;
use App\ArchivoProyecto;
use App\ArchivoReposicion;
use App\Configuracion;
use App\Http\Controllers\Controller;
use App\Proyecto;
use Illuminate\Http\Request;
use App\Empleado;
use App\ProyectoContador;
use App\Puesto;
use Illuminate\Support\Facades\DB;
use NumberFormatter;
use App\LugarEjecucion;
use App\Sede;
use App\Debug;
use App\Departamento;
use App\Provincia;
use App\Distrito;
use App\PoblacionBeneficiaria;
use App\EntidadFinanciera;
use App\ErrorHistorial;
use App\EstadoProyecto;
use App\Fecha;
use App\IndicadorActividad;
use App\IndicadorObjEspecifico;
use App\ObjetivoEstrategico;
use App\PlanEstrategicoInstitucional;
use App\RelacionProyectoObj;
use App\ObjetivoEspecifico;
use App\ResultadoEsperado;
use App\IndicadorResultado;
use App\MedioVerificacionMeta;
use App\MedioVerificacionResultado;
use App\MetaEjecutada;
use Illuminate\Support\Facades\Storage;
use App\Moneda;
use App\PersonaJuridicaPoblacion;
use App\PersonaNaturalPoblacion;
use App\PersonaPoblacion;
use App\RelacionProyectoObjMilenio;
use App\RespuestaAPI;
use App\TipoArchivoProyecto;
use App\TipoFinanciamiento;
use DateTime;
use Exception;
use Illuminate\Http\Response;
//require __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#endregion Uses
class ProyectoController extends Controller
{
    


    function RedirigirAProyectoSegunActor($codProyecto){
        $datos = session('datos');

        $empleado = Empleado::getEmpleadoLogeado();

        if($empleado->esGerente())
            return redirect()->route('GestionProyectos.Gerente.Ver',$codProyecto)->with('datos',$datos);
        
        if($empleado->esUGE())
            return redirect()->route('GestiónProyectos.editar',$codProyecto)->with('datos',$datos);

        return redirect()->route('user.home');
    }

    function RedirigirAVistaMetas($codProyecto){
        $datos = session('datos');
        $empleado = Empleado::getEmpleadoLogeado();
        
        if($empleado->esGerente())
            return redirect()->route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$codProyecto)->with('datos',$datos);
        
        if($empleado->esUGE())
            return redirect()->route('GestionProyectos.UGE.RegistrarMetasEjecutadas',$codProyecto)->with('datos',$datos);

        return redirect()->route('user.home');


    }

#region CRUDPROYECTOS
    
    
    //VISTA INDEX de proyectos PARA EL ADMINISTRADOR
    function index(){
        $listaProyectos = Proyecto::All();
        $listaGerentes = Empleado::getListaGerentesActivos();
        $listaContadores = Empleado::getListaContadoresActivos();
        $listaEstados = EstadoProyecto::All();

        return view('Proyectos.AdminSistema.ListarProyectos',
            compact('listaProyectos','listaGerentes','listaContadores','listaEstados'));


    }
    





    /* Lista los proyectos del gerente */
    function listarGerente(){
        $listaProyectos = Empleado::getEmpleadoLogeado()->getListaProyectos();
        $empleado = Empleado::getEmpleadoLogeado();

        return view('Proyectos.Gerente.ListarProyectos',
            compact('listaProyectos','empleado'));

    }


    function crear(){
        
        $listaDepartamentos = Departamento::All();
        $listaFinancieras = EntidadFinanciera::All();
        $listaPEIs = PlanEstrategicoInstitucional::All();
        $listaMonedas = Moneda::All();
        $listaSedes = Sede::All();
        $listaTipoFinanciamiento = TipoFinanciamiento::All();
        
        return view('Proyectos.Create',compact(
        'listaDepartamentos',
        'listaFinancieras',
        'listaPEIs',
        'listaMonedas',
        'listaSedes',
        'listaTipoFinanciamiento'
        
        
        ));
    }
    
    function editar($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $lugaresEjecucion = LugarEjecucion::where('codProyecto','=',$codProyecto)->get();
        $poblacionesBeneficiarias = PoblacionBeneficiaria::where('codProyecto','=',$codProyecto)->get();
        $listaDepartamentos = Departamento::All();
        $listaFinancieras = EntidadFinanciera::All();
        $listaPorcentajes = $proyecto->getPorcentajesObjEstrategicos();
        $listaPEIs = PlanEstrategicoInstitucional::All();
        $listaObjetivosEspecificos = ObjetivoEspecifico::where('codProyecto','=',$codProyecto)->get();
        $listaResultadosEsperados = ResultadoEsperado::where('codProyecto','=',$codProyecto)->get();
        $listaMonedas = Moneda::All();
        $listaSedes = Sede::All();
        $listaTipoFinanciamiento = TipoFinanciamiento::All();
        $listaActividades = $proyecto->getListaActividades();
        $listaTiposArchivos = TipoArchivoProyecto::All();
        $relacionesObjMilenio = RelacionProyectoObjMilenio::where('codProyecto','=',$codProyecto)->get();


        $listaIndicadoresResultados = $proyecto->getIndicadoresResultados();

        return view('Proyectos.EditarProyecto',compact('listaSedes','proyecto','lugaresEjecucion','listaDepartamentos',
            'poblacionesBeneficiarias','listaFinancieras','listaPorcentajes','listaPEIs','listaObjetivosEspecificos',
            'listaResultadosEsperados','listaMonedas','listaTipoFinanciamiento','listaActividades','listaIndicadoresResultados','listaTiposArchivos','relacionesObjMilenio'));
    }
    //PARA EXCEL
    function ExportarPoblacionBeneficiaria($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $poblacionesBeneficiarias = PoblacionBeneficiaria::where('codProyecto','=',$codProyecto)->get();
        
        return view('Proyectos.ExportarPoblacionBeneficiaria', compact('proyecto','poblacionesBeneficiarias'));
    }
    ///////

    function store(Request $request){
        try {
            DB::beginTransaction();

            //validamos si ya hay otro proyecto activo con ese cod presupuestal
            $proyectosMismoCodPresup = Proyecto::where('codigoPresupuestal','=',$request->codigoPresupuestal)
                ->where('codEstadoProyecto','!=',Proyecto::getCodEstado('Finalizado'))
                ->get();
            if(count($proyectosMismoCodPresup)!=0){

                return redirect()->route('GestiónProyectos.AdminSistema.Listar')
                    ->with('datos','ERROR: Ya existe un proyecto con el código presupuestal ingresado ['.$request->codigoPresupuestal.'].');
            }

            
            $proyecto = new Proyecto();
            $proyecto->codEstadoProyecto = Proyecto::getCodEstado('En Registro');

            $fechaInicio= Fecha::formatoParaSQL($request->fechaInicio);
            $proyecto->fechaInicio = $fechaInicio;
            $fechaFinalizacion = Fecha::formatoParaSQL($request->fechaFinalizacion);
            $proyecto->fechaFinalizacion = $fechaFinalizacion;
            
            $proyecto->nombre = $request->nombre;
            $proyecto->codPEI = $request->codPEI;
            $proyecto->codigoPresupuestal = $request->codigoPresupuestal;
            $proyecto->nombreLargo = $request->nombreLargo;
            $proyecto->codEntidadFinanciera = $request->codEntidadFinanciera;

            $proyecto->codMoneda = $request->codMoneda;
            
            $proyecto->importeContrapartidaCedepas = $request->importeContrapartidaCedepas;
            $proyecto->importeContrapartidaPoblacionBeneficiaria = $request->importeContrapartidaPoblacionBeneficiaria;
            $proyecto->importeContrapartidaOtros = $request->importeContrapartidaOtros;
            $proyecto->importeFinanciamiento = $request->importeFinanciamiento;

            
            $proyecto->importePresupuestoTotal = 
                $proyecto->importeContrapartidaCedepas + 
                $proyecto->importeContrapartidaPoblacionBeneficiaria + 
                $proyecto->importeContrapartidaOtros +
                $proyecto->importeFinanciamiento;
            


            $proyecto->codTipoFinanciamiento = $request->codTipoFinanciamiento;
            $proyecto->codSedePrincipal = $request->codSede;
            $proyecto->objetivoGeneral = $request->objetivoGeneral;


            $proyecto->save();
            $proyecto->inicializarObjetivosMilenio();//inserta en cada objetivo del milenio

            DB::commit();

            return redirect()->route('GestiónProyectos.AdminSistema.Listar')->with('datos','Proyecto creado exitosamente.');
        } catch (\Throwable $th) {
           
            Debug::mensajeError('PROYECTO CONTROLLER STORE',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestiónProyectos.AdminSistema.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }

    function update(Request $request){
        //return $request;
        try {
            DB::beginTransaction();

            $proyecto = Proyecto::findOrFail($request->codProyecto);
            $proyecto->nombre = $request->nombre;

            $fechaInicio= Fecha::formatoParaSQL($request->fechaInicio);
            $proyecto->fechaInicio = $fechaInicio;
            $fechaFinalizacion = Fecha::formatoParaSQL($request->fechaFinalizacion);
            $proyecto->fechaFinalizacion = $fechaFinalizacion;
            
            $proyecto->codigoPresupuestal = $request->codigoPresupuestal;
            $proyecto->nombreLargo = $request->nombreLargo;
            $proyecto->codEntidadFinanciera = $request->codEntidadFinanciera;
            
            $proyecto->codMoneda = $request->codMoneda;
            
            $proyecto->importeContrapartidaCedepas = $request->importeContrapartidaCedepas;
            $proyecto->importeContrapartidaPoblacionBeneficiaria = $request->importeContrapartidaPoblacionBeneficiaria;
            $proyecto->importeContrapartidaOtros = $request->importeContrapartidaOtros;
            $proyecto->importeFinanciamiento = $request->importeFinanciamiento;

            
            $proyecto->importePresupuestoTotal = 
                $proyecto->importeContrapartidaCedepas + 
                $proyecto->importeContrapartidaPoblacionBeneficiaria + 
                $proyecto->importeContrapartidaOtros +
                $proyecto->importeFinanciamiento;
            


            $proyecto->codTipoFinanciamiento = $request->codTipoFinanciamiento;
            $proyecto->codSedePrincipal = $request->codSede;
            $proyecto->objetivoGeneral = $request->objetivoGeneral;


            $proyecto->save();

            DB::commit();

            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)->with('datos','Proyecto actualizado exitosamente.');
        } catch (\Throwable $th) {
           
            Debug::mensajeError('PROYECTO CONTROLLER update',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }

    function actualizarContacto(Request $request){
        try {
            DB::beginTransaction();

            $proyecto = Proyecto::findOrFail($request->codProyecto);

            $proyecto->contacto_nombres = $request->contacto_nombres;
            $proyecto->contacto_telefono = $request->contacto_telefono;
            $proyecto->contacto_correo = $request->contacto_correo;

            $proyecto->save();
            DB::commit();

            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)->with('datos','Datos del contacto actualizados exitosamente.');
        } catch (\Throwable $th) {
           
            Debug::mensajeError('PROYECTO CONTROLLER actualizarContacto',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }




    }


    /* Funcion ejecutada desde JS con un get */
    /* La cadena tiene el formato 15*2
    Donde 15 esel codigo del proyecto
    donde 2 es el codigo del nuevo estado
    */
    function actualizarEstado($cadena){
        try {
            db::beginTransaction();            
            $vector = explode('*',$cadena);
            $codProyecto = $vector[0];
            $codNuevoEstado = $vector[1];

            $proyecto = Proyecto::findOrFail($codProyecto);
            $proyecto->codEstadoProyecto = $codNuevoEstado;
            $proyecto->save();

            db::commit();

            return TRUE;
        } catch (\Throwable $th) {
            
            Debug::mensajeError('PROYECTO CONTROLLER actualizarEstado',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena);
            return FALSE;
        }

    }

    /*  
    DEPRECADO
    function darDeBaja($codProyecto){
        try {
            DB::beginTransaction();

            $proyecto = Proyecto::findOrFail($codProyecto);
            $proyecto->codEstadoProyecto = Proyecto::getCodEstado('Finalizado');

            $proyecto->save();

            DB::commit();

            return redirect()->route('GestiónProyectos.AdminSistema.Listar')->with('datos','Proyecto Dado de baja exitosamente.');
        } catch (\Throwable $th) {
           
            Debug::mensajeError('PROYECTO CONTROLLER STORE',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestiónProyectos.AdminSistema.Listar')->with('datos',Configuracion::mensajeErrorEstandar.str_pad($codErrorHistorial, 7, '0', STR_PAD_LEFT));
        }
    }
 
    */


#endregion CRUDPROYECTOS



    /* FUNCION CON FILTROS, LISTA TODOS LOS PROYECTOS PERO PARA LA UGE */
    function listarProyectosParaUGE(Request $request){


        //para filtros
        $nombreProyectoBuscar=$request->nombreProyectoBuscar;
        $codSedeBuscar=$request->codSedeBuscar;
        $codEntidadFinancieraBuscar=$request->codEntidadFinancieraBuscar;
        $codTipoFinanciamientoBuscar=$request->codTipoFinanciamientoBuscar;
        $añoInicioBuscar=$request->añoInicioBuscar;
        $añoFinBuscar=$request->añoFinBuscar;
        $vectorObjetivosPEISeleccionados = $request->vectorObjetivosPEISeleccionados;
        $codPEI = $request->codPEI;
        
        /* Tipo financiamiento
                Coop internacional
                fondos nacionales estatales
                fondos nacionales estatales por donacion
                fondos nacionales estatales por facturacion
                fondos empresa privada
                fondos empresa privada por donacion
                fondos empresa privada por facturacion
                
                
        */

        /* Aqui filtro para: 
            Tipo financiamiento ***
            financiera**
            año de inicio
            sede principal**
            nombre del proyecto **
        */
        //return $request;
        $listaPEIs = PlanEstrategicoInstitucional::All();
        $listaProyectos = Proyecto::where('codEstadoProyecto','>=','0'); //jala a todos xd
        if($nombreProyectoBuscar!=''){
            $listaProyectos=$listaProyectos->where('nombre','like','%'.$nombreProyectoBuscar.'%');
        }
        if($codSedeBuscar!=0){
            $listaProyectos=$listaProyectos->where('codSedePrincipal','=',$codSedeBuscar);
        }
        if($codEntidadFinancieraBuscar!=0){
            $listaProyectos=$listaProyectos->where('codEntidadFinanciera','=',$codEntidadFinancieraBuscar);
        }
        

        
        if($vectorObjetivosPEISeleccionados != ""){
            $vectorObjPEI = explode(',',$request->vectorObjetivosPEISeleccionados);
            $vectorRelaciones = RelacionProyectoObj::whereIn('codObjetivoEstrategico',$vectorObjPEI)->where('porcentajeDeAporte','>','0')->get();

            $vectorCodProyectosConEsosObj = [];
            foreach ($vectorRelaciones as $rel) {
                array_push($vectorCodProyectosConEsosObj,$rel->codProyecto);
            }
             
            //return $vectorCodProyectosConEsosObj;
            $listaProyectos = $listaProyectos->whereIn('codProyecto',$vectorCodProyectosConEsosObj);
        }

        if($codTipoFinanciamientoBuscar!=0){
            switch ($codTipoFinanciamientoBuscar) {
                case '20':
                    $arr=[3,4];
                    $listaProyectos=$listaProyectos->whereIn('codTipoFinanciamiento',$arr);
                    break;
                case '30':
                    $arr=[5,6];
                    $listaProyectos=$listaProyectos->whereIn('codTipoFinanciamiento',$arr);
                    break;
                default:
                    $listaProyectos=$listaProyectos->where('codTipoFinanciamiento','=',$codTipoFinanciamientoBuscar);
                    break;
            }
        }

        if( ($añoInicioBuscar!="") && ($añoFinBuscar!="") ){/* Si viene sin filtros */
            if($añoFinBuscar > $añoInicioBuscar){/* Caso normal */

                $fechaInicio=$añoInicioBuscar.'-01-01';
                $fechaFin=$añoFinBuscar.'-12-31';
                Debug::mensajeSimple($fechaInicio." --- ". $fechaFin);
                $listaProyectos=$listaProyectos->where('fechaInicio','>=',$fechaInicio)->where('fechaInicio','<=',$fechaFin);
            }else{
                
                
                if($añoFinBuscar == $añoInicioBuscar){//si se ingresó el mismo año en ambas, listamos de ese año
                    $año = $añoInicioBuscar;
                    $fechaInicio=$año.'-01-01';
                    $fechaFin=$año.'-12-31';
                    $listaProyectos=$listaProyectos->where('fechaInicio','>=',$fechaInicio)->where('fechaInicio','<=',$fechaFin);
                }

                if($añoFinBuscar < $añoInicioBuscar){ //Si se ingresó 2021 y 2010, para que se arreglen los limites cuando vuelva a la vista
                    $añoInicioBuscar = $añoFinBuscar;
                }
            }
        
        }
        

        $listaProyectos = $listaProyectos->get();


        $listaGerentes = Empleado::getListaGerentesActivos();
        $listaContadores = Empleado::getListaContadoresActivos();
        $listaEstados = EstadoProyecto::All();
        //para filtros
        $sedes=Sede::all();
        $entidades=EntidadFinanciera::all();

        $tiposFinanciamiento=TipoFinanciamiento::all();
        $arr=[];
        foreach ($tiposFinanciamiento as $itemtipo) {
            $arr[]=[$itemtipo->codTipoFinanciamiento,$itemtipo->nombre];
        }
        $arr[]=[20,"Fondos Nacionales Estatales"];
        $arr[]=[30,"Fondos de la Empresa Privada"];
        $tiposFinanciamiento=$arr;

        
        
        return view('Proyectos.UGE.ListarProyectos',
            compact('listaProyectos','listaGerentes','listaContadores','nombreProyectoBuscar','sedes','codSedeBuscar',
                    'entidades','codEntidadFinancieraBuscar','tiposFinanciamiento','codTipoFinanciamientoBuscar',
                        'añoFinBuscar','añoInicioBuscar','listaEstados','listaPEIs','codPEI','vectorObjetivosPEISeleccionados'));
                        

    }

    //PARA EXCEL
    function ExportarProyectosParaUGE(Request $request){
       
        //para filtros
        
        $nombreProyectoBuscar=$request->nombreProyectoBuscar;
        $codSedeBuscar=$request->codSedeBuscar;
        $codEntidadFinancieraBuscar=$request->codEntidadFinancieraBuscar;
        $codTipoFinanciamientoBuscar=$request->codTipoFinanciamientoBuscar;
        $añoInicioBuscar=$request->añoInicioBuscar;
        $añoFinBuscar=$request->añoFinBuscar;
        $codPEI = $request->codPEI;
        $vectorObjetivosPEISeleccionados = $request->vectorObjetivosPEISeleccionados;

        //return $request;
        $empleado = Empleado::getEmpleadoLogeado();

        $listaProyectos = Proyecto::where('codEstadoProyecto','>=','0'); //jala a todos xd
        if($nombreProyectoBuscar!=''){
            $listaProyectos=$listaProyectos->where('nombre','like','%'.$nombreProyectoBuscar.'%');
        }
        if($codSedeBuscar!=0){
            $listaProyectos=$listaProyectos->where('codSedePrincipal','=',$codSedeBuscar);
        }
        if($codEntidadFinancieraBuscar!=0){
            $listaProyectos=$listaProyectos->where('codEntidadFinanciera','=',$codEntidadFinancieraBuscar);
        }
        if($codPEI!='-1'){
            $listaProyectos=$listaProyectos->where('codPEI','=',$codPEI);
        }


        
        if($vectorObjetivosPEISeleccionados != ""){
            $vectorObjPEI = explode(',',$request->vectorObjetivosPEISeleccionados);
            $vectorRelaciones = RelacionProyectoObj::whereIn('codObjetivoEstrategico',$vectorObjPEI)->where('porcentajeDeAporte','>','0')->get();

            $vectorCodProyectosConEsosObj = [];
            foreach ($vectorRelaciones as $rel) {
                array_push($vectorCodProyectosConEsosObj,$rel->codProyecto);
            }
             
            //return $vectorCodProyectosConEsosObj;
            $listaProyectos = $listaProyectos->whereIn('codProyecto',$vectorCodProyectosConEsosObj);
        }


        if($codTipoFinanciamientoBuscar!=0){
            switch ($codTipoFinanciamientoBuscar) {
                case '20':
                    $arr=[3,4];
                    $listaProyectos=$listaProyectos->whereIn('codTipoFinanciamiento',$arr);
                    break;
                case '30':
                    $arr=[5,6];
                    $listaProyectos=$listaProyectos->whereIn('codTipoFinanciamiento',$arr);
                    break;
                default:
                    $listaProyectos=$listaProyectos->where('codTipoFinanciamiento','=',$codTipoFinanciamientoBuscar);
                    break;
            }
        }

        if( ($añoInicioBuscar!="") && ($añoFinBuscar!="") ){/* Si viene sin filtros */
            if($añoFinBuscar > $añoInicioBuscar){/* Caso normal */

                $fechaInicio=$añoInicioBuscar.'-01-01';
                $fechaFin=$añoFinBuscar.'-12-31';
                Debug::mensajeSimple($fechaInicio." --- ". $fechaFin);
                $listaProyectos=$listaProyectos->where('fechaInicio','>=',$fechaInicio)->where('fechaInicio','<=',$fechaFin);
            }else{
                
                
                if($añoFinBuscar == $añoInicioBuscar)//si se ingresó el mismo año en ambas, listamos de ese año
                {
                    $año = $añoInicioBuscar;

                    $fechaInicio=$año.'-01-01';
                    $fechaFin=$año.'-12-31';

                    $listaProyectos=$listaProyectos->where('fechaInicio','>=',$fechaInicio)->where('fechaInicio','<=',$fechaFin);
                }

                if($añoFinBuscar < $añoInicioBuscar){ //Si se ingresó 2021 y 2010, para que se arreglen los limites cuando vuelva a la vista
                    $añoInicioBuscar = $añoFinBuscar;
                }
            }
        
        }
        $listaProyectos = $listaProyectos->get();

        //$codSedeBuscar=Sede::find($codSedeBuscar);
        //Debug::mensajeSimple($codSedeBuscar);
        //Debug::mensajeSimple('xdxdxdxdxd');

        if($codSedeBuscar!=0){
            $codSedeBuscar=Sede::findOrFail($codSedeBuscar)->nombre;
        }else $codSedeBuscar="";

        if($codEntidadFinancieraBuscar!=0){
            $codEntidadFinancieraBuscar=EntidadFinanciera::findOrFail($codEntidadFinancieraBuscar)->nombre;
        }else $codEntidadFinancieraBuscar="";

        if($codTipoFinanciamientoBuscar!=0){
            $codTipoFinanciamientoBuscar=TipoFinanciamiento::findOrFail($codTipoFinanciamientoBuscar)->nombre;
        }else $codTipoFinanciamientoBuscar="";

        if($añoInicioBuscar==1800 && $añoFinBuscar==2100){
            $años="";
        }else $años=$añoInicioBuscar." - ".$añoFinBuscar;
         
        return view('Proyectos.UGE.ExportarListaProyectos', compact('listaProyectos','nombreProyectoBuscar',
            'codSedeBuscar','codEntidadFinancieraBuscar','codTipoFinanciamientoBuscar','años','empleado','codPEI'));
 
            
    }
    ///////

    /*  funcion servicio se consume desde JS */
    function getCodigoPresupuestal($id){
        error_log('['.Proyecto::findOrFail($id)->codigoPresupuestal.']');
        return Proyecto::findOrFail($id)->codigoPresupuestal;
    } 


#region PEI y porcentajes
    function actualizarPEI(Request $request){

        try{
            $proyecto = Proyecto::findOrFail($request->codProyecto);
            $proyecto->codPEI = $request->codPEI;
            $proyecto->eliminarPorcentajesDeObjetivos();

            $listaNuevosObj = ObjetivoEstrategico::where('codPEI','=',$request->codPEI)->get();

            foreach($listaNuevosObj as $itemObj){
                
                $rela = new RelacionProyectoObj();
                $rela->codObjetivoEstrategico = $itemObj->codObjetivoEstrategico;
                $rela->codProyecto = $proyecto->codProyecto;
                $rela->porcentajeDeAporte = 0;
                $rela->save();

            }
            $proyecto->save();

            return RespuestaAPI::respuestaOk('PEI Actualizado correctamente.');
        
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaOk(Configuracion::getMensajeError($codErrorHistorial));
            
        }
    
    
    }

    function actualizarPorcentajesObjetivos(Request $request){
        try{
            $proyecto = Proyecto::findOrFail($request->codProyecto);

            $listaPorcentajes = RelacionProyectoObj::where('codProyecto','=',$proyecto->codProyecto)->get();
            foreach($listaPorcentajes as $itemPorcentaje){
                $itemPorcentaje->porcentajeDeAporte = $request->get('porcentaje'.$itemPorcentaje->codRelacion);
                $itemPorcentaje->save();

            }

            return RespuestaAPI::respuestaOk('Porcentajes de Objetivos Actualizados correctamente.');
            
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
            
        }
    
    
    }
#endregion PEI y porcentajes

#region Objetivos del milenio

    /* ESTO SE CONSUME DESDE javascript con jquery.post,  */
    function actualizarPorcentajesMilenio(Request $request){
        
        try{
            DB::beginTransaction();
            $proyecto = Proyecto::findOrFail($request->codProyecto);
 
            $listaRelaciones = RelacionProyectoObjMilenio::where('codProyecto','=',$proyecto->codProyecto)->get();
            foreach($listaRelaciones as $rel){
                $rel->porcentaje = $request->get('codPorcentaje'.$rel->codRelacion);
                $rel->save();
            }
            //Debug::mensajeSimple('EL REQUEST QUE LLEGÓ ES: ' .json_encode($request->toArray()));
            db::commit();
            return RespuestaAPI::respuestaOk("Se han actualizado los aportes del proyecto a los objetivos del milenio");
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
        }


    }

#endregion Objetivos del milenio


#region CRUD Contadores de un proyecto
    /* Despliega vista para ver los contadores de un proyecto */
    function listarContadores($id){
        $proyecto=Proyecto::findOrFail($id);
        $contadoresSeleccionados=$proyecto->getContadores();
        $arr=[];
        foreach ($contadoresSeleccionados as $itemcontador) {
            $arr[]=$itemcontador->codEmpleado;
        }
        $contadores=Empleado
            ::where('codPuesto','=',Puesto::getCodigo('Contador'))
            ->whereNotIn('codEmpleado',$arr)->get();
            

        return view('Proyectos.ContadoresProyecto',compact('proyecto','contadores','contadoresSeleccionados'));
    }

    function agregarContador(Request $request){


        $detalle=new ProyectoContador();
        $detalle->codProyecto=$request->codProyecto;
        $detalle->codEmpleadoContador=$request->codEmpleadoConta;
        $detalle->save();

        return redirect()->route('GestiónProyectos.ListarContadores',$request->codProyecto);



    }

    function eliminarContador($id){

        try{
            db::beginTransaction();
            $detalle=ProyectoContador::where('codEmpleadoContador','=',$id)->get();
            
            /* REVISAR  */

            $detalle[0]->delete();

            db::commit();
            return redirect()->route('GestiónProyectos.ListarContadores',$detalle[0]->codProyecto)
                ->with('datos','Contador eliminado.');

        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$id);
            return redirect()->route('GestiónProyectos.ListarContadores',$detalle[0]->codProyecto)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }


    }


    function actualizarProyectosYGerentesContadores($id){
        try{
            $arr = explode('*', $id);
            DB::beginTransaction();
            $proyecto=Proyecto::findOrFail($arr[0]);
            $gerente=Empleado::findOrFail($arr[1]);
            if($arr[2]==1){
                $proyecto->codEmpleadoDirector=$gerente->codEmpleado;
            }else{
                $proyecto->codEmpleadoConta=$gerente->codEmpleado;
            }
            $proyecto->save();
            DB::commit();
            return true;
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$id);
            return false;
        }
    }

    
    /**PARA RELLENAR PROYECTO_CONTADOR */
    public function RellenarProyectoContador(){


        try{
            db::beginTransaction();
            //borramos todos los actuales
            $listaActual = ProyectoContador::where('codProyectoContador','>','0')->delete();
            

            $contadores=Empleado::getListaContadoresActivos();
            $proyectos=Proyecto::getProyectosActivos();

            foreach ($proyectos as $itemproyecto) {
                foreach ($contadores as $itemcontador) {
                    $detalle=new ProyectoContador();
                    $detalle->codEmpleadoContador=$itemcontador->codEmpleado;
                    $detalle->codProyecto=$itemproyecto->codProyecto;
                    $detalle->save();
                }
            }

            db::commit();

            return redirect()->route('GestiónProyectos.AdminSistema.Listar')
                ->with('datos','Se han asignado todos los contadores registrados a todos los proyectos registrados.');


        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),"");
            return redirect()->route('GestiónProyectos.AdminSistema.Listar')
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
 
        }


    }

#endregion CRUD Contadores de un proyecto


#region Resultados esperados y sus indicadores

    public function agregarEditarResultadoEsperado(Request $request){
        
        try{
            db::beginTransaction();
            if($request->codResultadoEsperado=="0"){
                $resultado = new ResultadoEsperado();
                $resultado->descripcion = $request->descripcionNuevoResultado;
                $resultado->codProyecto = $request->codProyecto;
                $mensaje = "agregado";
            }else{ //editando
                
                $resultado = ResultadoEsperado::findOrFail($request->codResultadoEsperado);
                $resultado->descripcion = $request->descripcionNuevoResultado;
                $mensaje = "editado";
            }
            $resultado->save();
            db::commit();

            return RespuestaAPI::respuestaOk('Se ha '.$mensaje.' el resultado esperado "'.$resultado->descripcion.'" .');

        
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return  RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
             
        }

    }
    
    public function eliminarResultadoEsperado($codResultadoEsperado){

        try{
            db::beginTransaction();
            $resultado = ResultadoEsperado::findOrFail($codResultadoEsperado);
            $codProyecto = $resultado->codProyecto;
            $descripcion = $resultado->descripcion;
            if($resultado->getCantidadIndicadoresResultados()!=0)
                return RespuestaAPI::respuestaError('ERROR: Para eliminar el resultado esperado "'.$descripcion.'" debe eliminar primero sus indicadores de resultados.');
            
            if($resultado->getCantidadActividades()!=0)
                return RespuestaAPI::respuestaError('ERROR: Para eliminar el resultado esperado "'.$descripcion.'" debe eliminar primero sus actividades.');
            
            $nombre = $resultado->descripcion;
            $resultado->delete();
            db::commit();

            return RespuestaAPI::respuestaOk('Se ha eliminado el resultado esperado "'.$nombre.'" .');
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codResultadoEsperado);
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));

        }

    }




    public function agregarEditarIndicadorResultado(Request $request){        
        try{
            db::beginTransaction();

            if($request->codIndicadorResultado == "0"){/* NUEVO */
                $indicador = new IndicadorResultado();
                $indicador->descripcion = $request->descripcionNuevoIndicadorResultado;
                $indicador->codResultadoEsperado = $request->ComboBoxResultadoEsperadoX;
                $mensaje = "agregado";
                
            }else{
                $indicador = IndicadorResultado::findOrFail($request->codIndicadorResultado);
                $indicador->descripcion = $request->descripcionNuevoIndicadorResultado;
                $indicador->codResultadoEsperado = $request->ComboBoxResultadoEsperadoX;
                $mensaje = "editado";
            }
            $indicador->save();
              

    
            db::commit();
            return RespuestaAPI::respuestaOk('Indicador "'.$indicador->descripcion.'" '.$mensaje.' exitosamente.');


        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('Proyecto Controller : agregadeditarIndicadorResultado',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
             
        }



    }




    public function eliminarIndicadorResultado($codIndicador){
        
        try{
            db::beginTransaction();
        
            $indicador = IndicadorResultado::findOrFail($codIndicador);
            $codProyecto = $indicador->getResultadoEsperado()->codProyecto;

            $nombre = $indicador->descripcion;
            if($indicador->getCantidadMediosVerificacion() != 0 )
                return 'Error: para eliminar el indicador, este no debe tener medios de verificación.';
            
            $indicador->delete();

            /* FALTA CODIGO PARA ELIMINAR  LOS MEDIOS DE CADA INDICADOR */
            db::commit();
            return RespuestaAPI::respuestaOk('Se ha eliminado el indicador de Resultado "'.$nombre.'" .');
        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('proyecto controller eliminar indicador resultado',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codIndicador);
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));

        }


    }

#endregion 




#region INVOCABLES
// se consumen desde JS y retornan el html puro del contenido de un desplegable

    public function invocarObjetivosEspecificos($codProyecto){

        $proyecto = Proyecto::findOrFail($codProyecto);
        $listaObjetivosEspecificos = ObjetivoEspecifico::where('codProyecto','=',$codProyecto)->get();
       
        return view('Proyectos.Desplegables.Invocables.inv_ObjetivosEspecificos',compact('proyecto','listaObjetivosEspecificos'));

    }

    
    public function invocarResultadosEsperadosActividades($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $listaResultadosEsperados =  ResultadoEsperado::where('codProyecto','=',$codProyecto)->get();
        $listaActividades = $proyecto->getListaActividades();

        return view('Proyectos.Desplegables.Invocables.inv_ResultadosEspActividades',
            compact('proyecto','listaResultadosEsperados','listaActividades'));
    }

    
    public function invocarResultadosEsperadosIndicadores($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $listaResultadosEsperados =  ResultadoEsperado::where('codProyecto','=',$codProyecto)->get();
         
        $listaIndicadoresResultados = $proyecto->getIndicadoresResultados();

        return view('Proyectos.Desplegables.Invocables.inv_ResultadosEspIndicadores',
            compact('proyecto','listaResultadosEsperados','listaIndicadoresResultados'));
    }


    public function invocarPoblacionesBeneficiarias($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $poblacionesBeneficiarias = PoblacionBeneficiaria::where('codProyecto','=',$codProyecto)->get();
        return view('Proyectos.Desplegables.Invocables.inv_PobBen',compact('proyecto','poblacionesBeneficiarias'));

    }
    public function invocarTablaObjEstr($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $listaPorcentajes = $proyecto->getPorcentajesObjEstrategicos();
        return view('Proyectos.Desplegables.Invocables.inv_ObjEstrat',compact('proyecto','listaPorcentajes'));

    }

    public function invocarLugaresEjecucion($codProyecto){
        $lugaresEjecucion = LugarEjecucion::where('codProyecto','=',$codProyecto)->get();
        $proyecto = Proyecto::findOrfail($codProyecto);

        $listaDepartamentos = Departamento::All();

        return view('Proyectos.Desplegables.Invocables.inv_LugaresEjecucion',compact('lugaresEjecucion','proyecto','listaDepartamentos'));

    }

    //retorna la funcion javascript para cargar a los maestros detalle del proyecto (en js)
    public function cargarMaestrosDetalle($codProyecto){
        $listaObjetivosEspecificos = ObjetivoEspecifico::where('codProyecto','=',$codProyecto)->get();
        $listaResultadosEsperados = $listaResultadosEsperados = ResultadoEsperado::where('codProyecto','=',$codProyecto)->get();
        $poblacionesBeneficiarias = PoblacionBeneficiaria::where('codProyecto','=',$codProyecto)->get();
        
        $porcentajesObjetivosEstrategicos = RelacionProyectoObj::where('codProyecto','=',$codProyecto)->get();

        $listaPEIs = PlanEstrategicoInstitucional::All();
        $objetivosEspecificos = [];
        $indicadoresObjetivos = [];
        $resultadosEsperados = [];
        $indicadoresResultados = [];
        $mediosVerificacion = [];
        $actividadesResultados=[];
        $indicadoresActividades = [];
        



        foreach($poblacionesBeneficiarias as $itemPob){
            $itemPob['cantidadPersonasTotal'] = 
                count($itemPob->getPersonasNaturales()) + 
                count($itemPob->getPersonasJuridicas());
        }

        foreach ($listaObjetivosEspecificos as $itemObjEspecifico ) {
            $objetivosEspecificos[] = [
                'codObjEspecifico' => $itemObjEspecifico->codObjEspecifico,
                'descripcion' => $itemObjEspecifico->descripcion,
                'codProyecto' => $itemObjEspecifico->codProyecto,
                'cantidadIndicadores' => $itemObjEspecifico->getCantidadIndicadores()
            ];
            foreach ($itemObjEspecifico->getListaDeIndicadores() as $indicadorObj) {
                $indicadoresObjetivos[] = [
                    'codIndicadorObj' => $indicadorObj->codIndicadorObj,
                    'descripcion' => $indicadorObj->descripcion,
                    'codObjEspecifico' => $indicadorObj->codObjEspecifico
                ];
            }
        }

        
        foreach($listaResultadosEsperados as $res){
            $resultadosEsperados[] = [
                'codResultadoEsperado' => $res->codResultadoEsperado,
                'descripcion' => $res->descripcion,
                'codProyecto' => $res->codProyecto,
                'cantidadIndicadoresResultados'  => $res->getCantidadIndicadoresResultados(),
                'cantidadActividades'  => $res->getCantidadActividades()
            ];

            foreach ($res->getListaIndicadoresResultados() as $indicadorRes){
                $indicadoresResultados[] = [
                    'codIndicadorResultado'  => $indicadorRes->codIndicadorResultado,
                    'descripcion'  => $indicadorRes->descripcion,
                    'codResultadoEsperado'  => $indicadorRes->codResultadoEsperado,
                    'cantidadMediosVerificacion'  => $indicadorRes->getCantidadMediosVerificacion()
                ];    
            
                
                foreach ($indicadorRes->getMediosVerificacion() as $medioVerif){
                    if($medioVerif->tieneArchivo())
                        $tieneArchivo = '1';
                    else 
                        $tieneArchivo= '0';
                    
                    $mediosVerificacion[] = [
                        'codMedioVerificacion'  => $medioVerif->codMedioVerificacion,
                        'codIndicadorResultado'  =>$medioVerif->codIndicadorResultado,
                        'descripcion'  => $medioVerif->descripcion,
                        'nombreGuardado'  => $medioVerif->nombreGuardado,
                        'nombreAparente'  => $medioVerif->nombreAparente,
                        'tieneArchivo'  => $tieneArchivo 

                    ];
                }
                
            
            }

            foreach ($res->getListaActividades() as $actividad){
                $actividadesResultados[]=[
                    'codActividad'  => $actividad->codActividad,
                    'descripcion'  => $actividad->descripcion,
                    'codResultadoEsperado'  => $actividad->codResultadoEsperado,
                    'cantidadIndicadores'  => $actividad->getCantidadIndicadores()
                ];
                foreach ($actividad->getListaIndicadores() as $indicador){
                    $indicadoresActividades[] = [
                        'codIndicadorActividad'  => $indicador->codIndicadorActividad,
                        'descripcion'  =>  $indicador->descripcion ,
                        'meta'  => $indicador->meta,
                        'unidadMedida' =>  $indicador->unidadMedida,
                        'codActividad'  => $indicador->codActividad,
                        'saldoPendiente'  => $indicador->saldoPendiente
                    ];
                }
            }
        }

        $vectorGeneral = [            
            'poblacionesBeneficiarias' => $poblacionesBeneficiarias,
            'objetivosEspecificos' => $objetivosEspecificos, 
            'indicadoresObjetivos' => $indicadoresObjetivos,
            'resultadosEsperados' => $resultadosEsperados, 
            'indicadoresResultados' => $indicadoresResultados, 
            'mediosVerificacion' => $mediosVerificacion, 
            'actividadesResultados' => $actividadesResultados, 
            'indicadoresActividades' => $indicadoresActividades,
            'listaPEIs' => $listaPEIs,
            'porcentajesObjetivosEstrategicos' => $porcentajesObjetivosEstrategicos
        ];
        //header('Content-Type: application/json');
        $json =   (json_encode($vectorGeneral ));
        //$json = str_replace('\\r',"\\",$json); //Hago esta conversion porque en los saltos de linea que php expresa como \r\n a JS le deben llegar como \\n
        //Debug::mensajeSimple($json);
         
        
        return   $json ;
    }



#endregion INVOCABLES





#region actividades e indicadores de act
    public function agregarEditarActividad(Request $request){
        try{
            db::beginTransaction();

            if($request->codActividad == "0"){// nuevo
                $actividad = new ActividadResultado();
                $mensaje = "agregada";
            }else{//editado
                $actividad = ActividadResultado::findOrFail($request->codActividad);
                $mensaje = "editada";
            }
            $actividad->descripcion = $request->descripcionNuevaActividad;
            $actividad->codResultadoEsperado = $request->ComboBoxResultadoEsperado;
            $actividad->save();

            db::commit();
            return RespuestaAPI::respuestaOk('Actividad "'.$actividad->descripcion.'" '.$mensaje.' exitosamente.');

        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('PROYECTO CONTROLLER AGREGAR EDITAR ACTIVIDAD',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaOk( Configuracion::getMensajeError($codErrorHistorial));

            
        }


    }

    public function eliminarActividad($codActividad){

        try{
            db::beginTransaction();

            $actividad = ActividadResultado::findOrFail($codActividad);
            $resultado = ResultadoEsperado::findOrFail($actividad->codResultadoEsperado);
            $codProyecto = $resultado->codProyecto;

            $descripcion = $actividad->descripcion;
            if($actividad->getCantidadIndicadores()!=0){
                return RespuestaAPI::respuestaError('Para eliminar la actividad "'.
                $descripcion.'" debe eliminar primero sus indicadores.');
            }
            

            $nombreAct = $actividad->descripcion;
            $actividad->delete();


            /* FALTA CODIGO PARA ELIMINAR LOS INDICADORES Y LAS METAS */
            db::commit();
            return  RespuestaAPI::respuestaOk('Se ha eliminado la actividad "'.$nombreAct.'" .');
        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('PROYECTO CONTROLLER Eliminar actividad',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codActividad);
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
        }

    }


    public function agregarEditarIndicadorActividad(Request $request){

        try{
            db::beginTransaction();


            if($request->codIndicadorActividad=="0"){//nuevo

                $indicador = new IndicadorActividad();
                $indicador->meta = 0;
                $indicador->saldoPendiente = 0;
                $mensaje = 'agregado exitosamente. Ya puede registrar sus metas por meses.';
            }else{
                $indicador = IndicadorActividad::findOrFail($request->codIndicadorActividad);
                $mensaje = 'editado exitosamente. ';
            }

            //$indicador->descripcion = $request->descripcionNuevoIndicadorAct;
            $indicador->unidadMedida = $request->unidadNuevoIndicador;
            $indicador->codActividad = $request->ComboBoxActividad;
            
            $indicador->save();

            db::commit();

            return RespuestaAPI::respuestaOk('Indicador "'.$indicador->unidadMedida.'" '.$mensaje);


        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('proyecto controller agregareditar indicador actividad' ,$th);

            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
             
        }

        

    }


    public function eliminarIndicadorActividad($codIndicadorActividad){

        try{
            db::beginTransaction();

            $indicador = IndicadorActividad::findOrFail($codIndicadorActividad );
            $codProyecto = $indicador->getResultadoEsperado()->codProyecto;
            $nombre = $indicador->unidadMedida;
            $indicador->delete();

            db::commit();
        
            return  RespuestaAPI::respuestaOk('Se ha eliminado el indicador "'.$nombre.'".');

        }catch(\Throwable $th){
            DB::rollBack();

            Debug::mensajeError('PROYECTO CONTROOLLER  Eliminar indicador actividad',$th);

            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codIndicadorActividad);
            
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
             
        }

    }
#endregion


#region obj especificos e indicadores
    
    public function agregarEditarObjetivoEspecifico(Request $request ){
        try{
            db::beginTransaction();
            
            if($request->codObjetivoEspecifico=="0"){//NUEVO REGISTRO
                $obj = new ObjetivoEspecifico();
                $obj->codProyecto = $request->codProyecto;
                $mensaje = "creado";

            }else{ //registro ya existente estamos editando
                $obj = ObjetivoEspecifico::findOrFail($request->codObjetivoEspecifico);
                $mensaje = "editado";
            }
            $obj->descripcion = $request->descripcionObjetivo;
            $obj->save();
            db::commit();

            return RespuestaAPI::respuestaOk('Se ha '.$mensaje.' el obj específico.');
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
             
        }


    }


    public function eliminarObjetivoEspecifico($codObjEspecifico){
        try{
            db::beginTransaction();
            $obj = ObjetivoEspecifico::findOrFail($codObjEspecifico);
            $codProyecto = $obj->codProyecto;
             
            $descripcion = $obj->descripcion;
            if($obj->getCantidadIndicadores()!=0){
                return redirect()->route('GestiónProyectos.editar',$codProyecto)
                    ->with('datos','ERROR: Para eliminar el objetivo específico "'.$descripcion.'" debe eliminar primero sus indicadores.');
            }            
            $obj->delete();
            db::commit();

            return RespuestaAPI::respuestaOk('Se ha eliminado el objetivo específico "'.$descripcion.'" ');
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codObjEspecifico);
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
        }


    }



    //agrega un indicador a un obj especifico
    public function agregarEditarIndicadorObjEsp(Request $request){
        
        try{
            db::beginTransaction();

            if($request->codIndicadorObjetivo=="0"){//nuevo
                $indicador = new IndicadorObjEspecifico();
                $mensaje = "añadido";
            }else{//editar
                $indicador = IndicadorObjEspecifico::findOrFail($request->codIndicadorObjetivo);
                $mensaje = "editado";
            }
            
            $indicador->descripcion = $request->descripcionNuevoIndicador;
            $indicador->codObjEspecifico = $request->ComboBoxObjetivoEspecifico;
            
            $indicador->save();
            db::commit();

            return  RespuestaAPI::respuestaOk('Se ha '.$mensaje.' el indicador.') ;
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaOk( Configuracion::getMensajeError($codErrorHistorial) );
             
        }


    }

    /* OBJ ESPECIFICO */
    public function eliminarIndicador($codIndicadorObj){
        try{
            db::beginTransaction();
            $indicador = IndicadorObjEspecifico::findOrFail($codIndicadorObj);
            $nombre = $indicador->descripcion;
            $objetivo = ObjetivoEspecifico::findOrFail($indicador->codObjEspecifico);
            $indicador->delete();
            db::commit();

            return  RespuestaAPI::respuestaOk('Se ha eliminado el indicador "'.$nombre.'".' );
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codIndicadorObj);
            return  RespuestaAPI::respuestaOk (Configuracion::getMensajeError($codErrorHistorial)) ;
             
        }

    }

#endregion







    
#region LUGARE EJECUCION

    public function agregarLugarEjecucion(Request $request ){
        
        try{
            db::beginTransaction();

            $cantidadZonas = $request->cantidadZonas;
            $listaZonas = "";
            for ($i=1; $i <= $cantidadZonas ; $i++){ 
                    
                $nuevo = new LugarEjecucion();
                $nuevo->codDistrito = $request->ComboBoxDistrito;
                $nuevo->codProyecto = $request->codProyecto;
                $nuevo->zona = $request->get('zonaLugarEjecucion'.$i);
                $listaZonas = $listaZonas.",".$nuevo->zona;
                $nuevo->save();

                Debug::mensajeSimple('REGISTRANDO ZONA EJECUCION ' . json_encode($nuevo) );
            }

            $listaZonas = trim($listaZonas,',');


            db::commit();
            
            return RespuestaAPI::respuestaOk('Se han agregado los lugares de ejecución '.$listaZonas);
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaOk(Configuracion::getMensajeError($codErrorHistorial));
             
        }




    }



    public function eliminarLugarEjecucion($codLugarEjecucion){

            
        try{
            db::beginTransaction();


            $lugarEjecucion = LugarEjecucion::findOrFail($codLugarEjecucion);
            
            $codProyecto = $lugarEjecucion->codProyecto;

            //primero validamos si este lugar de ejecucion no está siendo usado en algun proyecto
            $listaPersonas = PersonaNaturalPoblacion::where('codLugarEjecucion','=',$codLugarEjecucion)->get();
            $cantRes = count($listaPersonas);
            if($cantRes>0){
                $nombreYdni = $listaPersonas[0]->getNombreCompletoYDNI();
                return RespuestaAPI::respuestaError("ERROR: No se puede eliminar el lugar de ejecución pues está siendo usado en por la Persona Natural de nombre '$nombreYdni'");

            }
        
            $nombre = $lugarEjecucion->zona;
            $lugarEjecucion->delete();
            db::commit();
            return RespuestaAPI::respuestaOk('Zona "'.$nombre.'" eliminada exitosamente.');

        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codLugarEjecucion);
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
            
        }


        
    }
#endregion
    //descarga el REPORTE EXCEL de marco logico
    public function descargarMML($codProyecto){

        $proy = Proyecto::findOrFail($codProyecto);


        
        return view('Proyectos.ReporteMML',compact('codProyecto'));


    }

#region POBLACION BENEFICIARIA
    public function agregarPoblacionBeneficiaria(Request $request ){
        
        try{
            db::beginTransaction();
            if($request->codPoblacionBeneficiaria == "0"){//NUEVO
                $nuevo = new PoblacionBeneficiaria();
                $nuevo->codProyecto = $request->codProyecto;
                $mensaje = "creado";
            }else{ //EDITANDO
                $nuevo = PoblacionBeneficiaria::findOrFail($request->codPoblacionBeneficiaria);
                $mensaje = "editado";
            }

            $nuevo->descripcion = $request->descripcionPob;
            $nuevo->save();
            
            db::commit();

            return RespuestaAPI::respuestaOk('Se ha '.$mensaje.' la población beneficiaria "'.$request->descripcionPob.'".');
        }catch(\Throwable $th){
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));
            
        }



   }

   public function eliminarPoblacionBeneficiaria($codPoblacionBeneficiaria){


    try{
        db::beginTransaction();
        $pob = PoblacionBeneficiaria::findOrFail($codPoblacionBeneficiaria);
        if($pob->getCantidadTotalDePersonas() != 0){
            return RespuestaAPI::respuestaError("La población beneficiaria tiene personas naturales y jurídicas insertadas, debe borrarlas primero para poder eliminar todo el grupo.");
        }
        
        $codProyecto = $pob->codProyecto;
        $pob->delete();

        db::commit();

        return RespuestaAPI::respuestaOk('Población eliminada.');
    }catch(\Throwable $th){
        DB::rollBack();
        $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codPoblacionBeneficiaria);
        
        return RespuestaAPI::respuestaError(Configuracion::getMensajeError($codErrorHistorial));

        
    }

    

    }





    public function listarPersonasRegistradas(){
        $personasNaturales = PersonaNaturalPoblacion::All();
        $personasJuridicas = PersonaJuridicaPoblacion::All();

        return view('Proyectos.AdminSistema.ListarPersonas',compact('personasNaturales','personasJuridicas'));
    }
#endregion








    public function listarProvinciasDeDepartamento($codDepartamento){
        return Provincia::where('codDepartamento','=',$codDepartamento)->get();

    }
    public function listarDistritosDeProvincia($codProvincia){
        return Distrito::where('codProvincia','=',$codProvincia)->get();

    }


    public function verMetas(){
        return view('Proyectos.Gerente.verMetas');
    }
    
    public function probandoMeses(){
        $meta = MetaEjecutada::findOrFail(4);
        return json_encode($meta)."  ¿?  ".$meta->sePuedeEditar();
    }

    public function registrarMetasEjecutadas($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        return view('Proyectos.RegistroEjecucionMetas',compact('proyecto'));

    }



    public function exportarMetasEjecutadas($codProyecto){
        $proyecto=Proyecto::findOrFail($codProyecto);
        
        return view('Proyectos.ExportarExcelEjecucionMetas',compact('proyecto'));
    }






#region CRUD archivos del proyecto
    public function añadirArchivos(Request $request){
        
        try {
            DB::beginTransaction();      
            
            $proyecto = Proyecto::findOrFail($request->codProyecto);
            
            Debug::mensajeSimple($request->nombresArchivos);
            $nombresArchivos = explode(', ',$request->nombresArchivos);
            Debug::mensajeSimple('a:'.($nombresArchivos[0]));
            
            //return $request->nombresArchivos." / ".json_encode($nombresArchivos);
            $j=0;
            
            //Debug::mensajeSimple("o yara/".$request->tipoIngresoArchivos);
            if($request->tipoIngresoArchivos=="1")
            {//AÑADIR
                
            }else{//SOBRESRIBIR
                $proyecto->eliminarArchivos();  //A
            }

            $indiceNumerado = $proyecto->getCantidadArchivos() +1;


            
            foreach ($request->file('filenames') as $archivo)
            {   
                $nombreArchivoGuardado = $proyecto->getNombreGuardadoNuevoArchivo($indiceNumerado + $j);
                Debug::mensajeSimple('el nombre de guardado del archivo es:'.$nombreArchivoGuardado.' y el aparente:'.$nombresArchivos[$j]);

                $fileget = \File::get( $archivo );
                
                $archivoProyecto = new ArchivoProyecto();
                $archivoProyecto->codProyecto = $proyecto->codProyecto;
                $archivoProyecto->nombreDeGuardado = $nombreArchivoGuardado;
                $archivoProyecto->nombreAparente = $nombresArchivos[$j];
                $archivoProyecto->codTipoArchivoProyecto = $request->codTipoArchivoProyecto;
                $archivoProyecto->fechaHoraSubida = new DateTime();
                $archivoProyecto->save();


                Storage::disk('proyectos')->put($nombreArchivoGuardado,$fileget );
                $j++;
            }

            DB::commit();
            return redirect()->route('GestionProyectos.RedirigirAProyectoSegunActor',$proyecto->codProyecto)
                ->with('datos','Archivos añadidos exitosamente');
        } catch (\Throwable $th) {
            Debug::mensajeError('PROYECTO CONTROLLER AÑADIR ARCHIVOS ',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }

        
    }


    public function eliminarArchivo($codArchivo){
        try {
            db::beginTransaction();
            $archivo = ArchivoProyecto::findOrFail($codArchivo);
            $nombreArchivEliminado = $archivo->nombreAparente;
            $proyecto = Proyecto::findOrFail($archivo->codProyecto);

            $archivo->eliminarArchivo();
            DB::commit();
        
            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)
                ->with('datos','Archivo "'.$nombreArchivEliminado.'" eliminado exitosamente.');
        } catch (\Throwable $th) {
            Debug::mensajeError(' PROYECTO CONTROLLER Eliminar archivo' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codArchivo);
            return redirect()->route('GestiónProyectos.editar',$proyecto->codProyecto)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }

    public function descargarArchivo($codArchivoProyecto){
        $archivo = ArchivoProyecto::findOrFail($codArchivoProyecto);  
        return Storage::download("/proyectos/".$archivo->nombreDeGuardado,$archivo->nombreAparente);
    }
#endregion CRUD archivos del proyecto

    //PARA EXCEL
    function ExportarModeloMarcoLogico($codProyecto){
        $proyecto = Proyecto::findOrFail($codProyecto);
        $listaObjetivosEspecificos=ObjetivoEspecifico::where('codProyecto','=',$proyecto->codProyecto)->get();
        $listarResultadosEsperados=ResultadoEsperado::where('codProyecto','=',$proyecto->codProyecto)->get();
        return view('Proyectos.ExportarExcelMarcoLogico', compact('proyecto','listaObjetivosEspecificos','listarResultadosEsperados'));
    }
}
