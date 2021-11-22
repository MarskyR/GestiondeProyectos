<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rendición de Gastos {{$rendicion->codigoCedepas}}</title>
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
        td {
            border: 1px solid black;
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
                    <th style="text-align: center" colspan="2">N° {{$rendicion->codigoCedepas}}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align: center; background-color: rgb(0, 102, 205); color: white">RENDICIÓN DE GASTOS</th>
                </tr>
            </thead>
          
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td style="width: 100px; font-weight: bold;">Proyecto:</td>
                    <td>{{$rendicion->getNombreProyecto()}}</td>
                    <td style="width: 140px;font-weight: bold;">Fecha:</td>
                    <td>{{$rendicion->getfechaHoraRendicion()}}</td>
                </tr>
                
                <tr>
                    <td style="font-weight: bold;">Colaborador/a:</td>
                    <td>{{$rendicion->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
                    <td style="font-weight: bold;">Código del/a Colaborador/a:</td>
                    <td>{{$rendicion->getEmpleadoSolicitante()->codigoCedepas}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Moneda:</td>
                    <td>{{$rendicion->getMoneda()->nombre}} </td>
                    <td style="font-weight: bold;">Importe Recibido:</td>
                    <td>{{$rendicion->getMoneda()->simbolo}} {{number_format($rendicion->totalImporteRecibido,2)}}</td>
                </tr>
            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%; font-size: 10px;">
                <tr style="font-weight: bold; background-color:rgb(238, 238, 238);">
                    <td style="width: 60px; text-align: center;">Fecha</td>
                    <td style="width: 30px; text-align: center;">Tipo</td>
                    <td style="width: 110px; text-align: center;">Nro Compbte</td>
                    <td style="text-align: center;">Concepto</td>
                    <td style="width: 50px; text-align: center;">Importe {{$rendicion->getMoneda()->simbolo}} </td>
                    <td style="width: 70px; text-align: center;">Código Presupuestal</td>
                </tr>

                @foreach($listaItems as $item)
                    <tr>
                        <td style="text-align: center">{{$item->getFecha()}}</td>
                        <td style="text-align: center">{{$item->getCDP()->codigoSUNAT}}</td>
                        <td style="text-align: center">{{$item->nroComprobante}}</td>
                        <td>{{$item->concepto}}</td>
                        <td style="text-align: right">{{number_format($item->importe,2)}}</td>
                        <td style="text-align: center">{{$item->codigoPresupuestal}}</td>
                    </tr>
                @endforeach
                    {{-- RECIBI 10, GASTE 14, SALDO A FAV EMPLEADO 4 --}}
                    {{-- RECIBI 125, GASTE 251, SALDO A FAV EMPLEADO 126 --}}
                    
                    {{-- Recibido + saldoFavEmp = Rendido --}}

                <tr>
                    <td style="border: 0;"></td>
                    <td style="border: 0;"></td>
                    <td style="border: 0;"></td>
                    <td style="font-weight: bold; background-color:rgb(238, 238, 238);">Total Importe rendido {{$rendicion->getMoneda()->simbolo}} </td>
                    <td style="text-align: right; background-color:rgb(238, 238, 238);">
                        {{number_format($rendicion->totalImporteRendido,2)}}</td>
                    <td style="border: 0;"></td>
                </tr>
                <tr>
                    <td style="border: 0;"></td>
                    <td style="border: 0;"></td>
                    <td style="border: 0;"></td>
                    <td style="font-weight: bold; background-color:rgb(238, 238, 238);">Saldo a favor de Cedepas {{$rendicion->getMoneda()->simbolo}} </td>
                    <td style="text-align: right; background-color:rgb(238, 238, 238);">
                        @if($rendicion->saldoAFavorDeEmpleado>0)
                            0
                        @else
                            {{number_format(-1*$rendicion->saldoAFavorDeEmpleado,2)}}
                        @endif
                        
                    </td>
                    <td style="border: 0;"></td>
                </tr>
                <tr>
                    <td style="border: 0;"></td>
                    <td style="border: 0;"></td>
                    <td style="border: 0;"></td>
                    <td style="font-weight: bold; background-color:rgb(238, 238, 238);">Saldo a favor del/a colaborador/a {{$rendicion->getMoneda()->simbolo}} </td>
                    <td style="text-align: right; background-color:rgb(238, 238, 238);">
                        @if($rendicion->saldoAFavorDeEmpleado<0)
                           0
                        @else
                            {{number_format($rendicion->saldoAFavorDeEmpleado,2)}}
                        @endif
                        
                    </td>
                    <td style="border: 0;"></td>
                </tr>
            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td style="width: 100px">Elaborado por:</td>
                    <td>{{$rendicion->getNombreSolicitante()}}</td>
                 
                </tr>
                <tr>
                    <td>Aprobado por:</td>
                    <td>{{$rendicion->getNombreEvaluador()  }}</td>
                </tr>
            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td> 
                        <p>
                            <b>RESUMEN DE LA ACTIVIDAD:</b><br>
                            {{$rendicion->resumenDeActividad}}  
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="notaDeGeneracion">
            *Vista PDF generada por el sistema gestion.cedepas.org el {{App\Fecha::getFechaHoraActual()}} por {{App\Empleado::getEmpleadoLogeado()->getNombreCompleto()}}
        </div>
    </div>
    
    
</body>

</html>