<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    const PAGINATION = '20';

    public function listarUnidades(Request $request){
        //$dniBuscar=$request->dniBuscar;
        $unidades = UnidadMedida::paginate($this::PAGINATION);
        return view('UnidadMedida.ListarUnidades',compact('unidades'));
    }

    public function crearUnidad(){
        //$puestos=UnidadMedida::findOrFail($id);
        return view('UnidadMedida.CrearUnidad');
    }


    public function store(Request $request){
        
        $unidad=new UnidadMedida();
        $unidad->nombre=$request->nombre;
        $unidad->save();

        return redirect()->route('GestiónUnidadMedida.listar')
            ->with('datos','Se registro la Unidad de Medida Nº'.$unidad->codUnidadMedida);
    }

    public function editarUnidad($id){
        $unidad=UnidadMedida::findOrFail($id);
        return view('UnidadMedida.EditarUnidad',compact('unidad'));
    }
    public function update(Request $request){
        $unidad=UnidadMedida::findOrFail($request->codUnidadMedida);
        $unidad->nombre=$request->nombre;
        $unidad->save();

        return redirect()->route('GestiónUnidadMedida.listar')->with('datos','Se editó la Unidad de Medida Nº'.$unidad->codUnidadMedida);
    }

    public function delete($id){
        $unidad=UnidadMedida::findOrFail($id);
        $unidad->delete();

        return redirect()->route('GestiónUnidadMedida.listar');
    }
}
