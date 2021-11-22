<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Configuracion;
use App\Empleado;
use App\LogeoHistorial;
use Carbon\Carbon;

class UserController extends Controller
{


    public function logearse(Request $request){
        /*  return $request; */
        //error_log('aaaaaaaa ');
        $data=$request->validate([
            'usuario'=>'required',
            'password'=>'required'
        ],
        [
            'usuario.required'=>'Ingrese Usuario',
            'password.required'=>'Ingrese Contraseña',
        ]);
        
            $usuario=$request->get('usuario');
            $query=User::where('usuario','=',$usuario)->get();
            
            if($query->count()!=0){
                $hashp=$query[0]->password; // guardamos la contraseña cifrada de la BD en hashp
                $password=$request->get('password');    //guardamos la contraseña ingresada en password
            
                if(password_verify($password,$hashp))       //comparamos con el metodo password_verifi ??¡ xdd
                {
                        // Preguntamos si es admin o no
                    //LogeoHistorial::registrarLogeo();
                    if($usuario=='admin')
                    {

                        //SI INGRESÓ EL ADMIN 
                        if(Auth::attempt($request->only('usuario','password'))){ //este attempt es para que el Auth se inicie
                            LogeoHistorial::registrarLogeo();
                            return redirect()->route('user.home');
                        }
                    }//si es user normal
                    else{
                        if(Auth::attempt($request->only('usuario','password'))){
                            LogeoHistorial::registrarLogeo();
                            return redirect()->route('user.home');
                        }
    
                    }
                    
                    
                
                }
                else{
                    return back()->withErrors(['password'=>'Contraseña no válida'])->withInput([request('password')]);
                }                
            }
            else
            {
                return back()->withErrors(['usuario'=>'Usuario no válido'])->withInput([request('usuario')]);
            }
    }

    public function cerrarSesion(){
        Auth::logout();
        /*  session(['token' => '-2']); */
        return redirect()->route('user.verLogin');  
    }

    public function verLogin(){
        if(Empleado::getEmpleadoLogeado()!=""){//si hay alguien logeado
            return redirect()->route('user.home');
        }


        return view('LoginCedepas');
    }


    public function home(){
        if(is_null(Auth::id()))
            return redirect()->route('user.verLogin');

        
        return view('Bienvenido');
    }



    public function paginaEnMantenimiento(){
    
        if(Configuracion::estamosEnMantenimiento)
            return view('Mantenimiento');
        
        return redirect()->route('user.verLogin');
        
    }
 
}
