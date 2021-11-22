<?php

namespace Tests\Feature;

use App\Debug;
use App\FakerCedepas;
use App\Proyecto;
use App\RendicionGastos;
use App\SolicitudFondos;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
class BackendSolicitudFondosTest extends TestCase
{

    /* 
    ESTA SERIE DE TESTS ENVIAN PETICIONES TEST AL SERVIDOR PARA PROBAR QUE FUNCIONE,
    NO PRUEBAN NADA DEL FRONTEND (vistas) NI DE SU RELACION CON EL BACKEND 
    SOLO PRUEBAN QUE NUESTRAS RUTAS GET Y POST FUNCIONEN (LISTEN,ALMACENEN,EDITEN Y ELIMINEN DATOS)

    ESTE DOCUMENTO PRUEBA TODO EL FLUJO DE SOLICITUD FONDOS 
    Y TAMBIEN EL DE RENDICION (PORQUE ES UN SOLO FLUJO)
     */
    
     

    public function test_FlujoSolicitudFondos(){
        $proyectoSeleccionado = Proyecto::findOrFail(1);
        
        $codigoCedepas = $this->guardarSolicitudFondos($proyectoSeleccionado);
        
/* 
        $solicitud = SolicitudFondos::where('codigoCedepas','=',$codigoCedepas)->first();
        $this->editarSolicitudFondos($solicitud);
        $this->aprobarSolicitudFondos($solicitud);
        $this->abonarSolicitudFondos($solicitud);
        $this->contabilizarSolicitudFondos($solicitud);

        $codigoCedepasRend = $this->crearRendicion($solicitud);
        Debug::mensajeSimple('el codigo de la rendicion es "'.$codigoCedepasRend.'"');
        $rendicion = RendicionGastos::where('codigoCedepas','=',$codigoCedepasRend)->get();

        Debug::mensajeSimple('la rend:'.($rendicion));
         */
       /*  $this->editarRendicion($rendicion);
        $this->aprobarRendicion($rendicion);
        $this->contabilizarRendicion($rendicion);
 */
    }


    public function guardarSolicitudFondos($proyectoSeleccionado){
        /* Usaremos al empleado APARCO HUAMAN HUBERT RICHARD como user de prueba */
        $usuario = User::findOrFail(0);
        /* Cuerpo de la peticion POST */
        

        $cuerpo = FakerCedepas::generarSolicitudFondos($proyectoSeleccionado);

        $ruta = route('SolicitudFondos.Empleado.Guardar');
        
        $response = $this
            ->actingAs($usuario)
            ->postJson($ruta,$cuerpo,[]);

        //$response->assertStatus(302); //código de estado de redirección HTTP 302
        $response->assertRedirect(route('SolicitudFondos.Empleado.Listar'));
        
        $mensajeLlegada = session('datos'); //mensaje con el que retorna al listar
        $mensajeContenido = 'Se ha creado la solicitud';  //parte del mensaje que debe tener 

        $codigoCedepas = substr($mensajeLlegada,26,12);

        $this->assertTrue( str_contains($mensajeLlegada,$mensajeContenido) );
        Debug::mensajeSimple('EL CODIGO CEDEPAS CREADO ES:'.$codigoCedepas);

        return $codigoCedepas;

    }

    public function editarSolicitudFondos($solicitud){

        
        
        Debug::mensajeSimple('El codigo que estoy usando es'.$solicitud->codSolicitud);
        $usuario = User::findOrFail(0);
        

        $cuerpo = FakerCedepas::generarSolicitudFondos($solicitud->getProyecto());
        //array_merge($cuerpo,[''=>'']);

        $ruta = route('SolicitudFondos.Empleado.update',$solicitud->codSolicitud);
        
        $response = $this
            ->actingAs($usuario)
            ->postJson($ruta,$cuerpo,[]);

        //$response->assertStatus(302); //código de estado de redirección HTTP 302
        $response->assertRedirect(route('SolicitudFondos.Empleado.Listar'));
        
        $mensajeLlegada = session('datos'); //mensaje con el que retorna al listar
        $mensajeContenido = 'actualizado';  //parte del mensaje que debe tener 

        
        $this->assertTrue( str_contains($mensajeLlegada,$mensajeContenido) );
    


    }

    public function aprobarSolicitudFondos($solicitud){
        $usuarioGerente = $solicitud->getProyecto()->getGerente()->usuario(); 
        
        $ruta = route('SolicitudFondos.Gerente.Aprobar');
        $cuerpo = FakerCedepas::generarDataAprobacion($solicitud);
        
        $response = $this
            ->actingAs($usuarioGerente)
            ->postJson($ruta,$cuerpo,[]);

        //$response->assertStatus(302); //código de estado de redirección HTTP 302
        $response->assertRedirect(route('SolicitudFondos.Gerente.Listar'));
        
        $mensajeLlegada = session('datos'); //mensaje con el que retorna al listar
        $mensajeContenido = 'Aprobada';  //parte del mensaje que debe tener 

        $this->assertTrue( str_contains($mensajeLlegada,$mensajeContenido) );

    }

    public function abonarSolicitudFondos($solicitud){

        $usuarioAdministrador = User::findOrFail(9); //MARYCRUZ BRIONES
        
        $ruta = route('SolicitudFondos.Administracion.Abonar');
        $cuerpo = FakerCedepas::generarDataAbonacion($solicitud);
        
        $response = $this
            ->actingAs($usuarioAdministrador)
            ->postJson($ruta,$cuerpo,[]);

        //$response->assertStatus(302); //código de estado de redirección HTTP 302
        $response->assertRedirect(route('SolicitudFondos.Administracion.Listar'));
        
        $mensajeLlegada = session('datos'); //mensaje con el que retorna al listar
        $mensajeContenido = 'Abonada';  //parte del mensaje que debe tener 

        $this->assertTrue( str_contains($mensajeLlegada,$mensajeContenido) );


    }

    public function contabilizarSolicitudFondos($solicitud){
        $empleadoContador = $solicitud->getProyecto()->getListaContadores()[0];
        $usuarioContador = $empleadoContador->usuario(); // ALGUN CONTADOR DEL PROYECTO
        
        $ruta = route('SolicitudFondos.Contador.Contabilizar',$solicitud->codSolicitud);
        $response = $this
            ->actingAs($usuarioContador)
            ->get($ruta);
        
        $response->assertRedirect(route('SolicitudFondos.Contador.Listar'));
        
    }
 

    public function crearRendicion($solicitud){
        $usuario = User::findOrFail(0);
        /* Cuerpo de la peticion POST */
        $cuerpo = FakerCedepas::generarDataRendicion($solicitud);

        $ruta = route('RendicionGastos.Empleado.Store');
        
        $response = $this
            ->actingAs($usuario)
            ->postJson($ruta,$cuerpo,[]);

        //$response->assertStatus(302); //código de estado de redirección HTTP 302
        $response->assertRedirect(route('RendicionGastos.Empleado.Listar'));
        
        $mensajeLlegada = session('datos'); //mensaje con el que retorna al listar
        $mensajeContenido = 'Se ha creado la rendición';  //parte del mensaje que debe tener 
        
        $codigoCedepas = mb_substr($mensajeLlegada,28,12);
        
        $this->assertTrue(  str_contains($mensajeLlegada,$mensajeContenido) );
        Debug::mensajeSimple('MENSAJE LLEGADA:"'.$mensajeLlegada.'"');

        return $codigoCedepas;

    }

    public function editarRendicion($rendicion){
        $usuario = User::findOrFail(0);
        /* Cuerpo de la peticion POST */
        $cuerpo = FakerCedepas::generarDataRendicion($rendicion->getSolicitud());

        $ruta = route('RendicionGastos.Empleado.Update');
        
        $response = $this
            ->actingAs($usuario)
            ->postJson($ruta,$cuerpo,[]);

        //$response->assertStatus(302); //código de estado de redirección HTTP 302
        $response->assertRedirect(route('RendicionGastos.Empleado.Listar'));
        
        $mensajeLlegada = session('datos'); //mensaje con el que retorna al listar
        $mensajeContenido = 'Se ha Editado la rendición';  //parte del mensaje que debe tener 

        
        $this->assertTrue( str_contains($mensajeLlegada,$mensajeContenido) );
        

    }


    public function aprobarRendicion($rendicion){


    }

    

    public function contabilizarRendicion($rendicion){


    }



}
