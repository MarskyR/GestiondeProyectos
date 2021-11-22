<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reposición de Gastos {{$reposicion->codigoCedepas}}</title>
    <meta charset="UTF-8MB4">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        
        .notaDeGeneracion { 
            font-size: 12px;
            color: rgb(168, 168, 168);
        } 
        html {
            /* Arriba | Derecha | Abajo | Izquierda */
            margin: 50pt 60pt 50pt 70pt;
            font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
        }
        #principal { 
            /*background-color: rgb(161, 51, 51);*/
            word-wrap: break-word;/* para que el texto no salga del div*/
        }
        thead {
            font-size: large;
        }
        tbody{
            font-size: 13px;
        }
        table{
            
            border-collapse: collapse;
        }
        th{
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px;  
        }
        td.conLineas {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px;  
        }

        td.sinLineas {
            
            border-collapse: collapse;
            padding: 3px;  
        }
    </style>
    
</head>
<body>
    <div id="principal" style="width: 635px; height: 750px">
        <table style="width:100%">
            <thead style="width:100%">
                <tr>
                    <th style="height: 70px; float: left" colspan="2">
                        <div style="height: 5px"></div>
                        <img src="{{App\Configuracion::getRutaImagenCedepasPNG()}}" height="100%">
                    </th>
                    <th style="text-align: center" colspan="2">N° {{$reposicion->codigoCedepas}}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align: center; background-color: rgb(0, 102, 205); color: white">REPOSICIÓN DE GASTOS</th>
                </tr>
            </thead>
          
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td class="conLineas" style="width: 100px; font-weight: bold;">Proyecto:</td>
                    <td class="conLineas" >{{$reposicion->getProyecto()->nombre}}</td>
                    <td class="conLineas"  style="width: 140px;font-weight: bold;">Fecha:</td>
                    <td class="conLineas" >{{$reposicion->getFechaHoraEmision()}}</td>
                </tr>
                
                <tr>
                    <td class="conLineas"  style="font-weight: bold;">Colaborador/a:</td>
                    <td class="conLineas" >{{$reposicion->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
                    <td class="conLineas"  style="font-weight: bold;">Código del/a Colaborador/a:</td>
                    <td class="conLineas" >{{$reposicion->getEmpleadoSolicitante()->codigoCedepas}}</td>
                </tr>
                <tr>
                    <td class="conLineas"  style="font-weight: bold;">Moneda:</td>
                    <td class="conLineas" >{{$reposicion->getMoneda()->nombre}}</td>
                    <td class="conLineas"  style="font-weight: bold;">Importe Gastado:</td>
                    <td class="conLineas" >{{$reposicion->getMoneda()->simbolo}}  {{number_format($reposicion->totalImporte,2)}}</td>
                </tr>
            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%; font-size: 10px;">
                <tr style="font-weight: bold; background-color:rgb(238, 238, 238);">
                    <td class="conLineas" style="width: 60px; text-align: center;">Fecha</td>
                    <td class="conLineas" style="width: 25px; text-align: center;">Tipo</td>
                    <td class="conLineas" style="width: 110px; text-align: center;">Nro Compbte</td>
                    <td class="conLineas" style="text-align: center;">Concepto</td>
                    <td class="conLineas" style="width: 50px; text-align: center;">Importe {{$reposicion->getMoneda()->simbolo}} </td>
                    <td class="conLineas" style="width: 70px; text-align: center;">Código Presupuestal</td>
                </tr>

                @foreach($detalles as $itemdetalle)
                    <tr>
                        <td class="conLineas"  style="text-align: center">{{$itemdetalle->getFechaComprobante()}}</td>
                        <td class="conLineas"  style="text-align: center">{{$itemdetalle->getCDP()->codigoSUNAT}}</td> 
                        <td class="conLineas"  style="text-align: center">{{$itemdetalle->nroComprobante}}</td>
                        <td class="conLineas" >{{$itemdetalle->concepto}}</td>
                        <td class="conLineas"  style="text-align: right">{{number_format($itemdetalle->importe,2)}}</td>
                        <td class="conLineas"  style="text-align: center">{{$itemdetalle->codigoPresupuestal}}</td>
                    </tr>
                @endforeach



                <tr style="border: 0;">
                    <td class="sinLineas" style="border: 0;"></td>
                    <td class="sinLineas" style="border: 0;"></td>
                    <td class="sinLineas" style="border: 0;"></td>
                    <td class="conLineas"  style="font-weight: bold; background-color:rgb(238, 238, 238);">Total solicitado como reposición {{$reposicion->getMoneda()->simbolo}}</td>
                    <td class="conLineas"  style="text-align: right; background-color:rgb(238, 238, 238);">
                        {{number_format($reposicion->totalImporte,2)}}</td>
                    <td class="sinLineas" style="border: 0;"></td>
                </tr>
                <!--
                <tr>
                    <td colspan="2" style="text-align: center">Son: Dos gotas gotitas de lluvia</td>
                    <td style="text-align: right">S/ 2.00</td>
                    <td></td>
                </tr>
                -->
            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            {{-- <tbody style="width:100%">
                <tr>
                    <td class="conLineas"  style="width: 50px;">Girar a la <br> orden de:</td>
                    <td class="conLineas" style="width: 120px;" >{{$reposicion->girarAOrdenDe}}</td>

                    
                    <td class="conLineas" style="width: 25px;" >Banco:</td>
                    <td class="conLineas" style="width: 40px;" >{{$reposicion->getBanco()->nombreBanco}}</td>
                    <td class="conLineas" style="width: 30px;" >Cuenta N°:</td>
                    <td class="conLineas" style="width: 90px;" >{{$reposicion->numeroCuentaBanco}}</td>
                    
                </tr>
             
                <tr>
                    <td class="conLineas" >Aprobado por:</td>
                    <td class="conLineas" colspan="5">
                        {{is_null($reposicion->codEmpleadoEvaluador) ? $reposicion->getProyecto()->evaluador()->getNombreCompleto() : $reposicion->evaluador()->getNombreCompleto()}}
                    </td>
                </tr>

         


            </tbody>
             --}}
            <tbody style="width:100%">
                <tr>
                    <td class="conLineas" style="width: 50px;">Girar a <br> la orden de:</td>
                    <td class="conLineas" style="width: 140px">{{$reposicion->girarAOrdenDe}}</td>
                    
                     
                    <td class="conLineas" style="width: 30px;">{{$reposicion->getBanco()->nombreBanco}}</td>
                    <td class="conLineas" style="width: 80px;" colspan="2">N° Cuenta: {{$reposicion->numeroCuentaBanco}}</td>
                   
                </tr>
                  
                <tr>
                    <td class="conLineas" >Aprobado por:</td>
                    <td class="conLineas" colspan="4">
                        {{is_null($reposicion->codEmpleadoEvaluador) ? $reposicion->getProyecto()->evaluador()->getNombreCompleto() : $reposicion->evaluador()->getNombreCompleto()}}
                    </td>


                </tr>
                <tr>
                    <td  class="conLineas" >Autorizado por:</td>
                    <td  class="conLineas" colspan="4">
                        {{-- Solo se mostrará AutorizadoPor cuando sea ya ha pasado por por el administrador --}}
                        @if(!is_null($reposicion->codEmpleadoAdmin))
                            Ana Cecilia Angulo Alva
                        @endif
                        
                    </td>
                </tr>
            </tbody>

        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td class="conLineas" > 
                        <p>
                            <b>RESUMEN DE LA ACTIVIDAD:</b><br>
                            {{$reposicion->resumen}}  
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div class="notaDeGeneracion">
            *Vista PDF generada por el sistema gestion.cedepas.org el {{App\Fecha::getFechaHoraActual()}} por {{App\Empleado::getEmpleadoLogeado()->getNombreCompleto()}}
        </div>
         
        <!--FIRMAS-->
        {{-- <div style="width: 33%; height: 70px; float: right; margin-top: 20px">
            <p style="text-align: center; font-size: 13px;"><b>
                
                _________________________<br>
                VISTO BUENO <br>  DIRECCIÓN<br>
                
            </b></p>
        </div> --}}
  
            
    </div>
        
</body>

</html>
