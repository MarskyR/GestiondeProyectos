<?php

namespace App\Http\Controllers;
use App\Configuracion;
use App\Debug;
use App\DetalleDJGastosVarios;
use App\DJGastosVarios;
use App\Empleado;
use App\ErrorHistorial;
use App\Http\Controllers\Controller;
use App\Moneda;
use App\Numeracion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DJGastosVariosController extends Controller
{
    const PAGINATION = 20;


    
    public function listarDeEmpleado(Request $request){
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';

        $listaDJ = DJGastosVarios::where('codEmpleado','=',Empleado::getEmpleadoLogeado()->codEmpleado);
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaDJ=$listaDJ->where('fechaHoraCreacion','>',$fechaInicio)->where('fechaHoraCreacion','<',$fechaFin);
        }
        $listaDJ =  $listaDJ->orderBy('fechaHoraCreacion','DESC');
        $listaDJ=$listaDJ->paginate($this::PAGINATION);

        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;

        return view('DJ.Varios.ListarDJVar',compact('listaDJ','fechaInicio','fechaFin'));
    }

    public function crearDJVar(){

        

        $listaMonedas = Moneda::All();
        return view('DJ.Varios.CrearDJVar',compact('listaMonedas'));
    }


    public function store(Request $request){
        try {
            DB::beginTransaction();   
            $dj = new DJGastosVarios();
            $dj->fechaHoraCreacion = Carbon::now();
            $dj->domicilio = $request->domicilio;
            $dj->importeTotal = $request->totalMostrado;
            $dj->codMoneda = $request->ComboBoxMoneda;
            $dj->codEmpleado = Empleado::getEmpleadoLogeado()->codEmpleado;
            $dj->codigoCedepas = DJGastosVarios::calcularCodigoCedepasLibre();
            Numeracion::aumentarNumeracionDJ_VAR();
            $dj->save();
                
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
            

            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) {
                $detalle=new DetalleDJGastosVarios();
                $detalle->codDJGastosVarios=       $dj->codDJGastosVarios; //ultimo insertad

                $fechaDet = $request->get('colFecha'.$i);
                    //DAMOS VUELTA A LA FECHA
                                                // AÑO                  MES                 DIA
                $detalle->fecha=   substr($fechaDet,6,4).'-'.substr($fechaDet,3,2).'-'.substr($fechaDet,0,2);
                

                $detalle->concepto=            $request->get('colConcepto'.$i);    
                $detalle->importe  =          $request->get('colImporte'.$i);    

                $detalle->save();
                                
                $i=$i+1;
            }    
            
            DB::commit();  
            return redirect()
                ->route('DJVarios.Empleado.Listar')
                ->with('datos','Se ha creado la Declaración Jurada '.$dj->codigoCedepas.', ya puede descargarla en opciones.' );
            
        }catch(\Throwable $th){
            Debug::mensajeError('DJ GAST VARIOS CONTROLLER : STORE',$th);
            
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('DJVarios.Empleado.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
                
        }
        
    }

    public function ver($codDJ){
        $DJ = DJGastosVarios::findOrFail($codDJ);
        $listaDetalles = DetalleDJGastosVarios::where('codDJGastosVarios','=',$DJ->codDJGastosVarios)->get();
        
        if(!$DJ->codEmpleado == Empleado::getEmpleadoLogeado()->codEmpleado)
            return redirect()->route('DJVarios.Empleado.Listar')
                ->with('datos','Las declaraciones juradas solo pueden ser vistas por la persona que las creó.');
        
        return view('DJ.Varios.VerDJVar',compact('DJ','listaDetalles'));


    }

    public function descargarPDF($codDJ){
        $DJ = DJGastosVarios::findOrFail($codDJ);
        $pdf = $DJ->getPDF();
        return $pdf->download('DJ Varios '.$DJ->codigoCedepas.'.Pdf');
    }   
    
    public function verPDF($codDJ){
        $DJ = DJGastosVarios::findOrFail($codDJ);
        $pdf = $DJ->getPDF();
        return $pdf->stream('DJ Varios '.$DJ->codigoCedepas.'.Pdf');
    }


}
