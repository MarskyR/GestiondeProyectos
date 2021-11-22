<?php

namespace Tests\Feature;

use App\Debug;
use App\Empleado;
use App\FakerCedepas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FrontendSolicitudFondosTest extends TestCase
{
    
    public function testExample()
    {

        $usuario = FakerCedepas::getUsuarioAleatorio();
        Debug::mensajeSimple('usando el useR:' .$usuario->codUsuario);
        $ruta = route('SolicitudFondos.Empleado.Create');

        $response = $this
            ->actingAs($usuario)
            ->get($ruta);
        

        $response->assertStatus(200);
        $response->assertSee("");


    }
}
