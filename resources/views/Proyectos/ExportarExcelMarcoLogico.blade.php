
@php
    $descargar = true;

    if($descargar){
        header("Pragma: public");
        header("Expires: 0");
        $filename = "Reporte ".$proyecto->nombre."- MML.xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");

    }

    $poblacionesBeneficiarias = $proyecto->getPoblacionesBeneficiarias();
 

    $fondoPlomo = "background-color: #D0CECE;";
    $borde = " ";
    
    $negrita = "font-weight: bold;";
    $centrar = "text-align:center;";

    $colorRojo = "color:red";
    $colorVerde = "color: green";
    
    $styleCeldaCampo = $fondoPlomo." ".$borde.$negrita;
    $styleCeldaValor = " ".$borde." ";



@endphp
<meta charset="utf-8">
 


{{-- TABLA DATOS GENERALES DEL PROYECTO --}}
<table style="" border="1">
    <tbody >

        <tr>
            <td style="{{$styleCeldaCampo}}">Nombre de proyecto:</td>   
            <td style="{{$styleCeldaValor}}">
                {{$proyecto->nombre}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Código Presupuestal:
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->codigoPresupuestal}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Sede Principal:
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getSede()->nombre}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Estado del Proyecto:
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getEstado()->nombre}}
            </td>


            <td style="{{$styleCeldaCampo}}">
                Gerente:
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getGerente()->getNombreCompleto()}}
            </td>
            <td style="{{$styleCeldaCampo}}">Financiera:</td>   
            <td style="{{$styleCeldaValor}}">
                {{$proyecto->getEntidadFinanciera()->nombre}}
            </td>
        </tr>

        
        <tr>
          

            <td style="{{$styleCeldaCampo}}">
                Contacto de la financiera::
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->contacto_nombres}} {{$proyecto->contacto_correo}} {{$proyecto->contacto_telefono}} 
            </td>

            <td style="{{$styleCeldaCampo}}">
                Fecha de inicio:
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getFechaInicio()}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Fecha de finalización
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getFechaFinalizacion()}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Duración en meses
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getDuracion('meses',false)}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Tipo de Financiamiento
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getTipoFinanciamiento()->nombre}}
            </td>

            <td style="{{$styleCeldaCampo}}">
                Moneda del proyecto
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->getMoneda()->nombre}}
            </td>

            
        </tr>

 

        <tr>


            <td style="{{$styleCeldaCampo}}">
                Nombre completo del proyecto
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                {{$proyecto->nombreLargo}} 
            </td>
            
            <td style="{{$styleCeldaCampo}}">
                Cptda Cedepas
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                 
                {{$proyecto->importeContrapartidaCedepas}}
            </td>
            
            <td style="{{$styleCeldaCampo}}">
                Cptda Pob. Beneficiaria
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                 
               
                {{$proyecto->importeContrapartidaPoblacionBeneficiaria}}
            </td>

            
            <td style="{{$styleCeldaCampo}}">
                Cptda Otros
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                 
                {{$proyecto->importeContrapartidaOtros}}
            </td>


            <td style="{{$styleCeldaCampo}}">
                Importe Financiamiento
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                 
                {{$proyecto->importeFinanciamiento}}
            </td>


            <td style="{{$styleCeldaCampo}}">
                Presupuesto total
            </td>   
            <td  style="{{$styleCeldaValor}}" >
                 
                {{$proyecto->importePresupuestoTotal}}
            </td>

        </tr>


        <tr>
       
            <td style="{{$styleCeldaCampo}}">
                Objetivo General
            </td>   
            <td colspan="11" style="{{$styleCeldaValor}}" >
                 
                {{$proyecto->objetivoGeneral}}
            </td>
        </tr>
       


    </tbody>
</table>
 
<br>
    



{{-- OBJETIVOS ESPECIFICOS --}}
<table border="1"  style=''> 
    <thead style="{{$fondoPlomo}}">
       

        <th style="{{$fondoPlomo}}">Obj Esp</th>    
        <th style="{{$fondoPlomo}}"> Indicadores</th>
         
        
    </thead>
    <tbody>
        @php    /* ESTO ES PARA NUMERAR LAS SUBDIVISIONES, ESTA NUMERACIÓN ES SOLO VISUAL, NO SE ALMACENA EN LA BD */
            $i=1;
        @endphp

        @foreach($listaObjetivosEspecificos as $itemObjEspecifico)
            <tr  class="selected">
                <td colspan="1">   
                    <b>{{$i.". "}}</b>    
                         
                    {{$itemObjEspecifico->descripcion}}
                    
                </td>       
 

                
                <td></td>
           

            </tr>    
            @php
                $j=1;
            @endphp        

            @foreach ($itemObjEspecifico->getListaDeIndicadores() as $itemIndicadorObjEsp)
                <tr>
                    <td></td>
                

                    <td colspan="1">
                        <b>{{$i.".".$j." "}}</b>
                               
                        {{$itemIndicadorObjEsp->descripcion}}
                    </td>
                
                </tr>
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

 <br>


{{-- LUGARES EJECUCION --}}
<table border="1"> 
    <thead style="{{$styleCeldaCampo}}">
        <tr>

            <th  style="{{$fondoPlomo}}" >Departamento</th>                                        
            <th  style="{{$fondoPlomo}}" >Provincia</th>                                 
            <th  style="{{$fondoPlomo}}" >Distrito</th>
            <th  style="{{$fondoPlomo}}">Lugar</th>
            
        </tr>
        {{-- <th width="20%" >Opciones</th>                                            
    --}}
    </thead>
    <tbody>
        @foreach($proyecto->getLugaresEjecucion() as $itemLugarEjecucion)
            <tr>
                <td  >               
                    {{$itemLugarEjecucion->getDepartamento()->nombre}}
                </td>       
                <td  > 
                    {{$itemLugarEjecucion->getProvincia()->nombre}}
                </td>               
                           
                <td >               
                    {{$itemLugarEjecucion->getDistrito()->nombre}} 
                </td>     
                <td >
                    {{$itemLugarEjecucion->zona}} 
                    
                </td>
                
            </tr>                
        @endforeach    
    </tbody>
</table>

<br>

{{-- OBJETIVOS ESTRATEGICOS PEI --}}
<table border="1"> 
    <thead >
        <th  style="{{$fondoPlomo}}"   > Item</th>
        <th  style="{{$fondoPlomo}}"  >Descripción objetivo estratégico</th>                                        
        <th  style="{{$fondoPlomo}}"   >0-100%</th>                                        
        
        
    </thead>
    <tbody>
        @foreach($proyecto->getListaPorcentajesObj() as $itemPorcentajeObj)
            <tr >
                <td>
                    {{$itemPorcentajeObj->getObjetivoEstrategico()->item}}
                    
                </td>
                
                <td   >               
                    {{$itemPorcentajeObj->getObjetivoEstrategico()->nombre}}
                </td>       
                <td   > 
                    {{$itemPorcentajeObj->porcentajeDeAporte}}
                  
                    
                </td>               
                
                        
            </tr>                
        @endforeach    
    </tbody>
</table>

<br>

{{-- OBJ DEL MILENIO --}}
<table  border="1" > 
    <thead  >
        <th style="{{$fondoPlomo}}" > Item</th>
        <th  style="{{$fondoPlomo}}"   >Descripción Objetivo del Milenio</th>                                        
        <th   style="{{$fondoPlomo}}" >0-100%</th>                                        
    </thead>
    <tbody>
        @foreach($proyecto->getRelacionesObjMilenio() as $relacion)
            <tr >
                <td>
                    {{$relacion->getObjetivoMilenio()->item}}
                    
                </td>
                
                <td >               
                    {{$relacion->getObjetivoMilenio()->descripcion}}
                </td>       
                <td  > 
                    {{$relacion->porcentaje}}
                   
                </td>               
            </tr>                

        @endforeach    
    </tbody>
</table>

<br>
 


{{-- RESULTADOS ESPERADOS -> INDICADORES -> MEDIOS VERIF  --}}
<table border="1"> 
    <thead>
        <tr>
            <th style="{{$styleCeldaCampo}}" >Resultado Esperado</th>
           
            <th  style="{{$styleCeldaCampo}}">Indicador</th>
            
            
            <th style="{{$styleCeldaCampo}}" >Medio de Verificacion</th>
            <th style="{{$styleCeldaCampo}}" >Opciones</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i=1;
        @endphp
        
        @foreach($proyecto->getResultadosEsperados() as $itemResultado)  
            <tr>
                <td >
                    <b>{{$i.". "}}</b>
                            
                    {{$itemResultado->descripcion}}
                </td>         
               

                <td></td>
               

                <td></td>
                <td></td>
                

            </tr>
            @php
            $j=1;
            @endphp
            @foreach ($itemResultado->getListaIndicadoresResultados() as $indicadorRes)
                <tr>
                    <td></td>
                    

                    <td>
                        <b>{{$i.".".$j." "}}</b>
                        
                        {{$indicadorRes->descripcion}}
                    </td>
                     

                    <td></td>
                    <td></td>
                </tr>
                @php
                    $k=1;
                @endphp
                @foreach ($indicadorRes->getMediosVerificacion() as $medioVerif)
                    <tr>
                        <td></td>
                        <td></td>
                        

                        <td>
                            <b>{{$i.".".$j.".".$k." "}}  </b>
                              
                            {{$medioVerif->descripcion}}
                            
                        </td>
                        <td>

                            @if($medioVerif->tieneArchivo())
                            <a href="{{route('MedioVerificacionResultado.descargar',$medioVerif->codMedioVerificacion)}}" 
                                class="btn btn-success btn-sm">
                                <i class="fas fa-download fa-sm"></i>
                                {{$medioVerif->nombreAparente}}
                            </a>
                            @endif
                           
 
                        </td>
                    </tr>
                    @php
                        $k++;
                    @endphp
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

<br>

{{-- RESULTADOS ESPERADOS -> ACTIVIDADES -> METAS  --}}
<table  border="1" > 
    <thead>
        <th style="{{$styleCeldaCampo}}" >Resultado Esperado</th>
     

        <th style="{{$styleCeldaCampo}}"  >Actividad</th>
         
         
        <th  style="{{$styleCeldaCampo}}" >Meta y Unid Med</th>
        <th  style="{{$styleCeldaCampo}}" >Opciones</th>

    </thead>
    <tbody>
        @php
            $i=1;
        @endphp
        @foreach ($proyecto->getResultadosEsperados()  as $itemResultado)
            <tr>
                <td>
                    <b>{{$i.". "}} </b>       
                    {{$itemResultado->descripcion}}
                </td>
               
                <td></td>
                
                <td></td>
                <td></td>
                 
                
            </tr>
            @php
            $j=1;
            @endphp
            @foreach ($itemResultado->getListaActividades() as $actividad)
                <tr>
                    <td></td>
                    @if($proyecto->sePuedeEditar())
                        <td></td>
                    @endif
                    <td>
                        <b>{{$i.".".$j." "}}</b>
                             
                        {{$actividad->descripcion}}
                    </td>
                 
                
                    <td></td>
                    <td></td>
                  
                    
                </tr>
                @php
                    $k=1;
                @endphp
                @foreach ($actividad->getListaIndicadores() as $indicador)
                    <tr>
                        <td></td>
                       
                        <td></td>
                       
                        
                        <td>
                            <b>{{$i.".".$j.".".$k." "}}</b>
                            
                            {{$indicador->getMetaYUnid()}}
                        </td>

                        <td>
                            <a href="{{route('IndicadorActividad.Ver',$indicador->codIndicadorActividad )}}" class="btn btn-success btn-sm">
                                <i class="fas fa-chart-bar fa-sm"></i>
                                Seguimiento
                            </a>
                            @if($proyecto->sePuedeEditar())
                            <a href="{{route('IndicadorActividad.RegistrarMetas',$indicador->codIndicadorActividad )}}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye fa-sm"></i>
                                Programar
                            </a>
                            
                            <button href="#" class="btn btn-info btn-sm" onclick="clickEditarIndicadorActividad({{$indicador->codIndicadorActividad}})"
                            data-toggle="modal" data-target="#ModalIndicadorActividad"     >
                                <i class="fas fa-pen  fa-sm"></i>   
                            </button>
                            <button href="#" class="btn btn-danger btn-sm" onclick="clickEliminarIndicadorActividad({{$indicador->codIndicadorActividad}})">
                                <i class="fas fa-trash  fa-sm"></i>   
                            </button>
                            @endif
                        </td>
                        


                    </tr>
                    @php
                        $k++;
                    @endphp 
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



{{-- POBLACION BENEFICIARIA --}}
@include('Proyectos.ExportarPobBeneficiaria-Tablas')