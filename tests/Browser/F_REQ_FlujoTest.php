<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Configuracion;
use App\Debug;
use App\DetalleRequerimientoGastos;
use App\FakerCedepas;
use App\Proyecto;
use App\RequerimientoGastos;
use App\RequerimientoBS;
use App\SolicitudFondos;
use App\User;
 
class F_REQ_FlujoTest extends DuskTestCase
{
        /* PARA CORRER SOLO ESTE TEST
        php artisan dusk tests/Browser/F_REQ_FlujoTest.php

    */

    public $codigoPresupuestal = "";
    public $requerimiento;
    const codUsuarioEmisor = 9;

    public function testFlujoREQ()
    {
        Debug::mensajeSimple("INICIANDO EL FLUJO DE REQUERIMIENTO");
        $this->assertTrue(Configuracion::mostrarInputsEscondidos);
        $this->crearRequerimiento();

        Debug::mensajeSimple("El cod presupuestal del REQUERIMIENTO es: '".$this->codigoPresupuestal."'");
        $this->requerimiento = RequerimientoBS::where('codigoCedepas','=',$this->codigoPresupuestal)->first();
         
        $this->gerente_observarRequerimiento();
        $this->empleado_editarRequerimiento();
        
        $this->aprobarRequerimiento();
         
        $this->atenderRequerimiento();
        $this->contabilizarRequerimiento();
 
        Debug::mensajeSimple("FLUJO DE REQUERIMIENTO FINALIZADO");

    }   

    public function crearRequerimiento(){


        $this->browse(function (Browser $browser) {        
            $proyecto = Proyecto::findOrFail(1);
            $cuerpo = FakerCedepas::F_REQ_generarCuerpo($proyecto);
            $usuario = User::findOrFail(static::codUsuarioEmisor);

            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('RequerimientoBS.Empleado.CrearRequerimientoBS'));

            foreach($cuerpo as $nombreCampo => $valor){
                Debug::mensajeSimple($nombreCampo."=".$valor);
                if(in_array($nombreCampo,['codProyecto'])) //si es de los de select
                    $browser = $browser->select('#'.$nombreCampo,$valor);
                else
                    $browser = $browser->type("#".$nombreCampo,$valor);
            }
            
            $cantidadItems = 10;
            for ($i=0; $i < $cantidadItems ; $i++) { 
                $detalle = FakerCedepas::F_REQ_GenerarDetalle($proyecto);
                $browser = $browser
                    ->select('#ComboBoxUnidad',$detalle['ComboBoxUnidad'])
                    ->type('#cantidad',$detalle['cantidad'])
                    ->type('#descripcion',$detalle['descripcion'])
                    ->type('#codigoPresupuestal',$detalle['codigoPresupuestal'])

                    ->press('#btnadddet');
                    
            }

            $nombreArchivo = "Ms Excel.pdf";
            $browser = $browser->attach('#filenames', __DIR__.'/ArchivosPrueba/'.$nombreArchivo);
            $browser = $browser->type('#nombresArchivos',$nombreArchivo);


            $browser = $browser
                ->press('#btnRegistrar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->screenshot('SS-ConfirmREQ')
                ->assertSee('¿Está seguro de crear el requerimiento?')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se ha Registrado el requerimiento') //Se ha Registrado el requerimiento N°REQ21-000041
                ->screenshot('SS-ListarREQdespuesdeCrear')
                ; 

            

            $mensajeLlegada = $browser->text('#msjEmergenteDatos');
            //Debug::mensajeSimple($mensajeLlegada);
            $this->codigoPresupuestal = mb_substr($mensajeLlegada,36,12);

            /* RENDIMIENTO
                con 10 ELEMENTOS
                    con screenshots demora 10.87 17.03 10.6 10.42
                    sin screenshots demora 10.45 10.54 10.62 11.44

                    Las screenshots no demoran mas el TEST
            */
             
        });


    }

    public function aprobarRequerimiento(){

        $this->browse(function (Browser $browser) {        
            $requerimiento = $this->requerimiento;
            $proyecto = $requerimiento->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail($proyecto->getGerente()->usuario()->codUsuario);
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('RequerimientoBS.Gerente.ver',$requerimiento->codRequerimiento));
             
            /* LOS CODIGOS PRESUPUESTALES NO LOS CAMBIAMOS */
            $browser = $browser
                    ->press('#botonActivarEdicion')
                    ->pause(100)
                    ->type('#justificacion',$requerimiento->resumen." CORREGIDO") //EL TYPE LO REMPLAZA TOTALMENTE
                    //->screenshot('hola mundo XD')
                    ; 
                     
            $browser = $browser
                
                ->press('#botonAprobar')
                ->pause(1000) //esperamos a que aparezca el modal de confirmacion
                ->assertSee('¿Está seguro de Aprobar el requerimiento?')
                ->screenshot('SS-ConfirmAprobacionREQ')
                ->press('SÍ')
                ->pause(500) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se aprobó correctamente el requeri')
                ->screenshot('SS-ListarREQ-postAprob')
                ;
             
        });

    }

    public function gerente_observarRequerimiento(){

        $this->browse(function (Browser $browser) {        
            $requerimiento = $this->requerimiento;
            $proyecto = $requerimiento->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail($proyecto->getGerente()->usuario()->codUsuario);
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('RequerimientoBS.Gerente.ver',$requerimiento->codRequerimiento));
            
            $browser = $browser
                ->press('#botonObservar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->assertSee('Observar Requerimiento de Bienes y Servicios')
                ->type('#observacion',"este es el texto de la observación.")
                ->press('#botonGuardarObservacion')
                ->assertSee('¿Esta seguro de observar el requerimiento?')
                ->screenshot('SS-ConfirmObservacionREQ')
                ->mouseover('.confirm.btn.btn-lg.btn-primary')
                ->screenshot('SS-MOUSE REQ sobre el si') //
                ->press('SÍ')
                ->pause(2500) //esperamos a que nos redirija a la pagina de listar. Aqui poner > a 2500 pq con menos falla
                ->screenshot('SS-ListarREQ-postObservacion')
                ->assertSee('Se observó correctamente el requer')
                /* 
                Por alguna razon a veces este test funciona y otras no xd
                sin cambiar codigo xd
                */
                ;

        });
    }

    public function empleado_editarRequerimiento(){
        

        $this->browse(function (Browser $browser) {        
            $requerimiento = $this->requerimiento;
            $usuario = User::findOrFail(static::codUsuarioEmisor);

            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('RequerimientoBS.Empleado.EditarRequerimientoBS',$requerimiento->codRequerimiento));

            $browser = $browser
                ->pause(1000)
                ->press('#btnRegistrar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->screenshot('SS-ConfirmUpdateREQ')
                ->assertSee('¿Está seguro de guardar los cambios del requerimiento?')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se ha editado el requerimient') // Se ha registrado la reposicion N°REQ21-000017
                ->screenshot('SS-ListarDespuesDeUpdateREQ')
                ; 

        });

    }

    public function atenderRequerimiento(){

        $this->browse(function (Browser $browser) {        
            $requerimiento = $this->requerimiento;
            $proyecto = $requerimiento->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail(9);//administradora MARYCRUZ BRIONES 
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('RequerimientoBS.Administrador.VerAtender',$requerimiento->codRequerimiento));
                     
            $browser = $browser
                ->press('#botonAtender')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->assertSee('¿Seguro que desea atender el requerimiento?')
                ->screenshot('SS-ConfirmAtenderREQ')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Atendido satisfactoriamente.')
                ->screenshot('SS-ListarREQ-postAtender')
                ;
             
        });
    }
    public function contabilizarRequerimiento(){

        $this->browse(function (Browser $browser) {        
            $requerimiento = $this->requerimiento;
            $proyecto = $requerimiento->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail(33);//contadora 
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('RequerimientoBS.Contador.ver',$requerimiento->codRequerimiento));
            
            /* Marcamos como contabilizados algunos gastos */

            $browser = $browser
                ->press('#botonContabilizarRequerimiento')
                ->assertSee('¿Desea marcar como contabilizada el requerimiento?')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->screenshot('SS-ConfirmContabilizacion-REQ')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Requerimiento REQ')
                ->screenshot('SS-ListarREQ-postContabilizar')
                ;
             
        });

         

    }

}
