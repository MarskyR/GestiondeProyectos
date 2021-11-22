<?php

use App\ArchivoOrdenCompra;
use App\ArchivoProyecto;
use App\ArchivoRendicion;
use App\ArchivoReposicion;
use App\ArchivoReqAdmin;
use App\ArchivoReqEmp;
use App\ArchivoSolicitud;
use App\BackendValidator;
use App\Configuracion;
use App\Debug;
use App\DetalleSolicitudFondos;
use App\Empleado;
use App\EstadoSolicitudFondos;
use App\FakerCedepas;
use App\Fecha;
use App\MaracsoftBot;
use App\Mes;
use App\MetaEjecutada;
use App\OperacionDocumento;
use App\PersonaNaturalPoblacion;
use App\Proyecto;
use App\RendicionGastos;
use App\RequerimientoBS;
use App\SolicitudFondos;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Faker\Factory as Faker;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

Route::get('/PaginaEnMantenimiento/','UserController@paginaEnMantenimiento')->name('mantenimiento');

Route::group(['middleware'=>"Mantenimiento"],function()
{

    Route::get('/login', 'UserController@verLogin')->name('user.verLogin'); //para desplegar la vista del Login
    Route::post('/ingresar', 'UserController@logearse')->name('user.logearse');
    Route::get('/cerrarSesion','UserController@cerrarSesion')->name('user.cerrarSesion');

    Route::get('/redireccionamiento',function (){
        return view('MensajeRedireccion');

    });

    Route::get('/', 'UserController@home')->name('user.home');

    
    Route::get('/encriptarContraseñas', function(){

        // return redirect()->route('error')->with('datos','Parece que te has perdido...');

        //$contraseñas = "40556946;46636006;47541289;26682689;41943357;43485279;42090409;44847934;26682687;17914644;70355561;70585629;44685699;19327774;40360154;45740336;15738099;19330869;74240802;70386230;42927000;42305800;15766143;45540460;45372425;03120627;45576187;17877014;02897932;44155217;18175358;40068481;18126610;43162714;40392458;40242073;40994213;42122048;44896824;46352412;43953715;99999999;99999999";
        
        //$contraseñas = '24462108;47449263;09962981;44284513;15738099;09858655;07238664;45434776;73636764;74324466;75513292';
        $contraseñas="45540460";
        $vectorContraseñas = explode(';',$contraseñas);
        
        $vectorContraseñasEncriptadas = [];
        foreach ($vectorContraseñas as $item){
            array_push($vectorContraseñasEncriptadas,Hash::make($item));   
        }

        $listaEncriptadasSeparadasComas= implode(';',$vectorContraseñasEncriptadas);


        return $listaEncriptadasSeparadasComas;

    });

    // RUTA -> CONTROLADOR -> VISTA
    // /home

    
    Route::get('/probandoProy','ProyectoController@probandoMeses');

    /* 
        eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZjA0ZjI4NDhkMTliZTg1NTI0Yjk2N2EyYjM2NmY5MmZiMDQ3Nzc5NDlmMzhkMzc3ZTYwZDFhMThmNjlhMmE0NmNjMWFmMjY1ZTUxZjcxOGYiLCJpYXQiOjE2MzQzMjExMTMuNTkzMzUsIm5iZiI6MTYzNDMyMTExMy41OTMzNTQsImV4cCI6MTY2NTg1NzExMy41NTQxMDcsInN1YiI6IjAiLCJzY29wZXMiOltdfQ.RQViipIGs-j-vAwqXnxFLjpKAb_8br-zS8vWhfXXRzdxAM7AxZyGsqE1VlqlcMAn-BHjTNdJSaYe2XPjCwRloRSyws2mr7e-GidQHYn3bv1IPdcFhefOfHymCFZDW9_bLgKW4YaQWY6DQSw9kitjM_MVWw0XOBpc1syWZDysQHJ-pWdZMioz-kaR52NpQvohGrrygLQ5RDIdkS17dLbODW5vkCtQV0WXQ7HuY7U7qqGxSUreUUmOkLY5BQOSzswJ1BrqCzT9c-n6BtiCFhYlV014_vQGM-foFrq0_Es5WXGZEOy2X-wl3e-SK_JFTS12h_IchKWSuqAHPq9YIzEZ3hb-g88FOr0Du6v03gfCrg4FA1Og02vepItS4R4-Hafj76HgOid3KE7ZqAD98KyrMuAttT6ijINDqmWRgSJp9oQFzmpVUfl6mb-jonpr3wPYmuuE8F9PuWpT2dqVMj4zqVGopzym6DPF4SZQjgBa6An4_cLYB0VUCspeFaxmWKrgPTti3f5F3pmCVlE6Ry1FvwXxERwMeEZTw6Q7YibFL5blOAF8TypvSJzSx4zZVlS0dSQBXeURpsE2M-Q-oFARMXrfUc7owsUt4pXbyr8cq_U1xhQ8FhEbFukzkCBajIaS0rxJ_9mWOjfYS8fWAafGzgoNX9BfMlBGmDJx-AZeIwE
    */

    Route::get('/probandoCosas',function(){
        $esta = EstadoSolicitudFondos::findOrFail(1);
        return $esta->getID();
    });

    Route::get('/getEmpleadoLogeado',function(){
        return Empleado::getEmpleadoLogeado();

    });

    Route::get('/Error',function(){
        
        $msj = session('datos');
        $datos='';
        if($msj!='')
            $datos = $msj;

        session(['datos' => '']);
        return view('ERROR',compact('datos'));

    })->name('error');

    
    /* RUTAS SERVICIOS */
    Route::get('/listarDetallesDeSolicitud/{id}','SolicitudFondosController@listarDetalles');
    Route::get('/listarDetallesDeRendicion/{id}','RendicionGastosController@listarDetalles');
    Route::get('/listarDetallesDeReposicion/{id}','ReposicionGastosController@listarDetalles');
    Route::get('/listarDetallesDeRequerimiento/{id}','RequerimientoBSController@listarDetalles');
    Route::get('/listarDetallesDeOrdenCompra/{id}','OrdenCompraController@listarDetalles');

    Route::get('/solicitudFondos/getNumeracionActual/','SolicitudFondosController@getNumeracionLibre');
    Route::get('/rendicionGastos/getNumeracionActual/','RendicionGastosController@getNumeracionLibre');
    Route::get('/reposicionGastos/getNumeracionActual/','ReposicionGastosController@getNumeracionLibre');
    Route::get('/obtenerCodigoPresupuestalDeProyecto/{id}','ProyectoController@getCodigoPresupuestal');

    Route::get('/listarProvinciasDeDepartamento/{id}','ProyectoController@listarProvinciasDeDepartamento');
    Route::get('/listarDistritosDeProvincia/{id}','ProyectoController@listarDistritosDeProvincia');

    Route::get('/listarObjetivosDePEI/{id}','PlanEstrategicoInstitucionalController@listarObjetivos');

    Route::get('/listarPersonasDePoblacion/{codPob}/Naturales','PersonaPoblacionController@listarPersonasNaturales');
    Route::get('/listarPersonasDePoblacion/{codPob}/Juridicas','PersonaPoblacionController@listarPersonasJuridicas');

    Route::get('/BaseDatosPoblacion/PersonasNaturales/consultarPorDni/{dni}','PersonaPoblacionController@consultarPoblacionPorDni');
    

    Route::get('/GestionProyectos/MetaEjecutada/{codMeta}/VerArchivos','IndicadorActividadController@vistaArchivosDeMeta');
    
    Route::get('/ConsultarAPISunat/ruc/{ruc}','PersonaPoblacionController@ConsultarAPISunatRUC');

    Route::get('/Sede/cambiarAdministrador/{cadena}','SedeController@cambiarAdministrador');

    Route::get('/GestiónUsuarios/asignarProyectoAContador/{cadena}','EmpleadoController@asignarProyectoAContador');
    
    Route::get('/GestiónUsuarios/asignarContadorATodosProyectos/{codEmpleadoContador}','EmpleadoController@asignarContadorATodosProyectos');
    Route::get('/GestiónUsuarios/quitarContadorATodosProyectos/{codEmpleadoContador}','EmpleadoController@quitarContadorATodosProyectos');
                
    

    /* ESTE MIDDLEWARE VALIDA SI ES QUE ESTÁS LOGEADO, SI NO, TE MANDA AL LOGIN */
    Route::group(['middleware'=>"ValidarSesion"],function()
    {

        
        
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ----------------------------------------------        MODULO SOLICITUD DE FONDOS ---------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */



        /* AFUERA DEL MIDDLEWARE(no requieren validacion o hacerlas sería demasiado complejo xd) */
        Route::get('/SolicitudFondos/MASTERINDEX','SolicitudFondosController@listarSolicitudes')
            ->name('solicitudFondos.ListarSolicitudes');


        Route::post('/SolicitudFondos/Observar','SolicitudFondosController@observar')
            ->name('solicitudFondos.observar');
        Route::get('/SolicitudFondos/Rechazar/{id}','SolicitudFondosController@rechazar')
            ->name('solicitudFondos.rechazar');
        
        Route::get('/SolicitudFondos/descargar/{id}','SolicitudFondosController@descargarPDF')
            ->name('solicitudFondos.descargarPDF');
        Route::get('/SolicitudFondos/verPDF/{id}','SolicitudFondosController@verPDF')
            ->name('solicitudFondos.verPDF');

        Route::get('/SolicitudFondos/descargarArchivo/{codArc}','SolicitudFondosController@descargarArchivo')
            ->name('SolicitudFondos.descargarArchivo');
        
        Route::get('/SolicitudFondos/eliminarArchivo/{codArc}','SolicitudFondosController@eliminarArchivo')
            ->name('SolicitudFondos.eliminarArchivo');
        

        /* EMPLEADO Cualquier user logeado puede hacer, no es necesario validar*/
        Route::get('/SolicitudFondos/Empleado/Crear','SolicitudFondosController@create')
            ->name('SolicitudFondos.Empleado.Create');

        Route::get('/SolicitudFondos/Empleado/editar/{id}','SolicitudFondosController@edit')
            ->name('SolicitudFondos.Empleado.Edit');


        Route::get('/SolicitudFondos/Empleado/delete/{id}','SolicitudFondosController@cancelar')
            ->name('SolicitudFondos.Empleado.cancelar');

        Route::get('/SolicitudFondos/Empleado/listar/','SolicitudFondosController@listarSolicitudesDeEmpleado')
            ->name('SolicitudFondos.Empleado.Listar');

        Route::get('/SolicitudFondos/{id}/Empleado/ver/','SolicitudFondosController@ver')
            ->name('SolicitudFondos.Empleado.Ver');
            
        Route::post('/SolicitudFondos/Empleado/guardar', 'SolicitudFondosController@store')
            ->name('SolicitudFondos.Empleado.Guardar');
        
        
        Route::get('/SolicitudFondos/Empleado/Rendir/{id}','SolicitudFondosController@rendir')
            ->name('SolicitudFondos.Empleado.Rendir');
        
        Route::post('/SolicitudFondos/{id}/Empleado/update/','SolicitudFondosController@update')
            ->name('SolicitudFondos.Empleado.update');



        /* GERENTE */


        Route::group(['middleware'=>"ValidarSesionGerente"],function()
        {

            Route::get('/SolicitudFondos/{id}/Gerente/Revisar/','SolicitudFondosController@revisar')
                ->name('SolicitudFondos.Gerente.Revisar');

            Route::get('/SolicitudFondos/Gerente/listar','SolicitudFondosController@listarSolicitudesParaGerente')
                ->name('SolicitudFondos.Gerente.Listar');

            Route::Post('/SolicitudFondos/Gerente/Aprobar/','SolicitudFondosController@aprobar')
                ->name('SolicitudFondos.Gerente.Aprobar');
        

        });

        Route::group(['middleware'=>"ValidarSesionAdministracion"],function(){

            /* ADMINSITRACION */
            Route::get('/SolicitudFondos/Administración/listar','SolicitudFondosController@listarSolicitudesParaJefe')
                ->name('SolicitudFondos.Administracion.Listar');

            Route::get('/SolicitudFondos/{id}/Administracion/vistaAbonar/','SolicitudFondosController@vistaAbonar')
                ->name('SolicitudFondos.Administracion.verAbonar');

            Route::post('/SolicitudFondos/Administracion/Abonar/','SolicitudFondosController@abonar')
                ->name('SolicitudFondos.Administracion.Abonar');

        });

        Route::group(['middleware'=>"ValidarSesionContador"],function()
        {

            /* CONTADOR */

            Route::get('/SolicitudFondos/Contador/listar','SolicitudFondosController@listarSolicitudesParaContador')
                ->name('SolicitudFondos.Contador.Listar');

            Route::get('/SolicitudFondos/{id}/Contador/verContabilizar/','SolicitudFondosController@verContabilizar')
                ->name('SolicitudFondos.Contador.verContabilizar');


            Route::get('/SolicitudFondos/Contador/Contabilizar/{id}','SolicitudFondosController@contabilizar')
                ->name('SolicitudFondos.Contador.Contabilizar');

        });
        
        


        
        




        
        
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* -----------------------------------------------------MODULO RENDICIONES--------- ---------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */
        /* ------------------------------------------------------------------------------------------------------------- */


        Route::post('/reportes/ver', 'RendicionGastosController@reportes')
        ->name('rendicionGastos.reportes');


        Route::get('/reportes/descargar/{str}', 'RendicionGastosController@descargarReportes')
            ->name('rendicionGastos.descargarReportes');


        Route::get('/rendicion/descargarCDP/{cadena}','RendicionGastosController@descargarCDP')
        ->name('rendiciones.descargarCDP');

        //se consume desde JS
        Route::get('/RendicionGastos/eliminarArchivo/{codArchivoRend}','RendicionGastosController@eliminarArchivo');

        


        Route::get('/RendicionGastos//descargarPDF/{id}','RendicionGastosController@descargarPDF')
        ->name('rendicionGastos.descargarPDF');

        Route::get('/RendicionGastos/verPDF/{id}','RendicionGastosController@verPDF')
        ->name('rendicionGastos.verPDF');


        Route::get('/RendicionGastos/{id}/rechazar','RendicionGastosController@rechazar')
        ->name('RendicionGastos.Gerente.Rechazar');

        Route::post('/rendiciones/observar','RendicionGastosController@observar')
        ->name('RendicionGastos.Observar');
        /* EMPLEADO */


        
        Route::post('/RendicionGastos/Empleado/guardar', 'RendicionGastosController@store')
        ->name('RendicionGastos.Empleado.Store');

        Route::get('/RendicionGastos/{id}/Empleado/ver/', 'RendicionGastosController@ver')
        ->name('RendicionGastos.Empleado.Ver');


        Route::get('RendicionGastos/{id}/Empleado/editar','RendicionGastosController@editar')
        ->name('RendicionGastos.Empleado.Editar');

        Route::Post('/RendicionGastos/Empleado/update/','RendicionGastosController@update')
        ->name('RendicionGastos.Empleado.Update');
        Route::get('/RendicionGastos/Empleado/Listar/','RendicionGastosController@listarEmpleado')
        ->name('RendicionGastos.Empleado.Listar');
        

        Route::get('RendicionGastos/Empleado/verMisGastos','RendicionGastosController@listarMisGastosRendicion')
            ->name('RendicionGastos.Empleado.verMisGastos');


        Route::get('/RendicionGastos/{codDetalle}/Empleado/marcarComoVisto','RendicionGastosController@marcarDetalleComoVisto')
            ->name('RendicionGastos.Empleado.marcarComoVisto');


        Route::group(['middleware'=>"ValidarSesionGerente"],function()
        {
        
        /* GERENTE */

            Route::get('/RendicionGastos/{id}/Gerente/ver', 'RendicionGastosController@verGerente')
                ->name('RendicionGastos.Gerente.Ver');

            Route::get('/RendicionGastos/{id}/Gerente/revisar','RendicionGastosController@revisar')
                ->name('RendicionGastos.Gerente.Revisar');

            Route::Post('/RendicionGastos/Gerente/aprobar','RendicionGastosController@aprobar')
                ->name('RendicionGastos.Gerente.Aprobar');

            Route::get('/RendicionGastos/Gerente/listar/','RendicionGastosController@listarDelGerente')
                ->name('RendicionGastos.Gerente.Listar');
            
        });
        Route::group(['middleware'=>"ValidarSesionAdministracion"],function()
        {
            /* ADMINISTRACION */

            Route::get('/RendicionGastos/Administracion/listar/','RendicionGastosController@listarJefeAdmin')
                ->name('RendicionGastos.Administracion.Listar');

            Route::get('/RendicionGastos/{id}/Administracion/ver', 'RendicionGastosController@verAdmin')
                ->name('RendicionGastos.Administracion.Ver');

        });

        Route::group(['middleware'=>"ValidarSesionContador"],function()
        {
            /* CONTADOR */

            Route::get('/RendicionGastos/{id}/Contador/verContabilizar/','RendicionGastosController@verContabilizar')
                    ->name('RendicionGastos.Contador.verContabilizar');   

            Route::get('/rendicion/contabilizar/{cad}','RendicionGastosController@contabilizar')
                ->name('RendicionGastos.Contador.Contabilizar');   

            Route::get('/RendicionGastos/Contador/listar/','RendicionGastosController@listarContador')
                ->name('RendicionGastos.Contador.Listar');

        });

        //RUTA MAESTAR QUE REDIRIJE A LOS LISTADOS DE RENDICIONES DE LOS ACTORES EMP GER Y J.A
        Route::get('/RendicionGastos/MAESTRA/listar','RendicionGastosController@listarRendiciones')
        ->name('RendicionGastos.ListarRendiciones');


        

    


        /**PUEDE HACER EL EMPLEADO */

        Route::get('/GestiónUsuarios/misDatos','EmpleadoController@verMisDatos')->name('GestionUsuarios.verMisDatos');
        Route::get('/GestiónUsuarios/cambiarContraseña','EmpleadoController@cambiarContraseña')->name('GestionUsuarios.cambiarContraseña');
        
        
        
        Route::post('/GestiónUsuarios/updateContrasena','EmpleadoController@guardarContrasena')->name('GestionUsuarios.updateContrasena');
        Route::post('/GestiónUsuarios/updateDPersonales','EmpleadoController@guardarDPersonales')->name('GestionUsuarios.updateDPersonales');

        /* FUNCIONES PROPIAS DEL ADMINISTRADOR DEL SISTEMA */
        Route::group(['middleware'=>"ValidarSesionAdminSistema"],function()
        {

            /* Modulo de modificaciones de la bd */

            
            Route::get('/archivos/migrarAutf8','OperacionesController@migrarAUTF8Archivos');
            Route::get('/arreglarErrorContadorGerente','OperacionesController@arreglarErrorContadorGerente');
            
            Route::get('/TipoOperaciones/Listar','TipoOperacionController@listar')->name('TipoOperacion.Listar');

            Route::get('/Operaciones/Listar','OperacionesController@ListarOperaciones')->name('Operaciones.Listar');

            /* -----------------------------------------MODULO HISTORIAL DE ERRORES-------------------------- */

            //listar errores
            Route::get('/HistorialErrores','ErrorHistorialController@listarErrores')->name('HistorialErrores.Listar');
            Route::get('/HistorialErrores/{codError}/cambiarEstadoError','ErrorHistorialController@cambiarEstadoError')->name('HistorialErrores.cambiarEstadoError');
            Route::get('/HistorialErrores/ver/{id}','ErrorHistorialController@ver')->name('HistorialErrores.ver');
            Route::post('/HistorialErrores/guardarRazonSolucionError','ErrorHistorialController@guardarRazonSolucionError')->name('HistorialErrores.guardarRazonSolucionError');

            
            /* -----------------------------------------MODULO HISTORIAL DE LOGEOS-------------------------- */

            //listar logeos
            Route::get('/HistorialLogeos','LogeoHistorialController@listarLogeos')->name('HistorialLogeos.Listar');
                

            /* ----------------------------------------------        MODULO GESTIÓN DE USUARIOS ---------------------------- */
            Route::get('/GestiónUsuarios/listar','EmpleadoController@listarEmpleados')->name('GestionUsuarios.Listar');
            
            Route::get('/GestiónUsuarios/crear','EmpleadoController@crearEmpleado')->name('GestionUsuarios.create');
            
            Route::post('/GestiónUsuarios/save','EmpleadoController@guardarCrearEmpleado')->name('GestionUsuarios.store');
            
            Route::get('/GestiónUsuarios/verProyectosContador/{codEmpleado}','EmpleadoController@verProyectosContador')->name('GestionUsuarios.verProyectosContador');
            

            Route::get('/GestiónUsuarios/{id}/editarUsuario','EmpleadoController@editarUsuario')->name('GestionUsuarios.editUsuario');
            Route::get('/GestiónUsuarios/{id}/editarEmpleado','EmpleadoController@editarEmpleado')->name('GestionUsuarios.editEmpleado');
            Route::post('/GestiónUsuarios/updateUsuario','EmpleadoController@guardarEditarUsuario')->name('GestionUsuarios.updateUsuario');
            Route::post('/GestiónUsuarios/updateEmpleado','EmpleadoController@guardarEditarEmpleado')->name('GestionUsuarios.updateEmpleado');
        
            Route::get('/GestiónUsuarios/{id}/cesar','EmpleadoController@cesarEmpleado')->name('GestionUsuarios.cesar');
        

            Route::get('/GestionUsuarios/cambiarTodosLosNombresAFormatoBonito','EmpleadoController@cambiarNombresEmpleadosAFormatoBonito')->name('GestionUsuarios.cambiarTodosLosNombresAFormatoBonito');
            
            
            Route::get('/GestiónUsuarios/ActualizarSedeContador/{cadena}','EmpleadoController@cambiarSedeAContador')->name('GestionUsuarios.cambiarSedeAContador');
            
            




            /* ----------------------------------------------        MODULO PUESTOS           ------------------------------------------ */
            Route::get('/GestiónPuestos/listar','PuestoController@listarPuestos')->name('GestiónPuestos.Listar');
            
            Route::get('/GestiónPuestos/crear','PuestoController@crearPuesto')->name('GestiónPuestos.create');
            Route::post('/GestiónPuestos/save','PuestoController@guardarCrearPuesto')->name('GestiónPuestos.store');
        
            Route::get('/GestiónPuestos/{id}/editar','PuestoController@editarPuesto')->name('GestiónPuestos.edit');
            Route::post('/GestiónPuestos/update','PuestoController@guardarEditarPuesto')->name('GestiónPuestos.update');
        
            Route::get('/GestiónPuestos/{id}/eliminar','PuestoController@eliminarPuesto')->name('GestiónPuestos.delete');



            /* ----------------------------------------------        MODULO TIPO DE PERSONA JURIDICA   ------------------------------------------ */
            Route::get('/GestiónTipoPersonaJuridica/listar','TipoPersonaJuridicaController@listarTipos')->name('GestiónTipoPersonaJuridica.Listar');
            
            Route::get('/GestiónTipoPersonaJuridica/crear','TipoPersonaJuridicaController@crearTipo')->name('GestiónTipoPersonaJuridica.create');
            Route::post('/GestiónTipoPersonaJuridica/save','TipoPersonaJuridicaController@guardarCrearTipo')->name('GestiónTipoPersonaJuridica.store');
        
            Route::get('/GestiónTipoPersonaJuridica/{id}/editar','TipoPersonaJuridicaController@editarTipo')->name('GestiónTipoPersonaJuridica.edit');
            Route::post('/GestiónTipoPersonaJuridica/update','TipoPersonaJuridicaController@guardarEditarTipo')->name('GestiónTipoPersonaJuridica.update');
        
            Route::get('/GestiónTipoPersonaJuridica/{id}/eliminar','TipoPersonaJuridicaController@eliminarTipo')->name('GestiónTipoPersonaJuridica.delete');
            
            

            /* ---------------------------------------------- MODULO UNIDAD DE MEDIDA -------------------------------------------- */
            Route::get('/GestiónUnidadMedida/listar','UnidadMedidaController@listarUnidades')->name('GestiónUnidadMedida.listar');

            Route::get('/GestiónUnidadMedida/crear','UnidadMedidaController@crearUnidad')->name('GestiónUnidadMedida.crear');
            Route::post('/GestiónUnidadMedida/store','UnidadMedidaController@store')->name('GestiónUnidadMedida.store');

            Route::get('/GestiónUnidadMedida/{id}/editar','UnidadMedidaController@editarUnidad')->name('GestiónUnidadMedida.editar');
            Route::post('/GestiónUnidadMedida/update','UnidadMedidaController@update')->name('GestiónUnidadMedida.update');

            Route::get('/GestiónUnidadMedida/{id}/eliminar','UnidadMedidaController@delete')->name('GestiónUnidadMedida.eliminar');


            /* CRUD FINANCIERAS */
            Route::get('/Financieras/listar','EntidadFinancieraController@listar')->name('EntidadFinanciera.listar');
            Route::get('/Financieras/crear','EntidadFinancieraController@crear')->name('EntidadFinanciera.crear');
            Route::get('/Financieras/editar/{id}','EntidadFinancieraController@editar')->name('EntidadFinanciera.editar');
            Route::post('/Financieras/actualizar','EntidadFinancieraController@actualizar')->name('EntidadFinanciera.actualizar');
            

            Route::post('/Financieras/guardar','EntidadFinancieraController@guardar')->name('EntidadFinanciera.guardar');
            Route::get('/Financieras/eliminar/{id}','EntidadFinancieraController@eliminar')->name('EntidadFinanciera.eliminar');
            
            /* CRUD TIPO FINANCIAMIENTO */
            Route::get('/TipoFinanciamiento/listar','TipoFinanciamientoController@listar')->name('TipoFinanciamiento.listar');
            Route::get('/TipoFinanciamiento/crear','TipoFinanciamientoController@crear')->name('TipoFinanciamiento.crear');
            Route::get ('/TipoFinanciamiento/editar/{id}','TipoFinanciamientoController@editar')->name('TipoFinanciamiento.editar');
            Route::post('/TipoFinanciamiento/actualizar','TipoFinanciamientoController@actualizar')->name('TipoFinanciamiento.actualizar');
            

            Route::post('/TipoFinanciamiento/guardar','TipoFinanciamientoController@guardar')->name('TipoFinanciamiento.guardar');
            Route::get('/TipoFinanciamiento/eliminar/{id}','TipoFinanciamientoController@eliminar')->name('TipoFinanciamiento.eliminar');
            
            /* CRUD OBJETIVOS ESTRATEGICOS */
             
            Route::post('/ObjetivoEstrategico/agregarEditarObjetivoEstrategico','ObjetivoEstrategicoController@agregarEditarObjetivoEstrategico')
                ->name('ObjetivoEstrategico.agregarEditarObjetivoEstrategico');
             
            Route::get('/ObjetivoEstrategico/eliminar/{id}','ObjetivoEstrategicoController@eliminar')->name('ObjetivoEstrategico.eliminar');
            
             
            /* CRUD PLANES ESTRATEGICOS */
            Route::get('/PlanEstrategico/listar','PlanEstrategicoInstitucionalController@listar')->name('PlanEstrategico.listar');
            
            Route::get('/PlanEstrategico/crear','PlanEstrategicoInstitucionalController@crear')->name('PlanEstrategico.crear');
            Route::get ('/PlanEstrategico/editar/{id}','PlanEstrategicoInstitucionalController@editar')->name('PlanEstrategico.editar');

             
            Route::get('/PlanEstrategico/eliminar/{id}','PlanEstrategicoInstitucionalController@eliminar')->name('PlanEstrategico.eliminar');
            
            Route::post('/PlanEstrategico/actualizar','PlanEstrategicoInstitucionalController@actualizar')->name('PlanEstrategico.actualizar');
            Route::post('/PlanEstrategico/guardar','PlanEstrategicoInstitucionalController@guardar')->name('PlanEstrategico.guardar');
            
            Route::get('/PlanEstrategico/generarRelacionesProyectosYObjetivosEstrategicos','PlanEstrategicoInstitucionalController@generarRelacionesProyectosYObjetivosEstrategicos')
                ->name('PlanEstrategico.generarRelacionesProyectosYObjetivosEstrategicos');

            /* CRUD SEDE */

            Route::get('/Sede/listarSedes','SedeController@listarSedes')->name('Sede.ListarSedes');

            Route::post('/Sede/GuardarEditar','SedeController@GuardarEditar')->name('Sede.GuardarEditar');
            
            
            /* CRUD Actividad Principal (de las personas naturales) */

            Route::get('/ActividadPrincipal/listar','ActividadPrincipalController@listar')->name('ActividadPrincipal.Listar');
             
             
            Route::post('/ActividadPrincipal/guardarEditarActividad','ActividadPrincipalController@guardarEditarActividad')->name('ActividadPrincipal.guardarEditarActividad');
             
            
            Route::get('/ActividadPrincipal/{id}/eliminar','ActividadPrincipalController@eliminar')->name('ActividadPrincipal.eliminar');
            
            
            /* OBJETIVO MILENIO */

            Route::get('/ObjetivosMilenio/Listar','ObjetivoMilenioController@listar')->name('ObjetivoMilenio.listar');

            Route::post('/ObjetivosMilenio/agregarEditarObjetivo','ObjetivoMilenioController@agregarEditarObjetivo')->name('ObjetivoMilenio.agregarEditarObjetivo');

            Route::get('/ObjetivosMilenio/eliminar/{cod}','ObjetivoMilenioController@eliminar')->name('ObjetivoMilenio.eliminar');

            
            Route::get('/ObjetivosMilenio/generarRelacionesProyectosYObjMilenio/','ObjetivoMilenioController@generarRelacionesProyectosYObjMilenio')->name('ObjetivoMilenio.generarRelacionesProyectosYObjMilenio');

        });


    
        
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------- MODULO PROYECTOS -------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */
        /* ---------------------------------------------------------------------------------------------------------------- */

        Route::group(['middleware'=>"ValidarSesionGerenteOUGE"],function()
        {   

            Route::get('/GestionProyectos/RedirigirAProyectoSegunActor/{codProy}','ProyectoController@RedirigirAProyectoSegunActor')
                ->name('GestionProyectos.RedirigirAProyectoSegunActor');
            
            Route::get('/GestionProyectos/{codProyecto}/RedirigirAVistaMetas/','ProyectoController@RedirigirAVistaMetas')
                ->name('GestionProyectos.RedirigirAVistaMetas');
            // redirije a GestionProyectos.Gerente.RegistrarMetasEjecutadas y  GestionProyectos.UGE.RegistrarMetasEjecutadas

            /* INVOCABLES Se consumen desde JS */
            Route::get('/GestionProyectos/{codProyecto}/invocarObjetivosEspecificos','ProyectoController@invocarObjetivosEspecificos')->name('GestionProyectos.InvocarDesplegable.ObjetivosEspecificos');
            Route::get('/GestionProyectos/{codProyecto}/invocarResultadosEsperadosActividades','ProyectoController@invocarResultadosEsperadosActividades')->name('GestionProyectos.InvocarDesplegable.ResultadosEsperadosActividades');
            Route::get('/GestionProyectos/{codProyecto}/invocarResultadosEsperadosIndicadores','ProyectoController@invocarResultadosEsperadosIndicadores')->name('GestionProyectos.InvocarDesplegable.InvocarResultadosEsperadosIndicadores');
            
            Route::get('/GestionProyectos/{codProyecto}/invocarPoblacionesBeneficiarias','ProyectoController@invocarPoblacionesBeneficiarias')->name('GestionProyectos.InvocarDesplegable.InvocarPoblacionesBeneficiarias');
            
            Route::get('/GestionProyectos/{codProyecto}/invocarTablaObjEstr','ProyectoController@invocarTablaObjEstr')->name('GestionProyectos.InvocarDesplegable.InvocarTablaObjEstr');
            Route::get('/GestionProyectos/{codProyecto}/invocarLugaresEjecucion','ProyectoController@invocarLugaresEjecucion')
                ->name('GestionProyectos.InvocarLugaresEjecucion');
               

            Route::get('/GestionProyectos/{codProyecto}/cargarMaestrosDetalle','ProyectoController@cargarMaestrosDetalle')->name('GestionProyectos.cargarMaestrosDetalle');
             
            
            
            
            //para excel (ESTAS LAS PUEDEN USAR TODOS)
            Route::get('/GestiónProyectos/{id}/ExportarPoblacion','ProyectoController@ExportarPoblacionBeneficiaria')->name('GestiónProyectos.ExportarPoblacion');
            Route::get('/GestionProyectos/{id}/ExportarModeloMarcoLogico','ProyectoController@ExportarModeloMarcoLogico')->name('GestionProyectos.ExportarModeloMarcoLogico');
            Route::get('/GestionProyectos/Metas/ExportarExcel/{codProyecto}','ProyectoController@exportarMetasEjecutadas')->name('GestionProyectos.Gerente.ExportarMetasEjecutadas');
            Route::get('/GestiónProyectos/{id}/darDeBaja','ProyectoController@darDeBaja')->name('GestiónProyectos.darDeBaja');
            Route::get('/GestiónProyectos/ActualizarEstado/{cadena}','ProyectoController@actualizarEstado');

            //seguimiento del proyecto
            Route::get('/GestionProyectos/descargarMedioVerificacionResultado/{codMedio}','MedioVerificacionResultadoController@descargar')
                ->name('MedioVerificacionResultado.descargar');
    
            Route::get('/GestionProyectos/IndicadorActividad/MetaEjecutada/DescargarMedioVerificacion/{codMedio}', 'IndicadorActividadController@descargarMedioVerificacion')
                ->name('MetaEjecutada.DescargarMedioVerificacion');
            

            /* -------- PROYECTOS-> Archivos del proyecto */

            Route::get('/GestionProyectos/descargarArchivo/{codArchivoProyecto}','ProyectoController@descargarArchivo')->name('GestionProyectos.descargarArchivo');
            Route::post('/GestionProyectos/añadirArchivos/','ProyectoController@añadirArchivos')->name('GestionProyectos.añadirArchivos');
            
            //desde JS se ejecuta
            Route::get('/GestionProyectos/eliminarArchivo/{codArchivo}','ProyectoController@eliminarArchivo')->name('GestionProyectos.eliminarArchivo');
            Route::get('/GestionProyectos/PoblacionBeneficiaria/{id}/VerDetalle','PersonaPoblacionController@listar' )->name('GestionProyectos.verPoblacionBeneficiaria');

        });


        Route::group(['middleware'=>"ValidarSesionUGE"],function()
        {
            Route::get('/GestionProyectos/descargarMML/{codProyecto}','ProyectoController@descargarMML')->name('GestionProyectos.descargarMML');
            Route::get('/GestiónProyectos/AdminSistema/listar','ProyectoController@index')->name('GestiónProyectos.AdminSistema.Listar');
            
            /* UGE */
            Route::get('/GestiónProyectos/UGE/editar/{id}','ProyectoController@editar')->name('GestiónProyectos.editar');
            Route::post('/GestiónProyectos/update','ProyectoController@update')->name('GestiónProyectos.update');
            Route::get('/GestiónProyectos/UGE/crear','ProyectoController@crear')->name('GestiónProyectos.crear');
            Route::post('/GestiónProyectos/store','ProyectoController@store')->name('GestiónProyectos.store');
            

            Route::get('/GestiónProyectos/UGE/Listar','ProyectoController@listarProyectosParaUGE')->name('GestiónProyectos.UGE.Listar');
            Route::get('/GestiónProyectos/UGE/Exportar/','ProyectoController@ExportarProyectosParaUGE')->name('GestiónProyectos.UGE.Exportar');
            
            Route::get('/GestionProyectos/UGE/Metas/VerEjecuciones/{codProyecto}','ProyectoController@registrarMetasEjecutadas')->name('GestionProyectos.UGE.RegistrarMetasEjecutadas');
             
            Route::post('GestionProyectos/{codProy}/actualizarContacto/','ProyectoController@actualizarContacto')->name('GestiónProyectos.UGE.ActualizarContacto');
            


            /* ------- PROYECTOS -> LUGARES DE EJECUCION  */
            Route::post('/GestionProyectos/agregarLugarEjecucion/','ProyectoController@agregarLugarEjecucion' )->name('GestionProyectos.agregarLugarEjecucion');
            Route::get('/GestionProyectos/eliminarLugarEjecucion/{id}','ProyectoController@eliminarLugarEjecucion' )->name('GestionProyectos.eliminarLugarEjecucion');
            
            /* ------- PROYECTOS -> POBLACION BENEFICIARIA  */
            Route::post('/GestionProyectos/agregarPoblacionBeneficiaria/','ProyectoController@agregarPoblacionBeneficiaria' )->name('GestionProyectos.agregarPoblacionBeneficiaria');
            Route::get('/GestionProyectos/eliminarPoblacionBeneficiaria/{id}','ProyectoController@eliminarPoblacionBeneficiaria' )->name('GestionProyectos.eliminarPoblacionBeneficiaria');
            
            
            
            

            /* ------- PROYECTOS -> PLAN ESTRATEGICO*/
            Route::post('/GestionProyectos/actualizarPEI/','ProyectoController@actualizarPEI' )->name('GestionProyectos.actualizarPEI');
            Route::post('/GestionProyectos/actualizarPorcentajesObjetivos/','ProyectoController@actualizarPorcentajesObjetivos' )->name('GestionProyectos.actualizarPorcentajesObjetivos');
            
            /* ------- PROYECTOS -> OBJETIVOS DEL MILENIO */
            Route::post('/GestionProyectos/actualizarPorcentajesMilenio','ProyectoController@actualizarPorcentajesMilenio')->name('GestionProyectos.actualizarPorcentajesMilenio');

            /* ------- PROYECTOS -> OBJETIVO ESPECIFICOS  */
            Route::post('/GestionProyectos/agregarEditarObjetivoEspecifico/','ProyectoController@agregarEditarObjetivoEspecifico' )->name('GestionProyectos.agregarEditarObjetivoEspecifico');
            Route::get('/GestionProyectos/eliminarObjetivoEspecifico/{id}','ProyectoController@eliminarObjetivoEspecifico' )->name('GestionProyectos.eliminarObjetivoEspecifico');

            Route::post('/GestionProyectos/agregarEditarIndicador','ProyectoController@agregarEditarIndicadorObjEsp')->name('GestionProyectos.agregarEditarIndicador');
            Route::get('/GestionProyectos/eliminarIndicador/{id}','ProyectoController@eliminarIndicador')->name('GestionProyectos.eliminarIndicador');

            
            
            
            /* ------- PROYECTOS -> RESULTADOS ESPERADOS Y SUS INDICADORES Y SUS MEDIOS DE VERIFICACION*/
            Route::post('/GestionProyectos/agregarEditarResultadoEsperado/','ProyectoController@agregarEditarResultadoEsperado' )->name('GestionProyectos.agregarEditarResultadoEsperado');
            Route::get('/GestionProyectos/eliminarResultadoEsperado/{id}','ProyectoController@eliminarResultadoEsperado' )->name('GestionProyectos.eliminarResultadoEsperado');
            

            Route::get('/GestionProyectos/eliminarIndicadorResultadoEsperado/{id}','ProyectoController@eliminarIndicadorResultado')->name('GestionProyectos.eliminarIndicadorResultado');
            
            Route::post('/GestionProyectos/agregarEditarIndicadorResultado','ProyectoController@agregarEditarIndicadorResultado')->name('GestionProyectos.agregarEditarIndicadorResultado');
            
            Route::post('/GestionProyectos/agregarMedioVerificacion','MedioVerificacionResultadoController@store')->name('GestionProyectos.agregarMedioVerificacion');
            
            Route::get('/GestionProyectos/MedioVerificacionResultado/{codMedio}/eliminar','MedioVerificacionResultadoController@eliminar')
            ->name('MedioVerificacionResultado.eliminar');
        
            

            /* ------- PROYECTOS -> Actividades y sus indicadores */
            Route::post('/GestionProyectos/agregarEditarActividad','ProyectoController@agregarEditarActividad')->name('GestionProyectos.agregarEditarActividad');
            Route::get('/GestionProyectos/eliminarActividad/{id}','ProyectoController@eliminarActividad')->name('GestionProyectos.eliminarActividad');
            
            Route::post('/GestionProyectos/agregarEditarIndicadorActividad','ProyectoController@agregarEditarIndicadorActividad')->name('GestionProyectos.agregarEditarIndicadorActividad');
            Route::get('/GestionProyectos/IndicadorActividad/{codIndicador}/Ver','IndicadorActividadController@verSeguimientoGrafico')->name('IndicadorActividad.Ver');
            Route::get('/GestionProyectos/EliminarIndicadorActividad/{codIndicador}','ProyectoController@EliminarIndicadorActividad')->name('GestionProyectos.EliminarIndicadorActividad');

            
            Route::get('/GestionProyectos/IndicadorActividad/{id}/eliminar','IndicadorActividadController@eliminar')->name('IndicadorActividad.EliminarValor');
            
            Route::get('/GestionProyectos/IndicadorActividad/{codIndicador}/RegistrarMetas','IndicadorActividadController@RegistrarMetas')->name('IndicadorActividad.RegistrarMetas');
            Route::get('/GestionProyectos/IndicadorActividad/registrarMetaProgramada','IndicadorActividadController@registrarMetaProgramada')->name('IndicadorActividad.registrarMetaProgramada');
            Route::get('/GestionProyectos/IndicadorActividad/editarMeta/{id}','IndicadorActividadController@editarMeta')->name('IndicadorActividad.editarMeta');
            Route::get('/GestionProyectos/IndicadorActividad/eliminarMeta/{codMeta}','IndicadorActividadController@eliminarMeta')->name('IndicadorActividad.EliminarMeta');
            
            
            /**GERENTES-CONTADORES */
            Route::get('/GestiónProyectos/{id}/asignarGerente','ProyectoController@actualizarProyectosYGerentesContadores');
            Route::get('/GestiónProyectos/{id}/asignarContador','ProyectoController@listarContadores')->name('GestiónProyectos.ListarContadores');
            Route::post('/GestiónProyectos/asignarContadores/save','ProyectoController@agregarContador')->name('GestiónProyectos.agregarContador');//usa la ruta no el name
            Route::get('/GestiónProyectos/{id}/eliminarContador','ProyectoController@eliminarContador')->name('GestiónProyectos.eliminarContador');

            Route::get('/RellenarProyectoContador','ProyectoController@RellenarProyectoContador')
            ->name('GestionProyectos.setearTodosLosContadoresATodosLosProyectos');



        });



        
        Route::group(['middleware'=>"ValidarSesionGerente"],function()
        {

            /* GERENTE */
            Route::get('/GestiónProyectos/Gerente/listar','ProyectoController@listarGerente')->name('GestiónProyectos.Gerente.Listar');
            Route::get('/GestionProyectos/Gerente/verProyecto/{id}','ProyectoController@editar')->name('GestionProyectos.Gerente.Ver');

            Route::get('/GestionProyectos/Gerente/Metas/RegistrarEjecuciones/{codProyecto}','ProyectoController@registrarMetasEjecutadas')
                ->name('GestionProyectos.Gerente.RegistrarMetasEjecutadas');


            
            Route::get('/GestionProyectos/Gerente/verMetas','ProyectoController@verMetas')->name('GestionProyectos.gerente.verMetas');
            Route::get('/GestionProyectos/Gerente/eliminarMedioVerificacion/{codMedio}','IndicadorActividadController@eliminarMedioVerificacion')->name('GestionProyectos.gerente.eliminarMedioVerificacion');
            
            Route::post('/GestionProyectos/MetaEjecutada/Actualizar','IndicadorActividadController@updateMeta')->name('Metas.Actualizar');
            
            //submit registrar ejecucion de meta
            Route::post('/GestionProyectos/IndicadorActividad/registrarCantidadEjecutada','IndicadorActividadController@registrarCantidadEjecutada')->name('IndicadorActividad.registrarCantidadEjecutada');//cambie de get a post
            

            /* -------- PROYECTOS -> Personas NATURALES Y JURIDICAS */

            Route::post('/GestionProyectos/PoblacionBeneficiaria/agregarEditarPersonaNatural','PersonaPoblacionController@agregarEditarPersonaNatural')->name('GestionProyectos.agregarEditarPersonaNatural');

            Route::post('/GestionProyectos/PoblacionBeneficiaria/agregarNaturalExistenteAPoblacion','PersonaPoblacionController@agregarNaturalExistenteAPoblacion')->name('GestionProyectos.agregarNaturalExistenteAPoblacion');
            Route::get('/GestionProyectos/PoblacionBeneficiaria/quitarNaturalDeLaPoblacion/{cadena}','PersonaPoblacionController@quitarNaturalDeLaPoblacion');
            Route::get('/GestionProyectos/PoblacionBeneficiaria/quitarJuridicaDeLaPoblacion/{cadena}','PersonaPoblacionController@quitarJuridicaDeLaPoblacion');
            
            Route::post('/GestionProyectos/PoblacionBeneficiaria/agregarJuridicaExistenteAPoblacion','PersonaPoblacionController@agregarJuridicaExistenteAPoblacion')->name('GestionProyectos.agregarJuridicaExistenteAPoblacion');
            
            Route::post('/GestionProyectos/PoblacionBeneficiaria/agregarEditarPersonaJuridica','PersonaPoblacionController@agregarEditarPersonaJuridica')->name('GestionProyectos.agregarEditarPersonaJuridica');
            Route::get('/GestionProyectos/PersonasRegistradas/','ProyectoController@listarPersonasRegistradas')->name('GestiónProyectos.AdminSistema.listarPersonasRegistradas');

            Route::post('/GestionProyectos/PoblacionBeneficiaria/guardarActividadesDePersona','PersonaPoblacionController@guardarActividadesDePersona')->name('GestionProyectos.guardarActividadesDePersona');

        });




        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* ----------------------------------- MODULO REPOSICIONES ------------------------------ */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        


        
        /**RUTA MAESTRA PARA REPOSICION */
        Route::get('/indexReposicion','ReposicionGastosController@listarReposiciones')
            ->name('ReposicionGastos.Listar');
        /**RUTA MAESTRA PARA DESCARGAR CDP */
        Route::get('/reposicion/descargarCDP/{cadena}','ReposicionGastosController@descargarCDP')
            ->name('ReposicionGastos.descargarCDP');

        /* Esta se consume desde JS */
        Route::get('/ReposicionGastos/eliminarArchivo/{codArchivoRepo}','ReposicionGastosController@eliminarArchivo');

        
        /**EMPLEADO*/
        Route::get('/ReposicionGastos/Empleado/listar','ReposicionGastosController@listarOfEmpleado')
            ->name('ReposicionGastos.Empleado.Listar');
        Route::get('/ReposicionGastos/Empleado/crear','ReposicionGastosController@create')
            ->name('ReposicionGastos.Empleado.create');
        Route::post('/ReposicionGastos/Empleado/store','ReposicionGastosController@store')
            ->name('ReposicionGastos.Empleado.store');

        Route::post('/ReposicionGastos/Empleado/update','ReposicionGastosController@update')
            ->name('ReposicionGastos.Empleado.update');
        Route::get('/ReposicionGastos/{id}/Empleado/editar','ReposicionGastosController@editar')
            ->name('ReposicionGastos.Empleado.editar');

        Route::get('/ReposicionGastos/{id}/Empleado/view','ReposicionGastosController@view')
            ->name('ReposicionGastos.Empleado.ver');

        Route::get('/ReposicionGastos/Empleado/misGastos','ReposicionGastosController@verMisGastos')
            ->name('ReposicionGastos.Empleado.verMisGastos');

        Route::get('/ReposicionGastos/{codDetalle}/Empleado/marcarComoVisto','ReposicionGastosController@marcarDetalleComoVisto')
            ->name('ReposicionGastos.Empleado.marcarComoVisto');

        


        Route::group(['middleware'=>"ValidarSesionGerente"],function()
        {
            /**GERENTE*/
            Route::get('/ReposicionGastos/Gerente/listar','ReposicionGastosController@listarOfGerente')
                ->name('ReposicionGastos.Gerente.Listar');
            Route::get('/ReposicionGastos/{id}/Gerente/view','ReposicionGastosController@viewGeren')
                ->name('ReposicionGastos.Gerente.ver');
            Route::Post('/ReposicionGastos/Gerente/Aprobar','ReposicionGastosController@aprobar')
                ->name('ReposicionGastos.Gerente.aprobar');
        });
        Route::group(['middleware'=>"ValidarSesionAdministracion"],function()
        {

            /**ADMINISTRACION*/
            Route::get('/ReposicionGastos/Administracion/listar','ReposicionGastosController@listarOfJefe')
                ->name('ReposicionGastos.Administracion.Listar');
            Route::get('/ReposicionGastos/{id}/Administracion/view','ReposicionGastosController@viewJefe')
                ->name('ReposicionGastos.Administracion.ver');
            Route::get('/ReposicionGastos/{id}/Abonar','ReposicionGastosController@abonar')
                ->name('ReposicionGastos.abonar');

        });
        Route::group(['middleware'=>"ValidarSesionContador"],function()
        {


            Route::get('/ReposicionGastos/{id}/Contabilizar','ReposicionGastosController@contabilizar')
            ->name('ReposicionGastos.contabilizar');//usa la ruta no el name

            /**CONTADOR*/
            Route::get('/ReposicionGastos/Contador/listar','ReposicionGastosController@listarOfConta')
                ->name('ReposicionGastos.Contador.Listar');
            Route::get('/ReposicionGastos/{id}/Contador/view','ReposicionGastosController@viewConta')
                ->name('ReposicionGastos.Contador.ver');


        
        });
        
        Route::get('/ReposicionGastos/{id}/Cancelar','ReposicionGastosController@cancelar')
        ->name('ReposicionGastos.cancelar');
    
        Route::get('/ReposicionGastos/{id}/Rechazar','ReposicionGastosController@rechazar')
            ->name('ReposicionGastos.rechazar');
        Route::post('/ReposicionGastos/Observar','ReposicionGastosController@observar')
            ->name('ReposicionGastos.observar');//usa la ruta no el name
        
        /**RUTA MAESTRA PARA DESCARGAR FORMULARIOS PDF */
        Route::get('/ReposicionGastos/{id}/PDF','ReposicionGastosController@descargarPDF')
            ->name('ReposicionGastos.exportarPDF');
        Route::get('/ReposicionGastos/{id}/verPDF','ReposicionGastosController@verPDF')
            ->name('ReposicionGastos.verPDF');





        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* --------------------------------MODULO REQUERIMIENTOS BS ----------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        
        /**RUTA MAESTRA PARA REPOSICION */
        Route::get('/indexRequerimiento','RequerimientoBSController@listarRequerimientos')
        ->name('RequerimientoBS.Listar');

        /* EMPLEADO */
        Route::get('/RequerimientoBS/Empleado/listar','RequerimientoBSController@listarOfEmpleado')
            ->name('RequerimientoBS.Empleado.Listar');

        Route::get('/RequerimientoBS/Empleado/crear','RequerimientoBSController@crear')
            ->name('RequerimientoBS.Empleado.CrearRequerimientoBS');
        Route::post('/RequerimientoBS/Empleado/store','RequerimientoBSController@store')
            ->name('RequerimientoBS.Empleado.store');

        Route::get('/RequerimientoBS/Empleado/{id}/ver','RequerimientoBSController@ver')
            ->name('RequerimientoBS.Empleado.ver');
        
        Route::get('/RequerimientoBS/Empleado/{id}/editar','RequerimientoBSController@editar')
            ->name('RequerimientoBS.Empleado.EditarRequerimientoBS');
        Route::post('/RequerimientoBS/Empleado/update','RequerimientoBSController@update')
            ->name('RequerimientoBS.Empleado.update');
        

        Route::get('/RequerimientoBS/Empleado/{id}/editar','RequerimientoBSController@editar')
            ->name('RequerimientoBS.Empleado.EditarRequerimientoBS');
        
        

        Route::get('/RequerimientoBS/descargarArchivoEmp/{id}','RequerimientoBSController@descargarArchivoEmp')
            ->name('RequerimientoBS.descargarArchivoEmp');

        /* Esta se consume desde JS */
        Route::get('/RequerimientoBS/Empleado/eliminarArchivoEmp/{codArchivoReq}','RequerimientoBSController@eliminarArchivo');

        

        Route::get('/RequerimientoBS/descargarArchivoAdm/{id}','RequerimientoBSController@descargarArchivoAdm')
            ->name('RequerimientoBS.descargarArchivoAdm');

        Route::get('/RequerimientoBS/verPDF/{id}','RequerimientoBSController@verPDF')->name('RequerimientoBS.verPDF');
        Route::get('/RequerimientoBS/descargarPDF/{id}','RequerimientoBSController@descargarPDF')->name('RequerimientoBS.descargarPDF');
        

            
        Route::group(['middleware'=>"ValidarSesionGerente"],function()
        {
            Route::get('/RequerimientoBS/Gerente/listar','RequerimientoBSController@listarOfGerente')
                ->name('RequerimientoBS.Gerente.Listar');
            Route::get('/RequerimientoBS/{id}/Gerente/view','RequerimientoBSController@viewGeren')
                ->name('RequerimientoBS.Gerente.ver');
            Route::Post('/RequerimientoBS/Gerente/Aprobar','RequerimientoBSController@aprobar')
                ->name('RequerimientoBS.Gerente.aprobar');
        });


        Route::group(['middleware'=>"ValidarSesionAdministracion"],function()
        {


            Route::get('/RequerimientoBS/Administrador/listar','RequerimientoBSController@listarOfAdministrador')
            ->name('RequerimientoBS.Administrador.Listar');

            Route::get('/RequerimientoBS/{id}/Administrador/VerAtender','RequerimientoBSController@VerAtender')
                ->name('RequerimientoBS.Administrador.VerAtender');
            

            Route::get('/RequerimientoBS/{id}/Administrador/atender','RequerimientoBSController@atender')
                ->name('RequerimientoBS.Administrador.Atender');

            Route::post('/RequerimientoBS/Administrador/subirArchivosAdministrador','RequerimientoBSController@subirArchivosAdministrador')
                ->name('RequerimientoBS.Administrador.subirArchivosAdministrador');
            
            
            Route::get('/RequerimientoBS/{id}/Administrador/eliminarArchivosAdmin','RequerimientoBSController@eliminarArchivosAdmin')
                ->name('RequerimientoBS.Administrador.eliminarArchivosAdmin');
            

            Route::get('/RequerimientoBS/{id}/Administrador/marcarQueYaTieneFactura','RequerimientoBSController@marcarQueYaTieneFactura')
                ->name('RequerimientoBS.Administrador.marcarQueYaTieneFactura');
            
            

        });


        Route::group(['middleware'=>"ValidarSesionContador"],function()
        {
            Route::get('/RequerimientoBS/Contador/listar','RequerimientoBSController@listarOfConta')
            ->name('RequerimientoBS.Contador.Listar');

            Route::get('/RequerimientoBS/{id}/Contador/view','RequerimientoBSController@viewConta')
                    ->name('RequerimientoBS.Contador.ver');

            Route::post('/RequerimientoBS/Contador/Contabilizar','RequerimientoBSController@contabilizar')
                    ->name('RequerimientoBS.Contador.Contabilizar');


            Route::get('/RequerimientoBS/{id}/Contador/contabilizarFactura','RequerimientoBSController@contabilizarFactura')
                ->name('RequerimientoBS.Contador.contabilizarFactura');
                
    
            Route::get('/RequerimientoBS/Contador/eliminarArchivoDelEmpleado/{codArchivoEmp}','RequerimientoBSController@ContadorEliminarArchivoDelEmpleado');
        });
        
        Route::get('/RequerimientoBS/{id}/Cancelar','RequerimientoBSController@cancelar')
            ->name('RequerimientoBS.cancelar');
        Route::get('/RequerimientoBS/{id}/Rechazar','RequerimientoBSController@rechazar')
            ->name('RequerimientoBS.rechazar');
        Route::post('/RequerimientoBS/Observar','RequerimientoBSController@observar')
            ->name('RequerimientoBS.observar');

        /**RUTA MAESTRA PARA DESCARGAR FORMULARIOS PDF */
        Route::get('/RequerimientoBS/{id}/PDF','RequerimientoBSController@descargarPDF')
            ->name('RequerimientoBS.exportarPDF');
        Route::get('/RequerimientoBS/{id}/verPDF','RequerimientoBSController@verPDF')
            ->name('RequerimientoBS.verPDF');



        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* --------------------------------MODULO DJ GASTOS MOVILIDAD---------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        

        Route::get('/DJ/GastosMovilidad','DJGastosMovilidadController@listarDeEmpleado')
            ->name('DJMovilidad.Empleado.Listar');

        Route::get('/DJ/GastosMovilidad/Crear','DJGastosMovilidadController@crearDJMov')
            ->name('DJMovilidad.Empleado.Crear');

        Route::post('/DJ/GastosMovilidad/Guardar','DJGastosMovilidadController@Guardar')
            ->name('DJMovilidad.Empleado.Guardar');
        
        Route::get('/DJ/GastosMovilidad/{codDJ}/descargarPDF','DJGastosMovilidadController@descargarPDF')
            ->name('DJMovilidad.Empleado.descargarPDF');

        Route::get('/DJ/GastosMovilidad/{codDJ}/verPDF','DJGastosMovilidadController@verPDF')
            ->name('DJMovilidad.Empleado.verPDF');
            
        Route::get('/DJ/GastosMovilidad/{codDJ}/ver','DJGastosMovilidadController@ver')
            ->name('DJMovilidad.Empleado.ver');

            

        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* --------------------------------  MODULO DJ GASTOS VARIOS ---------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */


        Route::get('/DJ/GastosVarios','DJGastosVariosController@listarDeEmpleado')
            ->name('DJVarios.Empleado.Listar');
        
        Route::get('/DJ/GastosVarios/Crear','DJGastosVariosController@crearDJVar')
            ->name('DJVarios.Empleado.Crear');

        Route::post('/DJ/GastosVarios/store','DJGastosVariosController@store')
            ->name('DJVarios.Empleado.Guardar');

        Route::get('/DJ/GastosVarios/{codDJ}/descargarPDF','DJGastosVariosController@descargarPDF')
            ->name('DJVarios.Empleado.descargarPDF');

        Route::get('/DJ/GastosVarios/{codDJ}/verPDF','DJGastosVariosController@verPDF')
            ->name('DJVarios.Empleado.verPDF');
        
        Route::get('/DJ/GastosVarios/{codDJ}/ver','DJGastosVariosController@ver')
            ->name('DJVarios.Empleado.ver');




        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* --------------------------------  MODULO DJ GASTOS VIATICOS -------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        

        Route::get('/DJ/GastosViaticos','DJGastosViaticosController@listarDeEmpleado')
            ->name('DJViaticos.Empleado.Listar');
        
        Route::get('/DJ/GastosViaticos/Crear','DJGastosViaticosController@crearDJVia')
            ->name('DJViaticos.Empleado.Crear');

        Route::post('/DJ/GastosViaticos/store','DJGastosViaticosController@Guardar')
            ->name('DJViaticos.Empleado.Guardar');

        Route::get('/DJ/GastosViaticos/{codDJ}/descargarPDF','DJGastosViaticosController@descargarPDF')
            ->name('DJViaticos.Empleado.descargarPDF');

        Route::get('/DJ/GastosViaticos/{codDJ}/verPDF','DJGastosViaticosController@verPDF')
            ->name('DJViaticos.Empleado.verPDF');
        
        Route::get('/DJ/GastosViaticos/{codDJ}/ver','DJGastosViaticosController@ver')
            ->name('DJViaticos.Empleado.ver');


        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* --------------------------------MODULO ORDEN DE COMPRA-------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */

         


        
        Route::group(['middleware'=>"ValidarSesionAdministracionOContador"],function()
        {
              
            Route::get('/OrdenCompra','OrdenCompraController@listarDeEmpleado')
                ->name('OrdenCompra.Empleado.Listar');
            
            Route::get('/OrdenCompra/Crear','OrdenCompraController@crearOrdenCompra')
                ->name('OrdenCompra.Empleado.Crear');

            Route::post('/OrdenCompra/store','OrdenCompraController@Guardar')
                ->name('OrdenCompra.Empleado.Guardar');
            
            Route::get('/OrdenCompra/{id}/Editar','OrdenCompraController@editarOrdenCompra')
                ->name('OrdenCompra.Empleado.Editar');

            Route::post('/OrdenCompra/update','OrdenCompraController@Update')
                ->name('OrdenCompra.Empleado.Update');

            Route::get('/OrdenCompra/{id}/Ver','OrdenCompraController@verOrdenCompra')
                ->name('OrdenCompra.Empleado.Ver');

            //PDF PARA ORDEN DE COMPRA
            Route::get('/OrdenCompra/{id}/descargar','OrdenCompraController@descargarPDF')
                ->name('OrdenCompra.descargarPDF');
            Route::get('/OrdenCompra/{id}/verPDF','OrdenCompraController@verPDF')
                ->name('OrdenCompra.verPDF');


            Route::get('/OrdenCompra/descargarArchivo/{id}','OrdenCompraController@descargarArchivo')
                ->name('OrdenCompra.descargarArchivo');
            Route::get('/OrdenCompra/eliminarArchivo/{id}','OrdenCompraController@eliminarArchivo')
                ->name('OrdenCompra.eliminarArchivo');

        });


        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* --------------------------------MODULO BUSCADOR MAESTRO ------------------------------ */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */
        /* -------------------------------------------------------------------------------------- */

        Route::group(['middleware'=>"ValidarSesionAdministracion"],function()
        {
        
            Route::get('/BuscadorMaestro','OperacionesController@buscadorMaestro')->name('BuscadorMaestro');
            
            Route::get('/BuscadorMaestro/CambiarEstadoDocumento','OperacionesController@CambiarEstadoDocumento')->name('CambiarEstadoDocumento');
            
            Route::get('/BuscadorMaestro/Invocables/GetListadoBusqueda','OperacionesController@GetListadoBusqueda')
                ->name('GetListadoBusqueda');
            

        });



    } ); //fin de middleware validar sesion




        








});