<?php

namespace App\Http\Controllers;
use App\Configuracion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DJGastosViaticos;
USE App\Empleado;
use App\Moneda;
use App\Debug;
use App\DetalleDJGastosMovilidad;
use App\DJGastosMovilidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\ErrorHistorial;
use Exception;
use App\DetalleDJGastosViaticos;
use App\Numeracion;


class DJGastosViaticosController extends Controller
{
    
    const PAGINATION = 20;

    public function listarDeEmpleado(Request $request){


        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';



        $listaDJ = DJGastosViaticos::where('codEmpleado','=',Empleado::getEmpleadoLogeado()->codEmpleado);
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaDJ=$listaDJ->where('fechaHoraCreacion','>',$fechaInicio)->where('fechaHoraCreacion','<',$fechaFin);
        }

        $listaDJ =  $listaDJ->orderBy('fechaHoraCreacion','DESC');
        $listaDJ=$listaDJ->paginate($this::PAGINATION);

        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;

        return view('DJ.Viaticos.ListarDJVia',compact('listaDJ','fechaInicio','fechaFin'));
    }

    public function crearDJVia(){
        $listaMonedas = Moneda::All();
        return view('DJ.Viaticos.CrearDJVia',compact('listaMonedas'));
    }

    public function Guardar(Request $request){
        
        try {
            DB::beginTransaction();   
            $dj = new DJGastosViaticos();
            $dj->fechaHoraCreacion = Carbon::now();
            $dj->domicilio = $request->domicilio;
            $dj->importeTotal = $request->total;
            $dj->codMoneda = $request->ComboBoxMoneda;
            $dj->codEmpleado = Empleado::getEmpleadoLogeado()->codEmpleado;
            
            $dj->codigoCedepas = DJGastosViaticos::calcularCodigoCedepasLibre();
            Numeracion::aumentarNumeracionDJ_VIA();
            

            
            $dj->save();
                
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
             


            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) {
                $detalle=new DetalleDJGastosViaticos();
                $detalle->codDJGastosViaticos=       $dj->codDJGastosViaticos; //ultimo insertad

                $fechaDet = $request->get('colFecha'.$i);
                    //DAMOS VUELTA A LA FECHA
                                                // AÑO                  MES                 DIA
                $detalle->fecha=   substr($fechaDet,6,4).'-'.substr($fechaDet,3,2).'-'.substr($fechaDet,0,2);
                
                $detalle->lugar=              $request->get('colLugar'.$i);
                
                $detalle->montoDesayuno=            $request->get('colMontoDesayuno'.$i);    
                $detalle->montoAlmuerzo=            $request->get('colMontoAlmuerzo'.$i);    
                $detalle->montoCena=            $request->get('colMontoCena'.$i);    
                
                $detalle->totalDia=  $detalle->montoDesayuno + $detalle->montoAlmuerzo + $detalle->montoCena;     
                
                $detalle->save();
                                
                $i=$i+1;
            }    
            
            DB::commit();  
            return redirect()
                ->route('DJViaticos.Empleado.Listar')
                ->with('datos','Se ha creado la Declaración Jurada '.$dj->codigoCedepas.', ya puede descargarla en opciones.' );
        }catch(\Throwable $th){
            
            Debug::mensajeError('DJ GAST VIATICOS CONTROLLER : STORE',$th);
            
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('DJViaticos.Empleado.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }

    public function ver($codDJ){
        $DJ = DJGastosViaticos::findOrFail($codDJ);
        $listaDetalles = DetalleDJGastosViaticos::where('codDJGastosViaticos','=',$DJ->codDJGastosViaticos)->get();
        
        if(!$DJ->codEmpleado == Empleado::getEmpleadoLogeado()->codEmpleado)
            return redirect()->route('DJViaticos.Empleado.Listar')
                ->with('datos','Las declaraciones juradas solo pueden ser vistas por la persona que las creó.');
        
        return view('DJ.Viaticos.VerDJVia',compact('DJ','listaDetalles'));


    }

    public function descargarPDF($codDJ){
        $DJ = DJGastosViaticos::findOrFail($codDJ);
        $pdf = $DJ->getPDF();
        return $pdf->download('DJ Viaticos '.$DJ->codigoCedepas.'.Pdf');
    }   
    
    public function verPDF($codDJ){
        $DJ = DJGastosViaticos::findOrFail($codDJ);
        $pdf = $DJ->getPDF();
        return $pdf->stream('DJ Viaticos '.$DJ->codigoCedepas.'.Pdf');
    }

    /* NO TIENE UPDATE ESTE CONTROLLER, NI DEBE TENERLO */


}
