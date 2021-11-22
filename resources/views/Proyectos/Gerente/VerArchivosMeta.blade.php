<form id="formAñadirArchivos" name="formAñadirArchivos" action="{{route('Metas.Actualizar')}}" 
    method="post"  enctype="multipart/form-data">
            
    @php
        $empLogeado = App\Empleado::getEmpleadoLogeado();
    @endphp
    
    <div class="row">
        <div class="col">
            
            Meta programada el 
            
            <b>{{$meta->getFechaRegistroProgramacion()}}</b>
            con una cantidad objetivo de 
            <b>{{$meta->cantidadProgramada}}</b>
            .Se registró la ejecución de la meta el 
            <b>{{$meta->getFechaRegistroEjecucion()}}</b>
            por una cantidad de 
            <input type="text" style="width: 100px" class="form-control" id="nuevaCantidadEjecutada" name="nuevaCantidadEjecutada" value="{{$meta->cantidadEjecutada}}"
            @if($meta->sePuedeEditar() && $empLogeado->esGerente()) {{-- Solo el gerente puede editar, la UGE No --}}
                
            @else
                readonly
            @endif
            
            > 
            Cumpliendo el 
            <b style="color: {{$meta->getColor()}}" >
                {{$meta->getEjecucion()}}
            </b>
            de lo programado. 

        </div>
        


    </div>

    <h4 >Descargar medios de verificación</h4>
    <table class="table table-striped table-bordered table-condensed table-hover" 
        style='background-color:#FFFFFF;'>
        <tbody>
            @foreach ($meta->getListaArchivos() as $itemArchivo)
            <tr>
                <td style = "padding: 0.50rem">
                <a href="{{route('MetaEjecutada.DescargarMedioVerificacion',$itemArchivo->codMedioVerificacion)}}">
                        <i id="" class="far fa-address-card nav-icon"></i>
                        {{$itemArchivo->nombreAparente}}
                    </a>
                </td>

                @if($meta->sePuedeEditar() && $empLogeado->esGerente())
                        
                    <td>
                        <a href="#" onclick="clickEliminarArchivo({{$itemArchivo->codMedioVerificacion}},'{{$itemArchivo->nombreAparente}}')">
                        <i class="fas fa-trash" title="Eliminar Archivo"></i>
                        </a>
                    </td>
                
                @endif
            </tr>
            @endforeach

            @if($meta->getCantidadArchivos()==0)
                <tr>
                <td>
                    No hay archivos para mostrar.
                </td>
                </tr>
            @endif

        </tbody>
    </table>

    @if($meta->sePuedeEditar())
        @if($empLogeado->esGerente())
            <div class="row">

                <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codMetaParaArchivos" id="codMetaParaArchivos" value="{{$meta->codMetaEjecutada}}">
                @csrf
                
                <div class="col BordeCircular" id="divAñadirArchivos">    
                    <div class="row">
                        <div class="col ">
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_añadir" value="1">
                                <label class="form-check-label" for="ar_añadir">
                                    Añadir Archivos
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_sobrescribir" value="2">
                                <label class="form-check-label" for="ar_sobrescribir">
                                    Sobrescribir archivos
                                </label>
                            </div>
                    

                        </div>
                        <div class="w-100"></div>
                        <div class="col">
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
                            <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                                    style="display: none" onchange="cambio()">  
                                            <input type="hidden" name="nombreImgImagenEnvio" id="nombreImgImagenEnvio">                 
                            <label class="label" for="filenames" style="font-size: 12pt;">       
                                    <div id="divFileImagenEnvio" class="hovered">       
                                    Seleccionar Archivos
                                    <i class="fas fa-upload"></i>        
                                </div>       
                            </label>  

                        </div>
                    </div>
                
                    
                </div>    
                
                <div class="col">
                <button type="button" class="btn btn-primary float-right" 
                    id="" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                    onclick="clickActualizarMeta()">
                    <i class='fas fa-save'></i> 
                    Guardar
                </button> 
                </div>


            </div>

            <label style="color:green" for="">
                Esta meta ejecutada se puede editar hasta el día 
                {{$meta->getDiaMaximoActualizacion()}}
            </label>
        @else {{-- UGE --}}
            <label style="color:green" for="">
                Esta meta ejecutada puede ser editada por el gerente hasta el día {{$meta->getDiaMaximoActualizacion()}}
            </label>
        @endif

    @else
        <label style="color:red" for="">
            @if($empLogeado->esGerente())
                Esta meta ya no se puede editar
            @else
                Esta meta ya no puede ser editada por el gerente. 
            @endif
        </label>
    @endif
</form>

<script>
    

</script>