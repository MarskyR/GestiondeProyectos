<?php 
header("Pragma: public");
header("Expires: 0");
$filename = "Reporte de Metas del Proyecto ".$proyecto->nombre.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");

?>
  <meta charset="utf-8">
<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: large" >Proyecto:</th>
            <th colspan="7" style="font-size: large">Gerente:</th>    
            <th colspan="7" style="font-size: large">Fecha de Inicio:</th>     
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="7">{{$proyecto->nombre}}</th>
            <th colspan="7">{{$proyecto->getGerente()->getNombreCompleto()}}</th>    
            <th colspan="7">{{$proyecto->getFechaInicio()}}</th>     
        </tr>
        <tr>

        </tr>
    </tbody>
</table>
<table border="1" id="tablaDetallesLugares" class="table table-striped table-bordered table-condensed table-hover table-sm" 
        style='background-color:#FFFFFF;'> 
    <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
        
        
        <form action="{{route('GestionProyectos.agregarLugarEjecucion')}}" method="POST">
            @csrf
            <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                        
        </form>

        <tr>
            <th rowspan="2" class="text-center">Enunciado</th>     
            <th rowspan="2">Meta Total Programada</th>
            <th rowspan="2">Saldo</th>
            <th rowspan="2">Ejecución</th>

            @foreach ($proyecto->getMesesDeEjecucion() as $mes)
                <th colspan="3" class="text-center">{{$mes['nombreMes']." ".$mes['año']}}</th>                                 
                    
            @endforeach
            
        </tr>

        <tr>
            
            @foreach ($proyecto->getMesesDeEjecucion() as $mes)
                <th class="text-center">Prog</th>                                 
                <th class="text-center">Ejec</th>    
                <th class="text-center">%</th>    
            @endforeach

        </tr>
        
    </thead>
    <tbody>
        @php
            $i=1;
        @endphp
        {{-- Falta lógica para obtener numero de meses --}}
        @foreach ($proyecto->getResultadosEsperados() as $resultado)
            <tr>
                <td colspan="{{$proyecto->getCantidadColsParaReporteRes()}}">
                    <b>{{$i.". "}}</b>
                    {{$resultado->descripcion}}
                </td>
            </tr>
            @php
                $j=1;
            @endphp
            @foreach ($resultado->getListaActividades() as $actividad)
                <tr>
                    <td colspan="{{$proyecto->getCantidadColsParaReporteRes()}}">
                        <b>{{$i.".".$j." "}}</b> 
                        {{$actividad->descripcion}}
                    </td>
                </tr>
                @php
                    $k=1;
                @endphp
                @foreach ($actividad->getListaIndicadores() as $indicador)
                    <tr>
                        <td>
                            <b>{{$i.".".$j.".".$k." "}}</b> 
                            {{$indicador->unidadMedida}}
                        </td>
                        
                        <td>
                            {{$indicador->meta}}
                        </td>
                        <td>
                            {{$indicador->saldoPendiente}}
                        </td>
                        <td style="color: {{$indicador->getColorPorcentajeEjecucion()}}">
                            {{$indicador->calcularPorcentajeEjecucion()}}
                            <a href="{{route('IndicadorActividad.Ver',$indicador->codIndicadorActividad )}}" 
                                class="btn btn-success btn-sm">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                        </td>
                        @php
                            $k++;
                        @endphp  
                        @foreach ($proyecto->getMesesDeEjecucion() as $mes)
                            <td class="text-center {{$indicador->getMeta($mes)->pintarSiVacia()}}">{{-- PROGRAMADA --}}
                                {{$indicador->getMeta($mes)->cantidadProgramada}}
                            </td>

                            <td  class="text-center {{$indicador->getMeta($mes)->pintarSiVacia()}}" >{{-- EJECUTADA --}}
                                @if($indicador->getMeta($mes)->puedeRegistrarEjecutada())
                                    <button onclick="clickIngresarMeta(
                                        {{$indicador->getMeta($mes)->codMetaEjecutada}},
                                        {{$indicador->getMeta($mes)->cantidadProgramada}},
                                        '{{$mes['nombreMes']}}-{{date('Y',strtotime($indicador->getMeta($mes)->mesAñoObjetivo))}}'
                                        )" 
                                        type="button" id="botonModal" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRegistrarEjecutada" data-whatever="@mdo">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                @else{{-- Simplemente mostramos la meta --}}
                                    @if(!is_null($indicador->getMeta($mes)->cantidadEjecutada ))
                                        <button class="btn btn-info" type="button" 
                                            onclick="clickVerArchivos(
                                                {{$indicador->getMeta($mes)->codMetaEjecutada}}
                                                ,
                                                '{{$mes['nombreMes']}}-{{date('Y',strtotime($indicador->getMeta($mes)->mesAñoObjetivo))}}'
                                                )"
                                                id="btnModalDescargarArchivos" data-toggle="modal" data-target="#modalArchivosMeta">
                                            {{$indicador->getMeta($mes)->cantidadEjecutada}}
                                        </button>
                                    @endif
                                    
                                    
                                @endif
                                
                            </td>       

                            <td class="{{$indicador->getMeta($mes)->pintarSiVacia()}}" style="color: {{$indicador->getMeta($mes)->getColor() }}">
                                {{$indicador->getMeta($mes)->getEjecucion()}}
                            </td>                          
                            
                        @endforeach


                    </tr>
                        
                @endforeach
                @php
                    $j++;
                @endphp  
            @endforeach
            @php
                $i++;
            @endphp  
        

        @endforeach
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <th colspan="20" style="font-style: oblique;font-weight: normal;text-align: left">* Reporte generado el {{date("d/m/Y")}} por el usuario {{$proyecto->getGerente()->getNombreCompleto()}} mediante el <b>Sistema Web de Gestión de Cedepas Norte</b></th> 
        </tr>
    </tbody>
</table>
