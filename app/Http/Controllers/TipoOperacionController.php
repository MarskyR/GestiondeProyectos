<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\TipoOperacion;
use Illuminate\Http\Request;

class TipoOperacionController extends Controller
{
    
    public function listar(){

        $lista = TipoOperacion::orderBy('codTipoDocumento','ASC')->get();
        return view('Operaciones.ListarTipoOperacion',compact('lista'));

    }
}
