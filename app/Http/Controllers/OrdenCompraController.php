<?php

namespace App\Http\Controllers;

use App\ArchivoOrdenCompra;
use App\Banco;
use App\Configuracion;
use App\Debug;
use App\DetalleOrdenCompra;
use App\Empleado;
use App\ErrorHistorial;
use App\Http\Controllers\Controller;
use App\Moneda;
use App\Numeracion;
use App\OrdenCompra;
use App\Proyecto;
use App\UnidadMedida;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrdenCompraController extends Controller
{
    const PAGINATION = 20;

    public function listarDeEmpleado(Request $request){
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';



        $ordenes = OrdenCompra::where('codEmpleadoCreador','>',"-1");
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $ordenes=$ordenes->where('fechaHoraCreacion','>',$fechaInicio)->where('fechaHoraCreacion','<',$fechaFin);
        }
        $ordenes =  $ordenes->orderBy('fechaHoraCreacion','DESC');
        $ordenes=$ordenes->paginate($this::PAGINATION);


        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;
        return view('OrdenCompra.ListarOrdenCompra',compact('ordenes','fechaInicio','fechaFin'));
    }

    public function crearOrdenCompra(){
        $empleado=Empleado::getEmpleadoLogeado();
        $objNumeracion = Numeracion::getNumeracionOC();
        $unidades=UnidadMedida::all();
        $proyectos=Proyecto::getProyectosActivos();
        $monedas=Moneda::all();
        $bancos=Banco::all();
        return view('OrdenCompra.CrearOrdenCompra',compact('empleado','objNumeracion','unidades','proyectos','monedas','bancos'));
    }

    public function Guardar(Request $request){

        try {
            DB::beginTransaction();

            $orden=new OrdenCompra();
            $orden->señores=$request->señores;
            $orden->ruc=$request->ruc;
            $orden->direccion=$request->direccion;
            $orden->atencion=$request->atencion;
            $orden->referencia=$request->referencia;
            $orden->total=$request->total;
            $orden->partidaPresupuestal=$request->partidaPresupuestal;
            $orden->observacion=$request->observacion;

            $orden->codProyecto=$request->codProyecto;
            $orden->codMoneda=$request->codMoneda;
            $orden->codEmpleadoCreador=Empleado::getEmpleadoLogeado()->codEmpleado;
            
            $empleado = Empleado::getEmpleadoLogeado();

            if($empleado->esJefeAdmin())
                $orden->codSede = $empleado->getSedeQueAdministra()->codSede;
            else //si es contador
                $orden->codSede =$empleado->getSedeContador()->codSede;
            
            $orden->fechaHoraCreacion=new DateTime();

            $orden->codigoCedepas=OrdenCompra::calcularCodigoCedepasLibre(Numeracion::getNumeracionOC());
            Numeracion::aumentarNumeracionOC();

            $orden->save();
            
            //creacion de detalles
            $vec[] = '';
            //$codREQRecienInsertado = $requerimiento->codRequerimiento;  
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
                

            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) 
            {
                $detalle=new DetalleOrdenCompra();
                $detalle->codOrdenCompra=$orden->codOrdenCompra ;//ultimo insertad            
                $detalle->cantidad=$request->get('colCantidad'.$i);
                $detalle->descripcion=$request->get('colDescripcion'.$i);            
                $detalle->valorDeVenta=$request->get('colValorVenta'.$i);
                $detalle->precioVenta=$request->get('colPrecioVenta'.$i);            
                $detalle->subtotal=$request->get('colSubTotal'.$i);
                if($request->get('colExonerado'.$i)=='1'){
                    $detalle->exoneradoIGV=0;
                }else $detalle->exoneradoIGV=1;
                $detalle->codUnidadMedida=$request->get('colUnidadMedida'.$i);
                $detalle->save();  
                $i=$i+1;
            }
            


            if( $request->nombresArchivos!='' ){
                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0;
                foreach ($request->file('filenames') as $archivo)
                {   
                    $nombreArchivoGuardado = $orden->getNombreGuardadoNuevoArchivo($j+1);
                    Debug::mensajeSimple('el nombre de guardado de la imagen es:'.$nombreArchivoGuardado);
                    
                    $archivoOrden = new ArchivoOrdenCompra();
                    $archivoOrden->codOrdenCompra = $orden->codOrdenCompra;
                    $archivoOrden->nombreGuardado = $nombreArchivoGuardado;
                    $archivoOrden->nombreAparente = $nombresArchivos[$j];
                    $archivoOrden->save();

                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('ordenes')
                    ->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }
            }

            db::commit();
            return redirect()
                ->route('OrdenCompra.Empleado.Listar')
                    ->with('datos', "Orden de compra ".$orden->codigoCedepas." generada exitosamente.");
        } catch (\Throwable $th) {
            Debug::mensajeError('ORDEN COMPRA CONTROLLER : Guardar',$th);
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()
                ->route('OrdenCompra.Empleado.Listar')->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }
            


        return redirect()->route('OrdenCompra.Empleado.Listar')->with('datos','Se ha Registrado la Orden de Compra N°'.$orden->codigoCedepas);
    }

    public function editarOrdenCompra($id){
        $orden=OrdenCompra::findOrFail($id);

        if($orden->codEmpleadoCreador != Empleado::getEmpleadoLogeado()->codEmpleado){
            return redirect()->route('OrdenCompra.Empleado.Listar')->with('datos','ERROR: Solo el administrador que creó la orden de compra puede editarla.');

        }

        if(!$orden->sePuedeEditar()){
            return redirect()->route('OrdenCompra.Empleado.Listar')->with('datos','Ya no se puede editar la orden de compra seleccionada, el plazo de '
            .Configuracion::maximoDiasEdicionOrdenCompra.
            ' dias para editarla se ha vencido.');
        }

        $empleado=Empleado::getEmpleadoLogeado();
        $objNumeracion = Numeracion::getNumeracionOC();
        $unidades=UnidadMedida::all();
        $proyectos=Proyecto::getProyectosActivos();
        $monedas=Moneda::all();
        $bancos=Banco::all();
        return view('OrdenCompra.EditarOrdenCompra',compact('empleado','objNumeracion','unidades','proyectos','monedas','bancos','orden'));
    }

    public function verOrdenCompra($id){
        $orden=OrdenCompra::findOrFail($id);

        $empleado=Empleado::getEmpleadoLogeado();

        $detalles=$orden->getDetalles();
        return view('OrdenCompra.VerOrdenCompra',compact('orden','empleado','detalles'));
    }

    public function Update( Request $request){
        
        try {

            db::beginTransaction();
            $orden=OrdenCompra::findOrFail($request->codOrdenCompra);

            
            if($orden->codEmpleadoCreador != Empleado::getEmpleadoLogeado()->codEmpleado)
                return redirect()->route('OrdenCompra.Empleado.Listar')
                    ->with('datos','Error: La orden no puede ser actualizado por un empleado distinto al que la creó.');

             
            $orden->señores=$request->señores;
            $orden->ruc=$request->ruc;
            $orden->direccion=$request->direccion;
            $orden->atencion=$request->atencion;
            $orden->referencia=$request->referencia;
            $orden->total=$request->total;
            $orden->partidaPresupuestal=$request->partidaPresupuestal;
            $orden->observacion=$request->observacion;
    
            $orden->codProyecto=$request->codProyecto;
            $orden->codMoneda=$request->codMoneda;
            $orden->save();

            //$total=0;
            //borramos todos los detalles pq los ingresaremos again
            //DB::select('delete from detalle_orden_compra where codOrdenCompra=" '.$orden->codOrdenCompra.'"');
            DetalleOrdenCompra::where('codOrdenCompra','=',$orden->codOrdenCompra)->delete();

            //creacion de detalles
            $vec[] = '';
            
            if($request->cantElementos==0)
                throw new Exception("No se ingresó ningún item.", 1);
                 

            $i = 0;
            $cantidadFilas = $request->cantElementos;
            while ($i< $cantidadFilas ) 
            {
                $detalle=new DetalleOrdenCompra();
                $detalle->codOrdenCompra=$orden->codOrdenCompra ;//ultimo insertad            
                $detalle->cantidad=$request->get('colCantidad'.$i);
                $detalle->descripcion=$request->get('colDescripcion'.$i);            
                $detalle->valorDeVenta=$request->get('colValorVenta'.$i);
                $detalle->precioVenta=$request->get('colPrecioVenta'.$i);            
                $detalle->subtotal=$request->get('colSubTotal'.$i);
                if($request->get('colExonerado'.$i)=='1'){
                    $detalle->exoneradoIGV=0;
                }else $detalle->exoneradoIGV=1;
                $detalle->codUnidadMedida=$request->get('colUnidadMedida'.$i);
                $detalle->save();  
                $i=$i+1;
            }
            
            


            //SOLO BORRAMOS TODO E INSERTAMOS NUEVOS ARCHIVOS SI ES QUE SE INGRESÓ NUEVOS
            if( $request->nombresArchivos!='' ){
                Debug::mensajeSimple("o yara/".$request->tipoIngresoArchivos);
                if($request->tipoIngresoArchivos=="1")
                {//AÑADIR
                    
                }else{//SOBRESRIBIR
                    $orden->borrarArchivos();  //A
                }

                $cantidadArchivosYaExistentes = $orden->getCantidadArchivos();

                $nombresArchivos = explode(', ',$request->nombresArchivos);
                $j=0; //A

                foreach ($request->file('filenames') as $archivo)
                {   
                    
                    $nombreArchivoGuardado = $orden->getNombreGuardadoNuevoArchivo($cantidadArchivosYaExistentes + $j+1);
                    Debug::mensajeSimple('el nombre de guardado de la imagen es:'.$nombreArchivoGuardado);

                    $archivoOrden = new ArchivoOrdenCompra();
                    $archivoOrden->codOrdenCompra = $orden->codOrdenCompra;
                    $archivoOrden->nombreGuardado = $nombreArchivoGuardado;
                    $archivoOrden->nombreAparente = $nombresArchivos[$j];
                    $archivoOrden->save();


                    $fileget = \File::get( $archivo );
                    
                    Storage::disk('ordenes')
                    ->put($nombreArchivoGuardado,$fileget );
                    $j++;
                }

                
            }



            db::commit();

            return redirect()->route('OrdenCompra.Empleado.Listar')
                ->with('datos','Se ha editado la orden de compra N°'.$orden->codigoCedepas);
        }catch(\Throwable $th){
            Debug::mensajeError('ORDEN COMPRA CONTROLLER UPDATE', $th);
            DB::rollBack();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('OrdenCompra.Empleado.Listar')
                ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
        }

    }


    public function descargarPDF($codOrden){
        $orden = OrdenCompra::findOrFail($codOrden);
        $pdf = $orden->getPDF();
        return $pdf->download('Orden de Compras '.$orden->codigoCedepas.'.Pdf');
    }   
    
    public function verPDF($codOrden){
        $orden = OrdenCompra::findOrFail($codOrden);
        $pdf = $orden->getPDF();
        return $pdf->stream('Orden de Compras '.$orden->codigoCedepas.'.Pdf');
    }

    //para consumirlo en js
    public function listarDetalles($codOrdenCompra){
        $vector = [];
        $listaDetalles = DetalleOrdenCompra::where('codOrdenCompra','=',$codOrdenCompra)->get();
        for ($i=0; $i < count($listaDetalles) ; $i++) { 
            $itemDet = $listaDetalles[$i];
            $itemDet['codOrdenCompra'] = UnidadMedida::findOrFail($itemDet->codUnidadMedida)->nombre;//va a tener el nombre de la unidad de medida
            //array_push($itemDet,['descripcionUnidadMedida'=>UnidadMedida::findOrFail($itemDet->codUnidadMedida)->nombre]);
            if($itemDet['exoneradoIGV']==1){
                $itemDet['exoneradoIGV']=0;
            }else{
                $itemDet['exoneradoIGV']=1;
            }
            array_push($vector,$itemDet);            
        }
        return $vector  ;
    }

    
    //se le pasa el codigo del archivo 
    function descargarArchivo($codArchivoOrden){
        $archivo = ArchivoOrdenCompra::findOrFail($codArchivoOrden);
        return Storage::download("/ordenes/".$archivo->nombreGuardado,$archivo->nombreAparente);

    }

    
    function eliminarArchivo($codArchivoOrden){
        try{
            $archivo = ArchivoOrdenCompra::findOrFail($codArchivoOrden);
        }catch (\Throwable $th) {
            return redirect()->route('OrdenCompra.Empleado.Listar')
                ->with('datos','ERROR: El archivo que desea eliminar no existe o ya ha sido eliminado, vuelva a la página de editar Orden de compra.');
        }
        
        try {
            db::beginTransaction();
            
            $nombreArchivEliminado = $archivo->nombreAparente;
            $orden = OrdenCompra::findOrFail($archivo->codOrdenCompra);

            $archivo->eliminarArchivo();
            DB::commit();
        
            return redirect()->route('OrdenCompra.Empleado.Editar',$orden->codOrdenCompra)
                ->with('datos','Archivo "'.$nombreArchivEliminado.'" eliminado exitosamente.');
        } catch (\Throwable $th) {
            Debug::mensajeError(' ORDEN COMPRA CONTROLLER Eliminar archivo' ,$th);    
            DB::rollback();
            $codErrorHistorial=ErrorHistorial::registrarError($th,
                                                             app('request')->route()->getAction(),
                                                             json_encode($request->toArray())
                                                            );
            return redirect()->route('OrdenCompra.Empleado.Editar',$orden->codOrdenCompra)
            ->with('datos',Configuracion::getMensajeError($codErrorHistorial));
                
        }
        


    }

}
