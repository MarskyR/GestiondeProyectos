<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\TipoFinanciamiento;
use Illuminate\Http\Request;

class TipoFinanciamientoController extends Controller
{
    public function listar(){

        $tiposFinanciamiento = TipoFinanciamiento::All();
        
        return view('TiposFinanciamiento.ListarTiposFinanciamientos',compact('tiposFinanciamiento'));



    }


    public function crear(){

        return view('TiposFinanciamiento.CrearTipoFinanciamiento');

    }


    public function guardar(Request $request){
        $nuevo = new TipoFinanciamiento(); 
        $nuevo->nombre = $request->nombre;
        $nuevo->save();

        return redirect()->route('TipoFinanciamiento.listar')->with('datos','¡Tipo de financiamiento añadido exitosamente!');

    }



    public function editar($codTipoFinanciamiento){

        $tipoFinanciamiento = TipoFinanciamiento::findOrFail($codTipoFinanciamiento);
        return view('TiposFinanciamiento.EditarTipoFinanciamiento',compact('tipoFinanciamiento'));

    }

    public function actualizar(Request $request){

        //return "a";
        $nuevo =  TipoFinanciamiento::findOrFail($request->codTipoFinanciamiento); 
        $nuevo->nombre = $request->nombre;
        $nuevo->save();

        return redirect()->route('TipoFinanciamiento.listar')
            ->with('datos','¡Tipo de financiamiento añadido exitosamente!');

    }

    public function eliminar($codTipoFinanciamiento){
        $registro =  TipoFinanciamiento::findOrFail($codTipoFinanciamiento); 
        $nombre = $registro->nombre;
        $registro->delete();

        return redirect()->route('TipoFinanciamiento.listar')
            ->with('datos','¡Tipo de financiamiento '.$nombre.' ELIMINADO exitosamente!');


    }


}
