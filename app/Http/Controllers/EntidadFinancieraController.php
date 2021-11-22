<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Configuracion;
use App\EntidadFinanciera;
class EntidadFinancieraController extends Controller
{
    public function listar(){
        $entidadesFinancieras = EntidadFinanciera::All();

        return view('Financieras.ListarFinancieras',compact('entidadesFinancieras'));

    }


    public function crear(){

        return view('Financieras.CrearFinanciera');

    }

    public function guardar(Request $request){
        $entidad = new EntidadFinanciera();
        $entidad->nombre = $request->nombre;
        $entidad->save();

        return redirect()->route('EntidadFinanciera.listar')->with('datos','¡Entidad creada exitosamente!');

    }

    public function eliminar($codEntidadFinanciera){
        $entidad = EntidadFinanciera::findOrFail($codEntidadFinanciera);
        $nombre = $entidad->nombre;
        $entidad->delete();
        return redirect()->route('EntidadFinanciera.listar')->with('datos','¡Entidad '.$nombre.' eliminada exitosamente!');

    }


    public function editar($codEntidadFinanciera){
        $entidadFinanciera = EntidadFinanciera::findOrFail($codEntidadFinanciera);

        return view('Financieras.EditarFinanciera',compact('entidadFinanciera'));
    }

    public function actualizar(Request $request){
        $entidad = EntidadFinanciera::findOrFail($request->codEntidadFinanciera);
        $entidad->nombre = $request->nombre;
        $entidad->save();

        return redirect()->route('EntidadFinanciera.listar')->with('datos','¡Entidad actualizada exitosamente!');

    }


}
