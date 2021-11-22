<!DOCTYPE html>
<html lang="en">
<head>
    <title>Orden de Compra {{$orden->codigoCedepas}} </title>
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
                    <th style="text-align: center" colspan="2">N° {{$orden->codigoCedepas}}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align: center; background-color: rgb(0, 102, 205); color: white">
                        ORDEN DE COMPRA - GUÍA DE INTERNAMIENTO
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 100px; font-weight: bold;">Señores:</td>
                    <td colspan="2">{{$orden->señores}}</td>
                    <td style="width: 140px; font-weight: bold;">RUC: {{$orden->ruc}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Dirección:</td>
                    <td colspan="3">{{$orden->direccion}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Atención:</td>
                    <td colspan="3">{{$orden->atencion}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Referencia:</td>
                    <td colspan="3">{{$orden->referencia}}</td>
                </tr>
            </tbody>

        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td colspan="7">Solicito abastecernos de los bienes / servicios que se detallan a continuación:</td>
                </tr>
                <tr style="font-weight: bold; background-color:rgb(238, 238, 238);">
                    <td style="width: 30px; text-align: center;">Ítem</td>
                    <td style="width: 40px; text-align: center;">Cant</td>
                    <td style="width: 70px; text-align: center;">Unidad de Medida</td>
                    <td style="text-align: center;">Descripción</td>
                    <td style="width: 70px; text-align: center;">Valor de Venta</td>
                    <td style="width: 70px; text-align: center;">Prec.Vta (inc IGV)</td>
                    <td style="width: 70px; text-align: center;">Total {{$orden->getMoneda()->simbolo}} </td>
                </tr>

                @foreach($listaItems as $i=>$item)
                <tr>
                    <td style="text-align: center;">{{$i+1}}</td>
                    <td style="text-align: right">{{number_format($item->cantidad,2)}}</td>
                    <td>{{$item->getUnidadMedida()->nombre}}</td>
                    <td>{{$item->descripcion}}</td>
                    <td style="text-align: right">{{number_format($item->valorDeVenta,2)}}</td>
                    <td style="text-align: right">{{number_format($item->precioVenta,2)}}</td>
                    <td style="text-align: right">{{number_format($item->subtotal,2)}}</td>
                </tr>    
                @endforeach
                
                <tr>
                    <td></td>
                    <td colspan="4" style="text-align: center">Son : {{$orden->escribirTotalSolicitado()}}  {{$orden->getMoneda()->nombre}}</td>
                    <td style="text-align: right; font-weight: bold; background-color:rgb(238, 238, 238);">TOTAL {{$orden->getMoneda()->simbolo}}</td>
                    <td style="text-align: right; font-weight: bold; background-color:rgb(238, 238, 238);">{{number_format($orden->total,2)}}</td>
                </tr>


        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td style="width: 140px;">Factura a nombre de:</td>
                    <td>CEDEPAS Norte</td>
                    <td>RUC 20481234574</td>
                </tr>
                
                <tr>
                    <td>Dirección:</td>
                    <td colspan="2">Calle Los Corales N°289 Urb. Santa Inés - Trujillo</td>
                </tr>
            </tbody>
        </table>
        <div style="width: 100%; height: 8px;"></div>
        <table style="width:100%">
            <tbody style="width:100%">
                <tr>
                    <td rowspan="3" style="width: 190px; vertical-align:bottom">
                        <p style="text-align: center; font-size: 11px; ">
                            <b>
                            _________________________<br>
                            {{$orden->getEmpleado()->getNombreCompleto()}}<br>
                            </b>
                            Administración CEDEPAS
                            {{-- 
                            {{$orden->getSede()->getMensajeAdministrador()}}
                             --}}

                        </p>
                    </td>
                    <td style="vertical-align:top">AFECTACIÓN PRESUPUESTAL</td>
                    <td style="vertical-align:top">DISTRIBUCIÓN CONTABLE</td>
                </tr>
                <tr>
                    <td>
                        <b>Proyecto:</b>
                        <div style="width: 100%; background-color:rgb(238, 238, 238);text-align: center">
                            {{$orden->getProyecto()->nombre}}
                        </div>
                    </td>
                    <td style="vertical-align:top">CUENTAS POR PAGAR</td>
                </tr>
                <tr>
                    <td>
                        <b>Partida:</b>
                        <div style="width: 100%; background-color:rgb(238, 238, 238);text-align: center; height:20px">
                            {{$orden->partidaPresupuestal}}
                        </div>
                    </td>
                    <td style="font-weight: bold;vertical-align:top;text-align: center">
                        {{$orden->getMoneda()->simbolo}} 
                        {{number_format($orden->total,2)}}
                    </td>

                </tr>
                <tr>
                    <td colspan="3">
                        <p>
                            <b>OBSERVACIÓN:</b><br>
                            {{$orden->observacion}}
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