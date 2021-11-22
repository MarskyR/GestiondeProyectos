<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\TipoPersonaJuridica;
use Illuminate\Http\Request;

class TipoPersonaJuridicaController extends Controller
{
    const PAGINATION=10;

    public function listarTipos(Request $request){
        $nombreBuscar=$request->nombreBuscar;

        $tipos=TipoPersonaJuridica::where('nombre','like','%'.$nombreBuscar.'%')->paginate($this::PAGINATION);
        return view('Proyectos.Poblacion.TipoPersonaJuridica.IndexTipo',compact('tipos','nombreBuscar'));
    }

    public function crearTipo(){
        return view('Proyectos.Poblacion.TipoPersonaJuridica.CreateTipo');
    }

    public function guardarCrearTipo(Request $request){
        
        $tipo=new TipoPersonaJuridica();
        $tipo->nombre=$request->descripcion;
        $tipo->siglas=$request->siglas;
        $tipo->save();

        return redirect()->route('GestiónTipoPersonaJuridica.Listar');
    }

    public function editarTipo($id){
        $tipo=TipoPersonaJuridica::find($id);
        return view('Proyectos.Poblacion.TipoPersonaJuridica.EditTipo',compact('tipo'));
    }

    public function guardarEditarTipo(Request $request){

        $tipo=TipoPersonaJuridica::find($request->codTipoPersonaJuridica);
        $tipo->nombre=$request->descripcion;
        $tipo->siglas=$request->siglas;
        $tipo->save();

        return redirect()->route('GestiónTipoPersonaJuridica.Listar');
    }

    public function eliminarTipo($id){
        $tipo=TipoPersonaJuridica::find($id);
        $tipo->delete();

        return redirect()->route('GestiónTipoPersonaJuridica.Listar');
        //return response()->json();
    }

    
}
