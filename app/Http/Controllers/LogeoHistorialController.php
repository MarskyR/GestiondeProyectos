<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\Http\Controllers\Controller;
use App\LogeoHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogeoHistorialController extends Controller
{
    const PAGINATION = '50';

    public function listarLogeos(Request $request){
        //filtros
        $codEmpleadoBuscar=$request->codEmpleadoBuscar;
        // AÑO                  MES                 DIA
        $fechaInicio=substr($request->fechaInicio,6,4).'-'.substr($request->fechaInicio,3,2).'-'.substr($request->fechaInicio,0,2).' 00:00:00';
        $fechaFin=substr($request->fechaFin,6,4).'-'.substr($request->fechaFin,3,2).'-'.substr($request->fechaFin,0,2).' 23:59:59';


        $logeos=LogeoHistorial::where('codLogeoHistorial','>','0');
        if($codEmpleadoBuscar!='-1' && !is_null($codEmpleadoBuscar)){
            $logeos=$logeos->where('codEmpleado','=',$codEmpleadoBuscar);
        }
        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            //$fechaFin='es mayor';
            $logeos=$logeos->where('fechaHoraLogeo','>',$fechaInicio)->where('fechaHoraLogeo','<',$fechaFin);
        }
        $logeos=$logeos->orderBy('fechaHoraLogeo','DESC')->paginate($this::PAGINATION);


        //para grafico
        $temp=DB::select('select CAST(fechaHoraLogeo AS DATE) as fecha, COUNT(*) as cantidad from logeo_historial group by CAST(fechaHoraLogeo AS DATE)');
        $arr=[];
        foreach ($temp as $item) {
            $arr[]=array('date'=>date("Y-m-d",strtotime($item->fecha)), 'a'=>$item->cantidad);
        }
        $temp=[];
        if(count($arr)>5){//obtenermos los ultimos 5 dias
            for ($i=0; $i < 5; $i++) { 
                $temp[]=array_pop($arr);
            }
            $arr=$temp;
        }
        

        if(strtotime($fechaFin) > strtotime($fechaInicio) && $request->fechaInicio!=$request->fechaFin){
            $temp=DB::select('select CAST(fechaHoraLogeo AS DATE) as fecha, COUNT(*) as cantidad from logeo_historial where fechaHoraLogeo>? and fechaHoraLogeo<? group by CAST(fechaHoraLogeo AS DATE)',[$fechaInicio,$fechaFin]);
            $arr=[];
            foreach ($temp as $item) {
                $arr[]=array('date'=>date("Y-m-d",strtotime($item->fecha)), 'a'=>$item->cantidad);
            }
        }
        


        $empleados=Empleado::getEmpleadosActivos();
        $fechaInicio=$request->fechaInicio;
        $fechaFin=$request->fechaFin;
        return view('HistorialLogeos.ListarLogeos',
            compact('logeos','empleados','codEmpleadoBuscar','arr','temp','fechaInicio','fechaFin'));
    }
}
