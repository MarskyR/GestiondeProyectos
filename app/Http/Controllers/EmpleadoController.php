<?php

namespace App\Http\Controllers;

use App\Area;
use App\Configuracion;
use App\Empleado;
use App\PeriodoEmpleado;
use App\Puesto;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Proyecto;
use App\Sede;
use App\Debug;
use App\ErrorHistorial;
use App\Fecha;
use App\ProyectoContador;
use Throwable;

class EmpleadoController extends Controller
{
    const PAGINATION = 100;

    public function listarEmpleados(Request $request){
        $dniBuscar=$request->dniBuscar;
        $nombreBuscar=$request->nombreBuscar;
        $empleados = Empleado::where('activo','=',1)
            ->where('dni','like',$dniBuscar.'%')
            ->where(DB::raw('CONCAT(nombres," ",apellidos)'),'like', '%'.$nombreBuscar.'%')
            ->orderBy('codEmpleado','ASC')
            ->paginate($this::PAGINATION);
        $listaSedes = Sede::All();

        return view('Empleados.Index',compact('empleados','dniBuscar','nombreBuscar','listaSedes'));

    }





    public function crearEmpleado(){
        //$areas=Area::all();
        //$proyectos = Proyecto::All();
        $puestos=Puesto::where('estado','!=',0)->get();
        $sedes=Sede::all();
        return view('Empleados.Create',compact('puestos','sedes'));
    }
    /*
    public function listarPuestos(Request $request,$id){
        $puestos=Puesto::where('codArea','=',$id)->get();
        return response()->json(['puestos'=>$puestos]);
    }
    */


    public function guardarCrearEmpleado(Request $request){
        try{
            db::beginTransaction();
            
            /* Validamos que no hay otro usuario con el mismo dni */
            $empleadoEncontrado = Empleado::buscarPorDNI($request->DNI);
            if($empleadoEncontrado!="")
                return redirect()->route('GestionUsuarios.Listar')
                    ->with('datos','ERROR: Ya existe un empleado registrado con el DNI "'.$empleadoEncontrado->dni.'"');
        
            /* Validamos que no hay otro usuario con el mismo nombre de usuario*/
            $usuarioEncontrado = User::buscarPorUsuario($request->usuario);
            if($usuarioEncontrado!="")
                return redirect()->route('GestionUsuarios.Listar')
                    ->with('datos','ERROR: Ya existe un empleado registrado con el nombre de usuario "'.$usuarioEncontrado->usuario.'"');
        
            
            
            //Usuario
            $usuario=new User();
            $usuario->usuario=$request->usuario;
            $usuario->password=hash::make($request->contraseña);
            $usuario->isAdmin=0;
            $usuario->save();

            //Empleado
            $empleado=new Empleado();
            $empleado->codUsuario=$usuario->codUsuario;
            $empleado->nombres=$request->nombres;
            $empleado->apellidos=$request->apellidos;
            $empleado->correo = $request->correo;
            $empleado->activo=1;
            $empleado->codigoCedepas=$request->codigo;
            $empleado->dni=$request->DNI;
            $empleado->codPuesto=$request->codPuesto;
            $empleado->fechaRegistro=date('y-m-d');   
            $empleado->codSede=$request->codSede;

            $empleado->sexo=$request->sexo;
            $empleado->fechaNacimiento=substr($request->fechaNacimiento,6,4).'-'.substr($request->fechaNacimiento,3,2).'-'.substr($request->fechaNacimiento,0,2);
            $empleado->nombreCargo=$request->cargo;
            $empleado->direccion=$request->direccion;
            $empleado->nroTelefono=$request->telefono;
            
            $empleado->save();

            db::commit();
            return redirect()->route('GestionUsuarios.Listar')
                ->with('datos','Empleado '.$empleado->getNombreCompleto().' registrado exitosamente');
            
        }catch (\Throwable $th) {
            Debug::mensajeError(' EMPLEADO CONTROLLER guardarcrearempleado' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionUsuarios.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
                
        }
         



    }

    public function editarUsuario($id){
        $empleado=Empleado::find($id);
        $usuario=$empleado->usuario();
        return view('Empleados.EditUsuario',compact('usuario','empleado'));
    }

    public function editarEmpleado($id){
        $puestos=Puesto::where('estado','!=',0)->get();
        $sedes=Sede::all();
        $empleado=Empleado::find($id);
        //$areas=Area::all();
        //$puestos=Puesto::all();
        return view('Empleados.EditEmpleado',compact('empleado','puestos','sedes'));
    }

    public function guardarEditarUsuario(Request $request){
       
        //Usuario
        //$usuario=new User();
        $empleado=Empleado::find($request->codEmpleado);
        $usuario=$empleado->usuario();
        $usuario->usuario=$request->usuario;
        $usuario->password=hash::make($request->password1);
        //$usuario->isAdmin=0;
        $usuario->save();

        return redirect()->route('GestionUsuarios.Listar')->with('datos',"La contraseña se ha actualizado.");
    }
    public function guardarEditarEmpleado(Request $request){
        $empleado=Empleado::find($request->codEmpleado);

        $empleado->nombres=$request->nombres;
        $empleado->apellidos=$request->apellidos;
        $empleado->codigoCedepas=$request->codigo;
        $empleado->dni=$request->DNI;
        $empleado->codPuesto=$request->codPuesto; 
        $empleado->codSede=$request->codSede;

        $empleado->sexo=$request->sexo;
        $empleado->fechaNacimiento=substr($request->fechaNacimiento,6,4).'-'.substr($request->fechaNacimiento,3,2).'-'.substr($request->fechaNacimiento,0,2);
        $empleado->nombreCargo=$request->cargo;
        $empleado->direccion=$request->direccion;
        $empleado->nroTelefono=$request->telefono;

        $empleado->save();

        return redirect()->route('GestionUsuarios.Listar')->with('datos','Empleado "'.$empleado->getNombreCompleto().'"actualizado.');
    }

    public function cesarEmpleado($id){
        $empleado=Empleado::find($id);
        $empleado->fechaDeBaja=date('y-m-d');
        $empleado->activo=0;
        $empleado->save();

        $usuario=$empleado->usuario();
        $usuario->delete();
        return redirect()->route('GestionUsuarios.ListarEmpleados');
    }


    

    public function verMisDatos(){
        $empleado=Empleado::getEmpleadoLogeado();

        return view('Empleados.MisDatos',compact('empleado'));

    }

    public function cambiarContraseña(){
        $empleado=Empleado::getEmpleadoLogeado();

        return view('Empleados.CambiarContraseña',compact('empleado'));
    }


    public function guardarContrasena(Request $request){
         
        try {
            $empleado=Empleado::find($request->codEmpleado);
            $hashp = $empleado->usuario()->password;

            if(!password_verify($request->contraseñaActual1,$hashp))
                return redirect()->route('GestionUsuarios.cambiarContraseña')
                    ->with('datos','La contraseña actual que ingresó no es correcta.');

            Db::beginTransaction();
            $usuario=$empleado->usuario();
            $usuario->password=hash::make($request->contraseña);
            $usuario->save();

            DB::commit();
            return redirect()->route('GestionUsuarios.cambiarContraseña')
                ->with('datos','Se ha actualizado su contraseña exitosamente.');

        } catch (\Throwable $th) {
            Debug::mensajeError('EMPLEAADO CONTROLLER guardarContraseña',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('GestionUsuarios.cambiarContraseña')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
    }



    public function guardarDPersonales(Request $request){
        try {
            
            db::beginTransaction();
            $empleado=Empleado::find($request->codEmpleado);
            $empleado->correo=$request->correo;
            $empleado->sexo=$request->sexo;
            $fechaNacimiento = Fecha::formatoParaSQL($request->fechaNacimiento);

            $empleado->fechaNacimiento=$fechaNacimiento;

            $empleado->nombreCargo=$request->cargo;
            $empleado->direccion=$request->direccion;
            $empleado->nroTelefono=$request->telefono;
            
            $empleado->save();
            
            DB::commit();
            return redirect()->route('GestionUsuarios.verMisDatos')->with('datos','Datos actualizados exitosamente.');
        } catch (\Throwable $th) {
            
            Debug::mensajeError('EMPLEAADO CONTROLLER guardarDPersonales',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            
            return redirect()->route('GestionUsuarios.verMisDatos')->with('datos',Configuracion::getMensajeError($codErrorHistorial));

        }


    }


    /* cadena llega en formato 15*77
        15 es el codigo del empleado contador
        77 el codigo del proyecto a asignar
    */

    /* ESTA FUNCION SIRVE PARA CREAR COMO PARA DESTRUIR */
    public function asignarProyectoAContador($cadena){
        try {
            db::beginTransaction();
            $vector = explode('*',$cadena);
            $codEmpleadoContador = $vector[0];
            $codProyecto = $vector[1];

            /* VERIFICAMOS SI YA EXISTE UNA RELACION  */
            $lista = ProyectoContador::where('codEmpleadoContador','=',$codEmpleadoContador)->where('codProyecto','=',$codProyecto)->get();
            if(count($lista)>0){//YA EXISTE, lo destruimos
                $relacion = $lista[0];
                $relacion->delete();
                $retorno = 1;
            }else{//NO EXISTE, CREAREMOS UNO NUEVO
                $relacion = new ProyectoContador();
                $relacion->codEmpleadoContador = $codEmpleadoContador;
                $relacion->codProyecto = $codProyecto;
                $relacion->save();
                $retorno = 2;
            }

            db::commit();
            
            return $retorno;
        } catch (\Throwable $th) {
            Debug::mensajeError(' EMPLEADO CONTROLLER asignarProyecto contador' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena);
            return 0;
        }


    }


    public function asignarContadorATodosProyectos($codEmpleadoContador){
        try{
            db::beginTransaction();
            ProyectoContador::where('codEmpleadoContador','=',$codEmpleadoContador)->delete();

            $listaProyectos = Proyecto::getProyectosActivos();
            foreach ($listaProyectos as $proy ) {
                $relacion = new ProyectoContador();
                $relacion->codProyecto = $proy->codProyecto;
                $relacion->codEmpleadoContador = $codEmpleadoContador;
                $relacion->save();

            }

            db::commit();
            return 1;
        } catch (\Throwable $th) {
            Debug::mensajeError(' EMPLEADO CONTROLLER asignar contador a todos los proyectos' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codEmpleadoContador);
            return 0;
        }

    }

    public function quitarContadorATodosProyectos($codEmpleadoContador){
        try{
            db::beginTransaction();
            ProyectoContador::where('codEmpleadoContador','=',$codEmpleadoContador)->delete();

            db::commit();
            return 1;
        } catch (\Throwable $th) {
            Debug::mensajeError(' EMPLEADO CONTROLLER quitar contador a todos los proyectos' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$codEmpleadoContador);
            return 0;
        }


    }

    public function verProyectosContador($codEmpleadoContador){
        $empleado = Empleado::findOrFail($codEmpleadoContador);
        //$listaRelacionesProyectos = ProyectoContador::where('codEmpleadoContador','=',$codEmpleadoContador)->get();
        $listaProyectos = Proyecto::All();

        return view('Empleados.AsignarProyectosAContador',compact('empleado','listaProyectos'));
        

    }


    /* Funcion ejecutada desde JS con un get */
    /* La cadena tiene el formato 15*2
    Donde 15 esel codigo del empleado contador 
    donde 2 es el codigo de la nueva sede
    */
    public function cambiarSedeAContador($cadena){
        try {
            db::beginTransaction();            
            $vector = explode('*',$cadena);
            $codEmpleado = $vector[0];
            $codSedeContador = $vector[1];

            $empleado = Empleado::findOrFail($codEmpleado);
            $empleado->codSedeContador = $codSedeContador;
            $empleado->save();

            db::commit();

            return TRUE;
        } catch (\Throwable $th) {
            
            Debug::mensajeError('PROYECTO CONTROLLER cambiarSedeAContador',$th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th, app('request')->route()->getAction(),$cadena);
            return FALSE;
        }
    }


    /* 
    Funcion que solo usaré una vez (espero xd)
        recorre todos los empleados y formatea sus nombres y apellidos
    
       convertir todos los nombres "DIEGO ERNESTO VIGO BRIONES" o "diego ernesto vigo briones" 
        a "Diego Ernesto Vigo Briones"
    */
    public function cambiarNombresEmpleadosAFormatoBonito(){
        return "funcion desactivada";
        try{

            db::beginTransaction();
            $cadena = "";
            $listaEmpleados = Empleado::All();
            foreach ($listaEmpleados as $emp ) {
                $emp->nombres = ucwords(mb_strtolower($emp->nombres));
                $emp->apellidos = ucwords(mb_strtolower($emp->apellidos));
                $emp->save();
            }
            db::commit();
            
            return "FUNCION cambiarNombresEmpleadosAFormatoBonito ejecutada exitosamente. ".$cadena;
        }catch(Throwable $th){
            Debug::mensajeError('EMPLEADO CONTROLLER cambiarNombresEmpleadosAFormatoBonito',$th);
            db::rollback();

        }


    }

}
