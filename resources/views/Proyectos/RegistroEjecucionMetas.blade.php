@extends('Layout.Plantilla')
@php
    $empLogeado = App\Empleado::getEmpleadoLogeado();
@endphp

@section('titulo')
    @if($empLogeado->esGerente())
        Registrar
    @else 
        Ver
    @endif
    
    Metas

@endsection


@section('tiempoEspera')
    <div class="loader" id="pantallaCarga"></div>
@endsection

@section('estilos')
<style>
    /* 
        CODIGO OBTENIDO DE
        https://www.it-swarm-es.com/es/html/tabla-con-encabezado-fijo-y-columna-fija-en-css-puro/1072563817/
    */
    .divTablaFijada {
        max-width: 100%;
        max-height: 600px;
        overflow: scroll;
    }

    /* ESTE ES EL QUE FIJA LA ROW */
    .filaFijada { 
        position: -webkit-sticky; /* for Safari */
        position: sticky;
        top: 0;
    }

    .fondoAzul{
        background-color:#3c8dbc;
    }

    .letrasBlancas{
        color: #fff;
    }

</style>
@endsection

@section('contenido')

    <link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
    <link rel="stylesheet" href="/select2/bootstrap-select.min.css">
     
    <div class="card fontSize9 m-1" >
        <div class="card-header" style=" ">
        <div class="row">
            <div class="col">
                <b>Proyecto:</b>
                {{$proyecto->nombre}}
            </div>

            <div class="col">
                <b>Gerente:</b>
                {{$proyecto->getGerente()->getNombreCompleto()}}
            </div>
            
            <div class="col">
                <b>Fecha de inicio:  </b>
                {{$proyecto->getFechaInicio()}}
            </div>
            
            <div class="col">
                <b>Fecha de finalización:  </b>
                {{$proyecto->getFechaFinalizacion()}}
            </div>
            
            
        </div>
        </div>
    </div>

    @include('Layout.MensajeEmergenteDatos')


    <div id="" class="table-responsive contenedorFijo divTablaFijada " style="">                           
        <table id="tablaDetallesLugares" class="table table-striped table-bordered table-condensed table-hover table-sm" 
                style='background-color:#FFFFFF;'> 
            <thead class="thead-default filaFijada fondoAzul letrasBlancas" style="">
                
                
                <form action="{{route('GestionProyectos.agregarLugarEjecucion')}}" method="POST">
                    @csrf
                    <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                             
                </form>

                <tr>
                    <th rowspan="2" class="text-center">Enunciado</th>     
                    <th rowspan="2">Meta Prog</th>
                    
                    <th rowspan="2">Saldo</th>
                    <th rowspan="2">Meta Ejec</th>

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
                                <td >

                                    {{$indicador->getCantidadEjecutada()}}
                                    <br>
                                    <b class="fontSize10" style="color: {{$indicador->getColorPorcentajeEjecucion()}}">
                                        {{$indicador->calcularPorcentajeEjecucion()}}
                                    </b>
                                     
                                </td>
                                @php
                                    $k++;
                                @endphp    

                                @foreach ($proyecto->getMesesDeEjecucion() as $mes)
                                    <td class="text-center {{$indicador->getMeta($mes)->pintarSiVacia()}}">{{-- PROGRAMADA --}}
                                        
                                        @if( !$indicador->getMeta($mes)->estaVacia() ) {{-- Si no esta vacia, preguntamos si es reprg --}}
                                            @if($indicador->getMeta($mes)->esReprogramada())
                                                <b class="fontSize8" style="m-0">(Repr)</b>
                                            @endif
                                           
                                        @endif  
                                        {{$indicador->getMeta($mes)->cantidadProgramada}}
                                    </td>

                                    <td  class="text-center {{$indicador->getMeta($mes)->pintarSiVacia()}}" >{{-- EJECUTADA --}}
                                        

                                        @if($indicador->getMeta($mes)->puedeRegistrarEjecutada() ) {{-- La UGE no puede registrar metas, el gerente sí --}}
                                            @if($empLogeado->esGerente())
                                               
                                                
                                                <button onclick="clickIngresarMeta(
                                                    {{$indicador->getMeta($mes)->codMetaEjecutada}},
                                                    {{$indicador->getMeta($mes)->cantidadProgramada}},
                                                    '{{$mes['nombreMes']}}-{{date('Y',strtotime($indicador->getMeta($mes)->mesAñoObjetivo))}}'
                                                    )" 
                                                    type="button" id="botonModal" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRegistrarEjecutada" data-whatever="@mdo">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                             
                                            @endif
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
    </div> 
 
    

    
    <div class="modal fade" id="modalRegistrarEjecutada" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRegistrarEjecutadaLabel">New message</h5>
                    <button type="button" onclick="limpiarModalEjecucionMeta()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <form method = "POST" action = "{{route('IndicadorActividad.registrarCantidadEjecutada')}}" 
                        id="modalForm" name="modalForm"  enctype="multipart/form-data">

                        @csrf
                        <div class="row">
                            <input type="hidden" name="modalCodMetaEjecutada" id="modalCodMetaEjecutada">
                            <div class="col-sm form-group">
                                <label for="modalCantidadProgramada" class="col-form-label">Cantidad Programada:</label>
                                <input type="text" class="form-control" id="modalCantidadProgramada" name="modalCantidadProgramada" readonly>
                            </div>
                            <div class="col-sm form-group">
                                <label for="modalCantidadEjecutada" class="col-form-label">Cantidad Ejecutada:</label>
                                <input type="number" class="form-control" id="modalCantidadEjecutada" name="modalCantidadEjecutada" value="0">
                            </div>
                            <div class="w-100"></div>
                            {{-- Este es para subir todos los archivos x.x  --}}
                            <div class="col" id="divEnteroArchivo">            
                                    
                            </div>    

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="limpiarModalEjecucionMeta()" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEjecutada()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalArchivosMeta" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalArchivosMeta">Ver Meta</h5>
                    <button type="button" class="close" onclick="limpiarModalVerMeta()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div id="ContenidoDescargarArchivos">

                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button onclick="limpiarModalVerMeta()" type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col">

            @php
                if($empLogeado->esGerente())
                    $ruta = "GestiónProyectos.Gerente.Listar";
                else
                    $ruta = "GestiónProyectos.UGE.Listar";
            @endphp

            <a href="{{route($ruta)}}" class="btn btn-success m-2">
                <i class="fas fa-arrow-left"></i> 
                Regresar a proyectos
            </a>


        </div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col">
            
        </div>
        <div class="col text-right">
            <a href="{{route('GestionProyectos.Gerente.ExportarMetasEjecutadas',$proyecto->codProyecto)}}" 
                class="btn btn-success m-2">
                <i class="fas fa-file-excel"></i> 
                Exportar tabla 
            </a>

        </div>
        
    </div>
    
    


    <style>
        .SinRegistro{
            background-color: #9c9c9c;
        }
        .BordeCircular{
        border-radius: 10px;
        background-color:rgb(190, 190, 190);
        margin-left: 2%;
    }
    </style>

@endsection



@section('script')

@include('Layout.ValidatorJS')

    
    <script>



        /* 
        Solamente habrá uno de estos HTML activo a la vez.
        Como el codigo debe aparecer en 2 ventanas modal , (La ventana de ver meta y la de registrar la ejecucion de una ) 
        cada vez que abramos una , borraremos el codigo de la otra para luego insertarlo en esta.
        */
        htmlParaSeleccionarArchivos = 
            /* html */
            `
            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
            <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                    style="display: none" onchange="cambio()">  
                            <input type="hidden" name="nombreImgImagenEnvio" id="nombreImgImagenEnvio">                 
            <label class="label" for="filenames" style="font-size: 12pt;">       
                    <div id="divFileImagenEnvio" class="hovered">       
                    Subir archivos comprobantes  
                    <i class="fas fa-upload"></i>        
                </div>       
            </label> 
            `;
        

        
        $(window).load(function(){
            
            $(".loader").fadeOut("slow");
        });

        
        /* CODIGO PARA EXPORTAR A EXCEL LA TABLA 
        function exportar() {
            $tabla = document.querySelector("#tablaDetallesLugares");
            
            let tableExport = new TableExport($tabla, {
                exportButtons: false, // No queremos botones
                filename: "Reporte de prueba", //Nombre del archivo de Excel
                sheetname: "Reporte de prueba", //Título de la hoja
            });
            let datos = tableExport.getExportData();
            console.log(datos);
            console.log(datos.tablaDetallesLugares);
            let preferenciasDocumento = datos.tablaDetallesLugares.xlsx;
           
            tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, 
                preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, 
                preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
                
        }*/
    
        function clickVerArchivos(codMeta,mes){
            limpiarModalEjecucionMeta();

            document.getElementById('tituloModalArchivosMeta').innerHTML='Ver meta para <b>' + mes +'</b>';
            
            ruta = '/GestionProyectos/MetaEjecutada/'+codMeta+'/VerArchivos'
            invocarHtmlEnID(ruta,'ContenidoDescargarArchivos')


        }
        
        /* Limpia el inner html */
        function limpiarModalVerMeta(){
            divArchivos = document.getElementById('ContenidoDescargarArchivos');
            divArchivos.innerHTML = "";
        }


        /* Abre la ventana para ingresar una meta ejecutada */
        function clickIngresarMeta(codigo,cantidadProgramada,mes){
            $("#modalCodMetaEjecutada").val(codigo);
            $("#modalCantidadProgramada").val(cantidadProgramada);

            
            

            document.getElementById('divEnteroArchivo').innerHTML=htmlParaSeleccionarArchivos;
            document.getElementById('modalRegistrarEjecutadaLabel').innerHTML='Cantidad Ejecutada para <b>' + mes +'</b>';

            

            //$("#modal-mes").val(mes);
        }

        function limpiarModalEjecucionMeta(){
            document.getElementById('divEnteroArchivo').innerHTML="";
            $("#modalCodMetaEjecutada").val("");
            $("#modalCantidadProgramada").val("");
            document.getElementById('modalRegistrarEjecutadaLabel').innerHTML='';

        }


        function guardarEjecutada() {

            cantidadEjecutado=parseInt($("#modalCantidadEjecutada").val());
            console.log(cantidadEjecutado);

            if(cantidadEjecutado<=0){
                alerta("Debe ingresar una correcta cantidad ejecutada");
            }else document.modalForm.submit();
        }
        
        //se ejecuta cada vez que escogewmos un file
        function cambio(){
            msjError = validarPesoArchivos();
            if(msjError!=""){
                alerta(msjError);
                return;
            }
        
            listaArchivos="";
            cantidadArchivos = document.getElementById('filenames').files.length;
        
            console.log('----- Cant archivos seleccionados:' + cantidadArchivos);
            for (let index = 0; index < cantidadArchivos; index++) {
                nombreAr = document.getElementById('filenames').files[index].name;
                console.log('Archivo ' + index + ': '+ nombreAr);
                listaArchivos = listaArchivos +', '+  nombreAr; 
            }
            listaArchivos = listaArchivos.slice(1, listaArchivos.length);
            document.getElementById("divFileImagenEnvio").innerHTML= listaArchivos;
            $('#nombresArchivos').val(listaArchivos);
        }
                    
        function validarPesoArchivos(){
            cantidadArchivos = document.getElementById('filenames').files.length;
            
            msj="";
            for (let index = 0; index < cantidadArchivos; index++) {
                var imgsize = document.getElementById('filenames').files[index].size;
                nombre = document.getElementById('filenames').files[index].name;
                if(imgsize > {{App\Configuracion::pesoMaximoArchivoMB}}*1000*1000 ){
                    msj=('El archivo '+nombre+' supera los  {{App\Configuracion::pesoMaximoArchivoMB}}Mb, porfavor ingrese uno más liviano o comprima.');
                }
            }
            
        
            return msj;
        
        }    







            
        codMVAeliminar =0;
        function clickEliminarArchivo(codMedioVerificacion,nombre){
            codMVAeliminar = codMedioVerificacion;
            confirmarConMensaje("Confirmación","¿Desea eliminar el archivo "+nombre+"?","warning",ejecutarEliminacionArchivo);

        }
        
        function ejecutarEliminacionArchivo(){
            location.href = "/GestionProyectos/Gerente/eliminarMedioVerificacion/" + codMVAeliminar;

        }

        function clickActualizarMeta()
        {


            msj = validarPesoArchivos();
            if(msj!="")
            {
                alerta(msj);
                return;
            }
            if( document.getElementById('nuevaCantidadEjecutada').value == "" )
            {
                alerta("Debe ingresar la cantidad ejecutada.");
                return;
            }


            document.formAñadirArchivos.submit();

        }









    </script>
@endsection