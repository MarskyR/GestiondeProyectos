<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

use App\Configuracion;
use App\Debug;
use App\DetalleReposicionGastos;
use App\FakerCedepas;
use App\Proyecto;
use App\ReposicionGastos;
use App\SolicitudFondos;
use App\User;
 


class F_REP_FlujoTest extends DuskTestCase
{
     

    /* PARA CORRER SOLO ESTE TEST
        php artisan dusk tests/Browser/F_REP_FlujoTest.php

    */

    public $codigoPresupuestal = "";
    public $reposicion;

    const codUsuarioEmisor = 9;

    public function testFlujoREP()
    {
        Debug::mensajeSimple("INICIANDO EL FLUJO DE REPOSICION");
        $this->assertTrue(Configuracion::mostrarInputsEscondidos);
        $this->crearReposicion();

        Debug::mensajeSimple("El cod presupuestal de la repo es: '".$this->codigoPresupuestal."'");
        $this->reposicion = ReposicionGastos::where('codigoCedepas','=',$this->codigoPresupuestal)->first();
        
        $this->gerente_observarReposicion();
        $this->empleado_editarReposicion();

        $this->gerente_observarReposicion();
        $this->empleado_editarReposicion();

        $this->gerente_observarReposicion();
        $this->empleado_editarReposicion();
        
        $this->aprobarReposicion();
        $this->abonarReposicion();
        $this->contabilizarReposicion();

        Debug::mensajeSimple("FLUJO DE REPOSICION FINALIZADO");

    }   

    public function crearReposicion(){


        $this->browse(function (Browser $browser) {        
            $proyecto = Proyecto::findOrFail(1);
            $cuerpo = FakerCedepas::F_REP_generarCuerpo($proyecto);
            $usuario = User::findOrFail(static::codUsuarioEmisor);
            

            $browser = $browser
                ->loginAs($usuario)
                ->visit('/ReposicionGastos/Empleado/crear');

            foreach($cuerpo as $nombreCampo => $valor){
                Debug::mensajeSimple($nombreCampo."=".$valor);
                if(in_array($nombreCampo,['codProyecto','codBanco','codMoneda'])) //si es de los de select
                    $browser = $browser->select('#'.$nombreCampo,$valor);
                else
                    $browser = $browser->type("#".$nombreCampo,$valor);
                
            }


            $cantidadItems = 10;
            for ($i=0; $i < $cantidadItems ; $i++) { 
                $detalle = FakerCedepas::F_REP_GenerarDetalle($proyecto);
                $browser = $browser
                    ->type('#fechaComprobante',$detalle['colFecha'])
                    ->select('#ComboBoxCDP',$detalle['colTipo'])
                    ->type('#ncbte',$detalle['colComprobante'])
                    ->type('#concepto',$detalle['colConcepto'])
                    ->type('#importe',$detalle['colImporte'])
                    ->type('#codigoPresupuestal',$detalle['colCodigoPresupuestal'])
                    //->screenshot('SS-PRUEBA')
                    ->press('#btnadddet');
                    
            }

            $nombreArchivo = "Ms Excel.pdf";
            $browser = $browser->attach('#filenames', __DIR__.'/ArchivosPrueba/'.$nombreArchivo);
            $browser = $browser->type('#nombresArchivos',$nombreArchivo);


            $browser = $browser
                ->press('#btnRegistrar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->screenshot('SS-ConfirmREP')
                ->assertSee('¿Está seguro de crear la reposicion?')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se ha registrado la rep') // Se ha registrado la reposicion N°REP21-000017
                ->screenshot('SS-ListarREPdespuesdeCrear')
                ; 

            

            $mensajeLlegada = $browser->text('#msjEmergenteDatos');
            //Debug::mensajeSimple($mensajeLlegada);
            $this->codigoPresupuestal = mb_substr($mensajeLlegada,33,12);

            /* RENDIMIENTO
                con 10 ELEMENTOS
                    con screenshots demora 10.87 17.03 10.6 10.42
                    sin screenshots demora 10.45 10.54 10.62 11.44

                    Las screenshots no demoran mas el TEST
            */
             
        });


    }

    public function aprobarReposicion(){

        $this->browse(function (Browser $browser) {        
            $reposicion = $this->reposicion;
            $proyecto = $reposicion->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail($proyecto->getGerente()->usuario()->codUsuario);
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('ReposicionGastos.Gerente.ver',$reposicion->codReposicionGastos));
             
            /* LOS CODIGOS PRESUPUESTALES NO LOS CAMBIAMOS */
            $browser = $browser
                    ->press('#botonActivarEdicion')
                    ->pause(100)
                    ->type('#resumen',$reposicion->resumen." CORREGIDO") //EL TYPE LO REMPLAZA TOTALMENTE
                    //->screenshot('hola mundo XD')
                    ; 
                     
            $browser = $browser
                
                ->press('#botonAprobar')
                ->pause(1000) //esperamos a que aparezca el modal de confirmacion
                ->assertSee('¿Está seguro de Aprobar la Reposición?')
                ->screenshot('SS-ConfirmAprobacionREP')
                ->press('SÍ')
                ->pause(500) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se aprobó correctamente la Reposic')
                ->screenshot('SS-ListarREP-postAprob')
                ;
             
        });

    }

    public function gerente_observarReposicion(){

        $this->browse(function (Browser $browser) {        
            $reposicion = $this->reposicion;
            $proyecto = $reposicion->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail($proyecto->getGerente()->usuario()->codUsuario);
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('ReposicionGastos.Gerente.ver',$reposicion->codReposicionGastos));
            
            $browser = $browser
                ->press('#botonObservar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->assertSee('Observar Reposición de Gastos')
                ->type('#observacion',"este es el texto de la observación.")
                ->press('#botonGuardarObservacion')
                ->assertSee('¿Esta seguro de observar la reposicion?')
                ->screenshot('SS-ConfirmObservacionREP')
                ->mouseover('.confirm.btn.btn-lg.btn-primary')
                ->screenshot('SS-MOUSE sobre el si') //
                ->press('SÍ') 
                ->pause(2500) //esperamos a que nos redirija a la pagina de listar. Aqui poner > a 2500 pq con menos falla
                ->screenshot('SS-ListarREP-postObservacion')
                ->assertSee('correctamente la Rep')
                
                /* 
                Por alguna razon a veces este test funciona y otras no xd
                sin cambiar codigo xd
                */
                ;

        });
    }

    public function empleado_editarReposicion(){
        

        $this->browse(function (Browser $browser) {        
            $reposicion = $this->reposicion;
            $usuario = User::findOrFail(static::codUsuarioEmisor);

            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('ReposicionGastos.Empleado.editar',$reposicion->codReposicionGastos));

            $browser = $browser
                ->pause(1000)
                ->press('#btnRegistrar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->screenshot('SS-ConfirmUpdateREP')
                ->assertSee('¿Seguro de guardar los cambios de la reposición?')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se ha editado la reposi') // Se ha registrado la reposicion N°REP21-000017
                ->screenshot('SS-ListarDespuesDeUpdateREP')
                ; 

        });

    }

    public function abonarReposicion(){

        $this->browse(function (Browser $browser) {        
            $reposicion = $this->reposicion;
            $proyecto = $reposicion->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail(9);//administradora MARYCRUZ BRIONES 
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('ReposicionGastos.Administracion.ver',$reposicion->codReposicionGastos));
                     
            $browser = $browser
                ->press('#botonAbonar')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->assertSee('¿Esta seguro de abonar la reposicion?')
                ->screenshot('SS-ConfirmAbonacionREP')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se abonó correctamente l')
                ->screenshot('SS-ListarREP-postAbono')
                ;
             
        });
    }
    public function contabilizarReposicion(){

        $this->browse(function (Browser $browser) {        
            $reposicion = $this->reposicion;
            $proyecto = $reposicion->getProyecto();

            //$cuerpo = FakerCedepas::F_SOL_generarCuerpoAprobacion($proyecto);
            
            $usuario = User::findOrFail(33);//contadora 
            
            $browser = $browser
                ->loginAs($usuario)
                ->visit(route('ReposicionGastos.Contador.ver',$reposicion->codReposicionGastos));
            
            /* Marcamos como contabilizados algunos gastos */
            
            $detalles  = $reposicion->detalles();
            foreach ($detalles as $detalle) {
                $num = rand(0,1); //Aleatoriamente marcamos algunos
                if($num==0){
                    $browser = $browser->press('#checkBoxContabilizarItem'.$detalle->codDetalleReposicion);
                }
            }

            
            $browser = $browser
                ->press('#botonContabilizar')
                ->assertSee('¿Seguro de contabilizar la reposicion?')
                ->pause(300) //esperamos a que aparezca el modal de confirmacion
                ->screenshot('SS-ConfirmContabilizacion-REP')
                ->press('SÍ')
                ->pause(200) //esperamos a que nos redirija a la pagina de listar
                ->assertSee('Se contabilizó correctam')
                ->screenshot('SS-ListarREP-postContabilizar')
                ;
             
        });

         

    }



}
