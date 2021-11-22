<?php

namespace App\Http\Controllers;

use App\ActividadPrincipal;
use App\Configuracion;
use App\Debug;
use App\ErrorHistorial;
use App\Fecha;
use App\Http\Controllers\Controller;
use App\PersonaJuridicaPoblacion;
use App\PersonaNaturalPoblacion;
use App\PersonaPoblacion;
use App\PoblacionBeneficiaria;
use App\RelacionPersonaJuridicaActividad;
use App\RelacionPersonaJuridicaPoblacion;
use App\RelacionPersonaNaturalActividad;
use App\RelacionPersonaNaturalPoblacion;
use App\TipoPersonaJuridica;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaPoblacionController extends Controller
{
    
   

    //retorna la vista con la lista las personas de una poblacion, tanto naturales como juridicas
    public function listar($codPoblacion){
        $poblacion = PoblacionBeneficiaria::findOrFail($codPoblacion);

        $listaRelacionesPersonasNaturales = RelacionPersonaNaturalPoblacion::where('codPoblacionBeneficiaria','=',$codPoblacion)->get();
        $listaRelacionesPersonasJuridicas = RelacionPersonaJuridicaPoblacion::where('codPoblacionBeneficiaria','=',$codPoblacion)->get();
        
        $vectorCodPersonaNatural=[];
        foreach ($listaRelacionesPersonasNaturales as $item) {
            $vectorCodPersonaNatural[]=$item->codPersonaNatural;
        }

        $vectorCodPersonaJuridica=[];
        foreach ($listaRelacionesPersonasJuridicas as $item) {
            $vectorCodPersonaJuridica[]=$item->codPersonaJuridica;
        }



        $listaPersonasNaturales = PersonaNaturalPoblacion::whereIn('codPersonaNatural',$vectorCodPersonaNatural)->get();
        $listaPersonasJuridicas = PersonaJuridicaPoblacion::whereIn('codPersonaJuridica',$vectorCodPersonaJuridica)->get();


        $tiposPersonaJuridica = TipoPersonaJuridica::All();

        $listaActividades = ActividadPrincipal::All();

        //las personas que están registradas en la BD pero no están en esta poblacion, tenemos estas listas para mostrar en el select
        //en realidad son objetos del modelo RelacionPersonaNatural
        $listaPersonasNaturalesAjenas = $poblacion->getPersonasNaturalesAjenas();
        $listaPersonasJuridicasAjenas = $poblacion->getPersonasJuridicasAjenas();
        
        return view('Proyectos.Poblacion.VerPoblacion',
            compact('poblacion','listaPersonasNaturales','listaPersonasJuridicas',
            'listaPersonasNaturalesAjenas','listaPersonasJuridicasAjenas' ,'tiposPersonaJuridica','listaActividades'
    ));

    }


#region NATURALES




    //funcion servicio, será consumida solo por javascript
    public function listarPersonasNaturales($codPob){
        $vector = [];
        $listaPersonas = PersonaNaturalPoblacion::where('codPoblacionBeneficiaria','=',$codPob)->get();

        for ($i=0; $i < count($listaPersonas) ; $i++) { 
            $itemDet = $listaPersonas[$i];
            // formato dado por sql 2021-02-11   
            //formato requerido por mi  12/02/2020
            $fechaDet = $itemDet->fechaNacimiento;
            //DAMOS VUELTA A LA FECHA
                            // DIA                  MES                 AÑO
            $nuevaFecha=substr($fechaDet,8,2).'/'.substr($fechaDet,5,2).'/'.substr($fechaDet,0,4);
            $itemDet['fechaFormateada'] = $nuevaFecha;
            array_push($vector,$itemDet);            
        }
        return $vector  ;
    }


    //agregar persona natural a la poblacion
    public function agregarEditarPersonaNatural(Request $request ){
      
        try{
            db::beginTransaction();
        
            $poblacion = PoblacionBeneficiaria::findOrFail($request->codPoblacionBeneficiaria);
            

                
        

            if($request->codPersonaNatural=="0"){//NUEVO REGISTRO

                /* VALIDACION PARA NO REGISTRAR A ALGUIEN QUE YA ESTÁ REGISTRADO (por dnis) */

                $personaEncontrada = PersonaNaturalPoblacion::buscarPorDNI($request->dniNatural);
                //primero buscamos a alguien con ese dni, si ya existiera, no permitimos.
                if($personaEncontrada != "")//ya existe en el sistema
                {
                    $mensaje="";
                    if($poblacion->buscarPorCodPersonaNatural($personaEncontrada->codPersonaNatural)){//ya está en esta poblacion
                        $mensaje='ERROR: La persona con el DNI indicado ya está registrada en esta población beneficiaria como "'.
                            $personaEncontrada->getNombreCompleto().'".';
                    }else{//no esta en esta poblacion
                        $mensaje='ERROR: La persona con el DNI indicado ya está registrada en la base de datos del sistema como "'.
                            $personaEncontrada->getNombreCompleto().'", solo  debe añadirla a esta población.';
                    }
                    return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
                    ->with('datos',$mensaje);
                }



                $natural=new PersonaNaturalPoblacion();
                $natural->dni=$request->dniNatural;
                $natural->nombres=$request->nombresNatural;
                $natural->apellidos=$request->apellidosNatural;

                $natural->fechaNacimiento = Fecha::formatoParaSQL($request->fechaNacimientoNatural);
               
                $natural->edadMomentanea=$request->edadNatural;
                $natural->sexo=$request->sexoNatural;
                $natural->direccion=$request->direccionNatural;
                $natural->nroTelefono=$request->telefonoNatural;
                //$natural->actividadPrincipal=$request->actividadPrincipalNatural;
                
                $natural->codLugarEjecucion=$request->codLugarEjecucion;
                
                $natural->save();
                
                $detalle=new RelacionPersonaNaturalPoblacion();
                $detalle->codPersonaNatural = $natural->codPersonaNatural;
                $detalle->codPoblacionBeneficiaria = $request->codPoblacionBeneficiaria;

                $detalle->save();

                $mensajeLlegada = "registrada";

            }else{ //registro ya existente estamos editando
                $natural= PersonaNaturalPoblacion::findOrFail($request->codPersonaNatural);

                $natural->dni=$request->dniNatural;
                $natural->nombres=$request->nombresNatural;
                $natural->apellidos=$request->apellidosNatural;

                $natural->fechaNacimiento = Fecha::formatoParaSQL($request->fechaNacimientoNatural);
                $natural->edadMomentanea=$request->edadNatural;
                $natural->sexo=$request->sexoNatural;
                $natural->direccion=$request->direccionNatural;
                $natural->nroTelefono=$request->telefonoNatural;
                //$natural->actividadPrincipal=$request->actividadPrincipalNatural;
                $natural->codLugarEjecucion=$request->codLugarEjecucion;
                
                $natural->save();
                
                $mensajeLlegada = "editada";
            }
            db::commit();

            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
                ->with('datos','Información de "'.$natural->getNombreCompleto().'" '.$mensajeLlegada.' exitosamente.');
           
        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('Personapoblacioncontroller agregareditarpersonanatura ',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
             
        }
        

    }

    
    public function agregarNaturalExistenteAPoblacion(Request $request){
        try{
            db::beginTransaction();
            $poblacion = PoblacionBeneficiaria::findOrFail($request->codPoblacionBeneficiaria);
            $persona = PersonaNaturalPoblacion::findOrFail($request->codEmpleadoBuscar);

            $relacion = new RelacionPersonaNaturalPoblacion();
            $relacion->codPersonaNatural = $persona ->codPersonaNatural;
            $relacion->codPoblacionBeneficiaria = $poblacion->codPoblacionBeneficiaria;
            $relacion->save();

            db::commit();
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
                ->with('datos','Persona natural "'.$persona->getNombreCompleto().'" añadida a la población beneficiaria');
        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER : agregarAPoblacion',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                                app('request')->route()->getAction(),
                                                                json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }



    /* cadena llega en formato 52*15
    donde 52 es el codigo de la poblacion beneficiaria
    15 es el codigo de la persona natural a eliminar de esta

    */
    function quitarNaturalDeLaPoblacion($cadena){
        try {
            db::beginTransaction();
            $vector = explode('*',$cadena);
            $codPoblacionBeneficiaria = $vector[0];
            $codPersonaNatural = $vector[1];

            $poblacion = PoblacionBeneficiaria::findOrFail($codPoblacionBeneficiaria);
            $persona = PersonaNaturalPoblacion::findOrFail($codPersonaNatural);

            $relacionesExistentes = RelacionPersonaNaturalPoblacion::
                where('codPoblacionBeneficiaria','=',$codPoblacionBeneficiaria)
                ->where('codPersonaNatural','=',$codPersonaNatural)
                ->get();
            if(count($relacionesExistentes) == 0){
                $mensaje = "ERROR: La persona no pertenece a esta población beneficiaria, ya fue eliminada anteriormente.";
            }else{
                //ahora sí quitamos a la relacion
                $relacion = $relacionesExistentes[0];
                $relacion->delete();
                $mensaje = 'Se ha eliminado exitosamente a '.$persona->getNombreCompleto().' de la población beneficiaria '.$poblacion->descripcion.'.';

            }
            db::commit();

            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
                ->with('datos',$mensaje);


        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER : quitarNaturalDeLaPoblacion ',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena );
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }



    }


    
    /* cadena llega en formato 52*15
    donde 52 es el codigo de la persona natural
    15 es el codigo de la actividad
    */
    /* 
    DEPRECADO PORQUE LO REMPLACE POR UN FORM COMPLETO FUNCION guardarActividadesDePersona
    public function agregarQuitarActividadANatural($cadena){
        try {
            db::beginTransaction();
            $vector = explode('*',$cadena);
            $codPersonaNatural = $vector[0];
            $codActividadPrincipal = $vector[1];
           
            $actividad = ActividadPrincipal::findOrFail($codActividadPrincipal);
            $persona = PersonaNaturalPoblacion::findOrFail($codPersonaNatural);

            $relacionesExistentes = RelacionPersonaNaturalActividad::
                where('codPersonaNatural','=',$codPersonaNatural)
                ->get();
            
            if(count($relacionesExistentes) == 0){//no hay ninguna, lacreamos
                $relacion = new RelacionPersonaNaturalActividad();
                $relacion->codActividadPrincipal =  $codActividadPrincipal;
                $relacion->codPersonaNatural= $codPersonaNatural;
                $relacion->save();

            }else{
                //ahora sí quitamos a la relacion
                $relacion = $relacionesExistentes[0];
                $relacion->delete();
                
            }
            db::commit();
            return "1";

        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER : agregarQuitarActividadANatural ',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena );
            return Configuracion::getMensajeError($codErrorHistorial);
        }



    }

 */
#endregion

#region JURIDICAS

    public function agregarEditarPersonaJuridica(Request $request ){

        try{
            db::beginTransaction();
        
            $poblacion = PoblacionBeneficiaria::findOrFail($request->codPoblacionBeneficiaria);

            if($request->codPersonaJuridica=="0"){//NUEVO REGISTRO

                /* VALIDACION PARA NO REGISTRAR A ALGUIEN QUE YA ESTÁ REGISTRADO (por dnis) */

                $personaEncontrada = PersonaJuridicaPoblacion::buscarPorRUC($request->ruc);
                //primero buscamos a alguien con ese dni, si ya existiera, no permitimos.
                if($personaEncontrada != "")//ya existe en el sistema
                {
                    $mensaje="";
                    if($poblacion->buscarPorCodPersonaJuridica($personaEncontrada->codPersonaJuridica)){//ya está en esta poblacion
                        $mensaje='ERROR: La organización con el RUC indicado ya está registrada en esta población beneficiaria como "'.
                            $personaEncontrada->razonSocial.'".';
                    }else{//no esta en esta poblacion
                        $mensaje='ERROR: La organización con el RUC indicado ya está registrada en la base de datos del sistema como "'.
                            $personaEncontrada->razonSocial.'", solo  debe añadirla a esta población.';
                    }
                    return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
                    ->with('datos',$mensaje);
                }



                $juridica=new PersonaJuridicaPoblacion();
                $juridica->ruc=$request->ruc;
                $juridica->razonSocial=$request->razonSocial;
                $juridica->direccion=$request->direccionInputJuridico;
                //$juridica->actividadPrincipal=$request->actividadPrincipal;
                $juridica->codTipoPersonaJuridica = $request->codTipoPersonaJuridica;
                $juridica->representante = $request->representante;
                
                
                $juridica->numeroSociosHombres=$request->numeroSociosHombres;
                $juridica->numeroSociosMujeres=$request->numeroSociosMujeres;
                
                $juridica->save();
                
                $detalle=new RelacionPersonaJuridicaPoblacion();
                $detalle->codPersonaJuridica = $juridica->codPersonaJuridica ;
                $detalle->codPoblacionBeneficiaria = $request->codPoblacionBeneficiaria;

                $detalle->save();

                $mensajeLlegada = "registrada";

            }else{ //registro ya existente estamos editando
                $juridica= PersonaJuridicaPoblacion::findOrFail($request->codPersonaJuridica);
                $juridica->ruc=$request->ruc;
                $juridica->razonSocial=$request->razonSocial;
                $juridica->direccion=$request->direccionInputJuridico;
                //$juridica->actividadPrincipal=$request->actividadPrincipal;
                $juridica->codTipoPersonaJuridica = $request->codTipoPersonaJuridica;
                $juridica->representante = $request->representante;
                
                $juridica->numeroSociosHombres=$request->numeroSociosHombres;
                $juridica->numeroSociosMujeres=$request->numeroSociosMujeres;
                
                $juridica->save();
 
                $mensajeLlegada = "editada";
                
            }

            
            db::commit();

            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
                ->with('datos','Información de "'.$juridica->razonSocial.'" '.$mensajeLlegada.' exitosamente.');
           
        }catch(\Throwable $th){
            DB::rollBack();
            Debug::mensajeError('Personapoblacioncontroller agregareditar persona juridica ',$th);
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
             
        }



    }

    


    //funcion servicio, será consumida solo por javascript
    public function listarPersonasJuridicas($codPob){
        $vector = [];
        $listaPersonas = PersonaJuridicaPoblacion::where('codPoblacionBeneficiaria','=',$codPob)->get();

        for ($i=0; $i < count($listaPersonas) ; $i++) { 
            $itemDet = $listaPersonas[$i];
            array_push($vector,$itemDet);            
        }
        return $vector  ;
    }


        /* cadena llega en formato 52*15
    donde 52 es el codigo de la poblacion beneficiaria
    15 es el codigo de la persona natural a eliminar de esta

    */
    function quitarJuridicaDeLaPoblacion($cadena){
        try {
            db::beginTransaction();
            $vector = explode('*',$cadena);
            $codPoblacionBeneficiaria = $vector[0];
            $codPersonaJuridica = $vector[1];

            $poblacion = PoblacionBeneficiaria::findOrFail($codPoblacionBeneficiaria);
            $persona = PersonaJuridicaPoblacion::findOrFail($codPersonaJuridica);

            $relacionesExistentes = RelacionPersonaJuridicaPoblacion::
                where('codPoblacionBeneficiaria','=',$codPoblacionBeneficiaria)
                ->where('codPersonaJuridica','=',$codPersonaJuridica)
                ->get();


            if(count($relacionesExistentes) == 0){
                $mensaje = "ERROR: La persona no pertenece a esta población beneficiaria, ya fue eliminada anteriormente.";
            }else{
                //ahora sí quitamos a la relacion
                $relacion = $relacionesExistentes[0];
                $relacion->delete();
                $mensaje = 'Se ha eliminado exitosamente a '.$persona->razonSocial.' de la población beneficiaria '.$poblacion->descripcion.'.';

            }
            db::commit();

            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
                ->with('datos',$mensaje);


        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER : quitarJuridicaDeLaPoblacion ',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena);
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }



    }

    public function ConsultarAPISunatRUC($ruc){
        try {
                
            $token = Configuracion::tokenParaAPISunat;
            $linkConsulta = "https://dniruc.apisperu.com/api/v1/ruc/".$ruc."?token=".$token;
            
            $resultado = file_get_contents($linkConsulta);
            if(str_contains($resultado,"ruc"))
                return $resultado;
            else //no encontrado
                return "1";
            return $resultado;//retorna el json
        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER' , $th);
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             $ruc
                                                            );
            return json_encode("ERROR");
        }

    }


    public function agregarJuridicaExistenteAPoblacion(Request $request){
        try{

            db::beginTransaction();
            $poblacion = PoblacionBeneficiaria::findOrFail($request->codPoblacionBeneficiaria);
            $persona = PersonaJuridicaPoblacion::findOrFail($request->codJuridicaBuscar);

            $relacion = new RelacionPersonaJuridicaPoblacion();
            $relacion->codPersonaJuridica = $persona ->codPersonaJuridica;
            $relacion->codPoblacionBeneficiaria = $poblacion->codPoblacionBeneficiaria;
            $relacion->save();

            db::commit();
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
                ->with('datos','Persona jurídica "'.$persona->razonSocial.'" añadida a la población beneficiaria');
        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER : agregarJuridicaExistenteAPoblacion',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                            app('request')->route()->getAction(),
                                                            json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$poblacion->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }


#endregion

#region ACTIVIDADES

    /*  */
    public function guardarActividadesDePersona(Request $request){
  
        try {
            db::beginTransaction();
            
            $listaActividadesExistentes = ActividadPrincipal::All(); 
            if($request->naturalOJuridica=="natural"){


                RelacionPersonaNaturalActividad::where('codPersonaNatural','=',$request->codPersona)->delete();

                foreach ($listaActividadesExistentes as $actividad) {
                    if( $request->get('Actividad'.$actividad->codActividadPrincipal) == "on"){
                        Debug::mensajeSimple('Actividad'.$actividad->codActividadPrincipal);
                        $relacion = new RelacionPersonaNaturalActividad();
                        $relacion->codActividadPrincipal = $actividad->codActividadPrincipal;
                        $relacion->codPersonaNatural = $request->codPersona;
                        $relacion->save();
                    }
                }

            }else{
                RelacionPersonaJuridicaActividad::where('codPersonaJuridica','=',$request->codPersona)->delete();
                
                foreach ($listaActividadesExistentes as $actividad) {
                    if( $request->get('Actividad'.$actividad->codActividadPrincipal) == "on"){
                        Debug::mensajeSimple('Actividad'.$actividad->codActividadPrincipal);
                        $relacion = new RelacionPersonaJuridicaActividad();
                        $relacion->codActividadPrincipal = $actividad->codActividadPrincipal;
                        $relacion->codPersonaJuridica = $request->codPersona;
                        $relacion->save();
                    }
                }
            }

            db::commit();
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
            ->with('datos','Se guardaron las actividades de la persona');

            
        } catch (\Throwable $th) {
            Debug::mensajeError('PERSONA POBLACION CONTROLLER : guardarActividadesDePersona',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                            app('request')->route()->getAction(),
                                                            json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionProyectos.verPoblacionBeneficiaria',$request->codPoblacionBeneficiaria)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }
    }
#endregion


}
