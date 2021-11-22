<!DOCTYPE html>
<html lang="en">
<head>
    <title>Solicitud de Fondos {{$solicitud->codigoCedepas}}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   
    <style>
        .notaDeGeneracion { 
            font-size: 12px;
            color: rgb(168, 168, 168);
        } 
        
        html {
            /* Arriba | Derecha | Abajo | Izquierda */
            margin: 50pt 60pt 50pt 69pt;
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
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 3px;  
        }
    </style>
    
</head>
<body>
    <div id="principal" style="width: 635px; height: 750px">
        <table style="width:100%">
            <thead style="width:100%">
                <tr>
                    <th style="height: 70px;  " colspan="2">
                        <div style="height: 5px"></div>
                        
                        <img src="{{App\Configuracion::getRutaImagenCedepasPNG()}}" height="100%">
                    </th>
                    <th style="text-align: center" colspan="2">N° {{$solicitud->codigoCedepas}}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align: center; background-color: rgb(0, 102, 205); color: white">SOLICITUD DE FONDOS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">Proyecto:</td>
                    <td colspan="3">{{$solicitud->getNombreProyecto()}}</td>
                </tr>
                
                <tr>
                    <td style="font-weight: bold;">Fecha:</td>
                    <td colspan="3">{{$solicitud->getFechaHoraEmision()}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" width="16%">Código del/a Colaborador/a:</td>
                    <td width="17%">{{$solicitud->getEmpleadoSolicitante()->codigoCedepas}}</td>
                    <td style="font-weight: bold;" width="10%">Nombre:</td>
                    <td width="30%">{{$solicitud->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
                </tr>
            </tbody>

        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%; font-size: 11px;">
                <tr style="font-weight: bold">
                    <td style="width: 30px; text-align: center;">Ítem</td>
                    <td style="text-align: center;">Concepto</td>
                    <td style="width: 50px; text-align: center;">Importe {{$solicitud->getMoneda()->simbolo}}</td>
                    <td style="width: 90px; text-align: center;">Código Presupuestal</td>
                </tr>

                @foreach($listaItems as $item)
                    <tr>
                        <td style="text-align: center"> {{$item->nroItem}} </td>
                        <td>{{$item->concepto}}</td>
                        <td style="text-align: right">{{number_format($item->importe,2)}}</td>
                        <td>{{$item->codigoPresupuestal}}</td>
                    </tr>
                @endforeach

                
                
                
                
                <tr>
                    <td colspan="2" style="text-align: center">Son : {{$solicitud->escribirTotalSolicitado()}}  {{$solicitud->getMoneda()->nombre}}</td>
                    
                    <td style="text-align: right">{{$solicitud->getMoneda()->simbolo}} {{number_format($solicitud->totalSolicitado,2)}}</td>
                    <td></td>
                </tr>



            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td style="width: 50px;">Girar a <br> la orden de:</td>
                    <td style="width: 140px">{{$solicitud->girarAOrdenDe}}</td>
                    
                     
                    <td style="width: 30px;">{{$solicitud->getNombreBanco()}} </td>
                    <td style="width: 80px;" colspan="2">N° Cuenta: {{$solicitud->numeroCuentaBanco}} </td>
                   
                </tr>
                  
                <tr>
                    <td class="conLineas" >Aprobado por:</td>
                    <td class="conLineas" colspan="4">
                        {{$solicitud->getNombreEvaluador()}}
                    </td>


                </tr>
                <tr>
                    <td  class="conLineas" >Autorizado por:</td>
                    <td  class="conLineas" colspan="4">

                        @if(!is_null($solicitud->codEmpleadoAbonador))
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
                    <td style="width: 110px; font-weight: bold;">
                        JUSTIFICACIÓN
                        DE LA
                        SOLICITUD:</td>
                    <td >
                        {{$solicitud->justificacion}}
                    </td>
                    
                </tr>
            </tbody>
        </table>
        <div class="notaDeGeneracion">
            *Vista PDF generada por el sistema gestion.cedepas.org el {{App\Fecha::getFechaHoraActual()}} por {{App\Empleado::getEmpleadoLogeado()->getNombreCompleto()}}
        </div>
        <div style="width: 100%; height: 70px;"></div>

        <!--FIRMAS-->
        {{-- 
        <div style="width: 33%; height: 30px; float: left;">
            <p style="text-align: center; font-size: 13px;"><b>
                <br>
                _________________________<br>
                V°B° DIRECCIÓN
            </b></p>
        </div> --}}

    </div>
      
    
</body>

</html>