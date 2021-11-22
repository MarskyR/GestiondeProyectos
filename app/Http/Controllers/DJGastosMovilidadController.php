<?php

namespace App\Http\Controllers;
use App\Configuracion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Debug;
use App\DetalleDJGastosMovilidad;
use App\DJGastosMovilidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\ErrorHistorial;
use Exception;
use App\Moneda;
use App\Empleado;
use App\Numeracion;

class DJGastosMovilidadController extends Controller
{
    const PAGINATION = 20;

    public function listarDeEmpleado(Request $request){


        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';



        $listaDJ = DJGastosMovilidad::where('codEmpleado','=',Empleado::getEmpleadoLogeado()->codEmpleado);
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $listaDJ=$listaDJ->where('fechaHoraCreacion','>',$fechaInicio)->where('fechaHoraCreacion','<',$fechaFin);
        }

        $listaDJ =  $listaDJ->orderBy('fechaHoraCreacion','DESC');
        $listaDJ=$listaDJ->paginate($this::PAGINATION);

        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;


        /*
        //Este código es para abrir una ventana emergente al terminar de crear la DJ, Sí funciona pero al activarlo el antivirus/chrome bloquea la ventana emergente
        
        $mensajeLlegadaDatos = "";
        if( !is_null(session('datos') ) )
            $mensajeLlegadaDatos = session('datos');

        $nuevaDJ="0";
        if(str_contains($mensajeLlegadaDatos,'Se ha creado la Declaración jurada ') ){
            $codigoCedepasNueva = substr($mensajeLlegadaDatos,36,10);
            Debug::mensajeSimple('A'.$codigoCedepasNueva);
            $nuevaDJ = DJGastosMovilidad::where('codigoCedepas','=',$codigoCedepasNueva)->get()[0]->codDJGastosMovilidad;
           
        }
         */

        return view('DJ.Movilidad.ListarDJMov',compact('listaDJ','fechaInicio','fechaFin'));
    }

    public function crearDJMov(){

        $listaMonedas = Moneda::All();
        return view('DJ.Movilidad.CrearDJMov',compact('listaMonedas'));
    }

    public function Guardar(Request $request){

        
        try {
            DB::beginTransaction();   
            $dj = new DJGastosMovilidad();
            $dj->fechaHoraCreacion = Carbon::now();
            $dj->domicilio = $request->domicilio;
            $dj->importeTotal = $request->total;
            $dj->codMoneda = $request->ComboBoxMoneda;
            $dj->codEmpleado = Empleado::getEmpleadoLogeado()->codEmpleado;
            
            $dj->codigoCedepas = DJGastosMovilidad::calcularCodigoCedepasLibre();
            Numeracion::aumentarNumeracionDJ_MOV();
            

            
            $dj->save();
                
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
             


            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) {
                $detalle=new DetalleDJGastosMovilidad();
                $detalle->codDJGastosMovilidad=       $dj->codDJGastosMovilidad; //ultimo insertad

                
                $fechaDet = $request->get('colFecha'.$i);
                    //DAMOS VUELTA A LA FECHA
                                                // AÑO                  MES                 DIA
                $detalle->fecha=   substr($fechaDet,6,4).'-'.substr($fechaDet,3,2).'-'.substr($fechaDet,0,2);
                
                $detalle->lugar=              $request->get('colLugar'.$i);
                $detalle->detalle=            $request->get('colDetalle'.$i);    
                $detalle->importe  =          $request->get('colImporte'.$i);    

                $detalle->save();
                                
                $i=$i+1;
            }    
            
            DB::commit();  
            return redirect()
                ->route('DJMovilidad.Empleado.Listar')
                ->with('datos','Se ha creado la Declaración Jurada '.$dj->codigoCedepas.', ya puede descargarla en opciones.' );
        }catch(\Throwable $th){
            
            Debug::mensajeError('DJ GAST MOVILIDAD CONTROLLER : STORE',$th);
            
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('DJMovilidad.Empleado.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }


    }

    public function ver($codDJ){
        $DJ = DJGastosMovilidad::findOrFail($codDJ);
        $listaDetalles = DetalleDJGastosMovilidad::where('codDJGastosMovilidad','=',$DJ->codDJGastosMovilidad)->get();
        
        if(!$DJ->codEmpleado == Empleado::getEmpleadoLogeado()->codEmpleado)
            return redirect()->route('DJMovilidad.Empleado.Listar')
                ->with('datos','Las declaraciones juradas solo pueden ser vistas por la persona que las creó.');
        
        return view('DJ.Movilidad.VerDJMov',compact('DJ','listaDetalles'));


    }

    public function descargarPDF($codDJ){
        $DJ = DJGastosMovilidad::findOrFail($codDJ);
        $pdf = $DJ->getPDF();
        return $pdf->download('DJ Movilidad '.$DJ->codigoCedepas.'.Pdf');
    }   
    
    public function verPDF($codDJ){
        $DJ = DJGastosMovilidad::findOrFail($codDJ);
        $pdf = $DJ->getPDF();
        return $pdf->stream('DJ Movilidad '.$DJ->codigoCedepas.'.Pdf');
    }

    /* NO TIENE UPDATE ESTE CONTROLLER, NI DEBE TENERLO */

}
