<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Declaración Jurada {{$DJ->codigoCedepas}}- Gastos Varios</title>
       
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <style>
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

            .sinlineas{
                border: 0px !important;
            }
        </style>
        
    </head>
    <body>
        <div id="principal" style="width: 635px; height: 750px">
            <table style="width:100%">
                <thead style="width:100%">
                    <tr>
                        <th style="height: 70px; float: left" colspan="2">
                            <img src="{{App\Configuracion::getRutaImagenCedepasPNG()}}" height="100%" style="float: left">
                        </th>
                        <th style="text-align: center" colspan="2">Nº {{$DJ->codigoCedepas}}</th>
                    </tr>
                    <tr>
                        <th colspan="4" style="text-align: center; background-color: rgb(0, 102, 205); color: white">
                            DECLARACIÓN JURADA - GASTOS VARIOS
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

            </table>
            <div style="width: 100%; height: 10px;"></div>
            <table style="width:100%">
                <tbody>
                    <tr>
                        <td style="font-weight: bold; width: 100px">Fecha:</td>
                        <td>{{$DJ->getFechaHoraCreacion()}}</td>
                        <td style="font-weight: bold; width: 170px">Código Colaborador/a:</td>
                        <td>{{$DJ->getEmpleado()->codigoCedepas}}</td>
                    </tr>
                </tbody>
            </table>
            <div style="width: 100%; height: 10px;"></div>

            <table style="width:100%">
                <tbody style="width:100%;">
                    <tr>
                        <td colspan="3">Yo, <b>{{$DJ->getEmpleado()->getNombreCompleto()}}</b>, identificado/a con DNI <b>{{$DJ->getEmpleado()->dni}}</b> domiciliado/a en <b>{{$DJ->domicilio}}</b> DECLARO BAJO
                            JURAMENTO haber realizado gastos de los cuales no tengo documentos sustentatorios hasta por la suma de <b>{{$DJ->getMoneda()->simbolo}} {{number_format($DJ->importeTotal,2)}}</b> tal como detallo a continuación:
                            </td>
                    </tr>
                    <tr style="font-weight: bold; background-color:rgb(238, 238, 238);">
                        <td style="width: 80px; text-align: center;">Fecha</td>
                        <td style="text-align: center;">Concepto</td>
                        <td style="width: 90px; text-align: center;">Importe {{$DJ->getMoneda()->simbolo}}</td>
                    </tr>
                    @foreach($listaItems as $itemdetalle)
                    <tr>
                        <td style="text-align: center">{{$itemdetalle->getFecha()}}</td>
                        <td style="text-align: center">{{$itemdetalle->concepto}}</td>
                        <td style="text-align: right">{{number_format($itemdetalle->importe,2)}}</td>
                    </tr>
                    @endforeach
                    
                    <tr>
                        <td class="sinlineas"></td>
                        <td style="font-weight: bold; background-color:rgb(238, 238, 238); text-align: center">Total {{$DJ->getMoneda()->simbolo}}</td>
                        <td style="text-align: right; background-color:rgb(238, 238, 238);">{{number_format($DJ->importeTotal,2)}}</td>
                    </tr>
                   
                </tbody>
            </table>
            <div style="width: 100%; height: 20px;"></div>
            <table style="width:100%; border: 0;">
                <tbody style="width:100%; border: 0;">
                    <tr>
                        <td class="sinlineas"></td>

                        <td class="sinlineas" width="220px"><br>
                            <p style="text-align: center; font-size: 13px;">
                            FIRMA: ...........................................
                            </p>
                            <p style="text-align: center; font-size: 13px;">
                                DNI {{$DJ->getEmpleado()->dni}}
                            </p>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </body>

</html>