@extends('Layout.Plantilla')

@section('titulo')
    Registrar Metas
@endsection

@section('contenido')

    <link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
    <link rel="stylesheet" href="/select2/bootstrap-select.min.css">

    <br>
    <div class="card">
        <div class="card-header" style=" ">
        <div class="row">
            <div class="col">
                
                <h3>Proyecto:</h3>
                {{$proyecto->nombre}}
            </div>

            <div class="col">
                <h3>Gerente: </h3>
                
                {{$proyecto->getGerente()->getNombreCompleto()}}
            </div>
            
            <div class="col">
                <h3>Fecha de inicio: </h3>
                
                {{$proyecto->fechaInicio}}
            </div>
            
            
        </div>
        </div>
    </div>
    @include('Layout.MensajeEmergenteDatos')
    <div class="table-responsive " style="margin: 5px">                           
        <table  id="tablaDetallesLugares" class="table table-striped table-bordered table-condensed table-hover table-sm" 
                style='background-color:#FFFFFF;'> 
            <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                
                
                <form action="{{route('GestionProyectos.agregarLugarEjecucion')}}" method="POST">
                    @csrf
                    <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                             
                </form>

                <tr>
                    <th rowspan="2" class="text-center">Enunciado</th>     
                    <th rowspan="2">Meta</th>
                    <th rowspan="2">Saldo</th>
                    <th rowspan="2">Ejecucion</th>

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

                {{-- Falta lógica para obtener numero de meses --}}
                @foreach ($proyecto->getResultadosEsperados() as $resultado)
                    
                    <tr>
                        <td colspan="{{$proyecto->getCantidadColsParaReporteRes()}}">
                            {{$resultado->descripcion}}


                        </td>
                    </tr>

                    @foreach ($resultado->getListaActividades() as $actividad)
                        <tr>
                            <td colspan="{{$proyecto->getCantidadColsParaReporteRes()}}">
                                &nbsp;  &nbsp; {{$actividad->descripcion}}
                            </td>
                        </tr>

                        @foreach ($actividad->getListaIndicadores() as $indicador)
                            <tr>
                                <td>
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$indicador->descripcion}}
                                    
                                    
                                </td>
                                
                                <td>
                                    {{$indicador->meta}}
                                </td>
                                <td>
                                    {{$indicador->saldoPendiente}}
                                </td>
                                <td style="color: {{$indicador->getColorPorcentajeEjecucion()}}">
                                    {{$indicador->calcularPorcentajeEjecucion()}}
                                    {{-- <a href="{{route('IndicadorActividad.Ver',$indicador->codIndicadorActividad )}}" 
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-chart-bar"></i>
                                    </a> --}}
                                </td>
                                    
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
                                                <button class="btn btn-info" type="button" onclick="clickVerArchivos({{$indicador->getMeta($mes)->codMetaEjecutada}})" id="btnModalDescargarArchivos" data-toggle="modal" data-target="#modalArchivosMeta">
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

                    @endforeach
                    
                

                @endforeach
            </tbody>
        </table>
    </div> 

    <div></div>
    

    
    <div class="modal fade" id="modalRegistrarEjecutada" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRegistrarEjecutadaLabel">New message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                            </div>    

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEjecutada()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalArchivosMeta" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Descargar Archivos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div id="ContenidoDescargarArchivos">

                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="clickSalirDeVerArchivos()" type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>
                </div>
            </div>
        </div>
    </div>
    <button id="btnExportar" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Exportar datos a Excel
    </button>

    <div class="row">
        <div class="col">
            <a href="{{route('GestiónProyectos.Gerente.Listar')}}"class="btn btn-success">
                <i class="fas fa-arrow-left"></i> 
                Regresar a proyectos
            </a>
        </div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
        
    </div>

    <style>
        .SinRegistro{
            background-color: #9c9c9c;
        }

    </style>

@endsection



@section('script')


    <script>
        function clickVerArchivos(codMeta){
            divArchivos = document.getElementById('ContenidoDescargarArchivos');
            console.log('hola');
            $.get('/GestionProyectos/MetaEjecutada/'+codMeta+'/VerArchivos', 
                function(data)
                {                       
                    console.log('Se ha actualizado la ventana emergente para la meta:[' +codMeta+"]" );
                    divArchivos.innerHTML = data;
                }
                );

        }
        
        /* Limpia el inner html */
        function clickSalirDeVerArchivos(){
            divArchivos = document.getElementById('ContenidoDescargarArchivos');
            divArchivos.innerHTML = "";
        }

        function clickIngresarMeta(codigo,cantidadProgramada,mes){
            $("#modalCodMetaEjecutada").val(codigo);
            $("#modalCantidadProgramada").val(cantidadProgramada);

            document.getElementById('modalRegistrarEjecutadaLabel').innerHTML='Cantidad Ejecutada para <b>' + mes +'</b>';

            //$("#modal-mes").val(mes);
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

    </script>
@endsection