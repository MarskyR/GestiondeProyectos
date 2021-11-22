<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Requerimiento de ByS {{$requerimiento->codigoCedepas}}</title>
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
                padding: 1px;  
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
                        <th style="text-align: center" colspan="2">{{$requerimiento->codigoCedepas}}</th>
                    </tr>
                    <tr>
                        <th colspan="4" style="text-align: center; background-color: rgb(0, 102, 205); color: white">
                            REQUERIMIENTO DE BIENES Y SERVICIOS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Fecha y Hora Emisión:</td>
                        <td colspan="1">{{$requerimiento->formatoFechaHoraEmision()}}</td>
                        <td style="font-weight: bold;">Proyecto:</td>
                        <td colspan="1">{{$requerimiento->getProyecto()->nombre}}</td>
                        
                    </tr>
                    
                
                    <tr>
                        <td style="font-weight: bold;">Colaborador/a:</td>
                        <td colspan="1">{{$requerimiento->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
                        
                        <td style="font-weight: bold;">Cód Colab:</td>
                        <td colspan="1">{{$requerimiento->getEmpleadoSolicitante()->codigoCedepas}}</td>
                    </tr>
                    
                    
                </tbody>

            </table>
            <div style="width: 100%; height: 8px;"></div>
            <table style="width:100%">
                <tbody style="width:100%; font-size: 11px;">
                    <tr>
                        <td colspan="4">Solicito abastecernos de los bienes / servicios que se detallan a continuación:</td>
                    </tr>
                    <tr style="font-weight: bold; background-color:rgb(238, 238, 238);">
                        <td style="width: 70px; text-align: center;">Cantidad</td>
                        <td style="width: 80px; text-align: center;">Unidad de medida</td>
                        <td style="text-align: center;">Descripción del bien o servicio</td>
                        <td style="width: 90px; text-align: center;">Código</td>
                    </tr>

                    @foreach ($listaItems as $item)
                    <tr>
                        <td style="text-align: right">{{$item->cantidad}}</td>
                        <td>{{$item->getNombreTipoUnidad()}}</td>
                        <td>{{$item->descripcion}}</td>
                        <td style="text-align: left;">{{$item->codigoPresupuestal}}</td>
                    </tr>
                    @endforeach

                    
                


            </table>
            <div style="width: 100%; height: 8px;"></div>
            <table style="width:100%">
                <tbody style="width:100%; border: 0;">
                    <tr>
                        <td style="width: 100px; border: 0;">Solicitado por:</td>
                        <td style="border: 0;">{{$requerimiento->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
                       
                    </tr>
                    <tr>
                        <td style="border: 0;">Aprobado por:</td>
                        <td style="border: 0;">
                            @if(!is_null($requerimiento->codEmpleadoEvaluador))
                                {{$requerimiento->getEmpleadoEvaluador()->getNombreCompleto()}}
                            @endif
                        </td>
                        
                    </tr>
                    <tr>
                        <td style="border: 0;">Autorizado por:</td>
                        <td style="border: 0;">
                            @if(!is_null($requerimiento->codEmpleadoAdministrador))
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
                        <td colspan="2"> <p>
                            <b>JUSTIFICACIÓN DEL REQUERIMIENTO:</b><br>
                            {{$requerimiento->justificacion}}  
                        </p> </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width:30%">Cuenta Bancaria Proveedor:</td>
                        <td >{{$requerimiento->cuentaBancariaProveedor}}</td>
                        
                    </tr>
                </tbody>
            </table>
            <div class="notaDeGeneracion">
                *Vista PDF generada por el sistema gestion.cedepas.org el {{App\Fecha::getFechaHoraActual()}} por {{App\Empleado::getEmpleadoLogeado()->getNombreCompleto()}}
            </div>
        </div>
        
         
    </body>

</html>