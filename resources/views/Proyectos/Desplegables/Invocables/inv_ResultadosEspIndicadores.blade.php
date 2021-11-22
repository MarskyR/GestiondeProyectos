 

@if($proyecto->sePuedeEditar())
    <div class="row">
        <div class="col">

            <button type="button" id="" class="btn btn-primary m-2" onclick="limpiarModalResultadoEsperado()"
                data-toggle="modal" data-target="#ModalAgregarResultadoEsperado">
                Agregar Resultado Esperado 
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <div class="col"  >
            <button type="button" id="" class="btn btn-primary m-2" onclick="limpiarModalIndicadorResultado()"
                data-toggle="modal" data-target="#ModalAgregarIndicadorResultado">
                Agregar Indicador de Resultado
                <i class="fas fa-plus"></i>
            </button>
            <div class="modal fade" id="ModalAgregarIndicadorResultado" tabindex="-1" aria-labelledby="" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        

                            <div class="modal-header">
                                <h5 class="modal-title" id="">Agregar Indicador de Resultado</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                                        
                                <form id="frmAgregarIndicadorResultado" name="frmAgregarIndicadorResultado" action="" method="POST">
                                    <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                                    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codIndicadorResultado" id="codIndicadorResultado" value="0">
            
                                    @csrf
                                    
                                    <div class="row">
                                    
                                        <div class="col">

                
                                            <label for="">Seleccionar Resultado Esperado</label>
                                            <select class="form-control"  id="ComboBoxResultadoEsperadoX" name="ComboBoxResultadoEsperadoX" onchange="" >
                                                <option value="-1"> -- Res Esp -- </option>
                                                @foreach($listaResultadosEsperados as $itemResEsperado)
                                                    <option value="{{$itemResEsperado->codResultadoEsperado}}">
                                                        {{$itemResEsperado->getDescripcionAbreviada()}}
                                                    </option>
                                                    
                                                @endforeach 
                                            </select> 
                
                                        </div>

                                        
                                        <div class="w-100"></div>
                                        <div class="col">
                                            <label for="">Descripción del Indicador</label>
                                            <textarea class="form-control" name="descripcionNuevoIndicadorResultado" id="descripcionNuevoIndicadorResultado" cols="3" rows="2"
                                            ></textarea>

                                        </div>

                                    </div>                            
                                
                                </form>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Salir
                                </button>

                                <button type="button" class="m-1 btn btn-primary" onclick="clickAgregarIndicadorResultado()">
                                    Guardar <i class="fas fa-save"></i>
                                </button>   
                            </div>
                        
                    </div>
                </div>
            </div>
            
        </div>    

        <div class="col">

            <button type="button" id="" class="btn btn-primary m-2" onclick="limpiarMedioVerificacion()"
                data-toggle="modal" data-target="#ModalMedioVerificacion">
                Agregar Medio Verificacion
                <i class="fas fa-plus"></i>
            </button>
            <div class="modal fade" id="ModalMedioVerificacion" tabindex="-1" aria-labelledby="" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        

                            <div class="modal-header">
                                <h5 class="modal-title" id="TituloModalMedioVerificacion">Agregar Medio de Verificación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                
                                <form id="frmMedioVerificacion" name="frmMedioVerificacion" 
                                    action="{{route('GestionProyectos.agregarMedioVerificacion')}}" method="POST"  enctype="multipart/form-data">
                                    <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                                    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codMedioVerificacion" id="codMedioVerificacion" value="0">
            

                                    @csrf
                                    
                                    <div class="row">
                                    
                                        <div class="col">
        
                
                                            <label for="">Seleccionar Indicador</label>
                                            <select class="form-control"  id="ComboBoxIndicadoresResultados" name="ComboBoxIndicadoresResultados" onchange="" >
                                                <option value="-1"> -- Res Esp -- </option>
                                                @foreach($listaIndicadoresResultados as $itemIndicadorResultado)
                                                    <option value="{{$itemIndicadorResultado->codIndicadorResultado}}">
                                                        {{$itemIndicadorResultado->getDescripcionAbreviada()}}
                                                    </option>
                                                    
                                                @endforeach
                                            </select> 
                
                                        </div>
        
                                        
                                        <div class="w-100"></div>
                                        <div class="col">
                                            <label for="">Descripción del Medio</label>
                                            <textarea class="form-control" name="descripcionNuevoMedio" id="descripcionNuevoMedio" cols="3" rows="2"
                                            ></textarea>
        
                                        </div>
        
                                        <div class="w-100"></div>
                                        <div class="col">
                                                
                                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombreArchivoMedioVerificacion" id="nombreArchivoMedioVerificacion" value="">
                                            <input type="file" class="btn btn-primary" name="filenamesMedio" id="filenamesMedio"        
                                                    style="display: none" onchange="cambioArchivoMedioVer()">              
                                            <label class="label" for="filenamesMedio" style="font-size: 12pt;">       
                                                <div id="divFileImagenEnvio2" class="hovered">       
                                                    Subir archivo
                                                    <i class="fas fa-upload"></i>        
                                                </div>       
                                            </label>       
                                    
                                        </div>
                                        
        
                                    </div>                            
                                
        
                                </form>


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Salir
                                </button>

                                <button type="button" class="m-1 btn btn-primary" onclick="clickGuardarNuevoMedioVerificacion()">
                                    Guardar <i class="fas fa-save"></i>
                                </button>   
                            </div>
                        
                    </div>
                </div>
            </div>
            

            

        </div>

    </div>
@endif

<div class="table-responsive divTablaFijada" >          
    <table id="tabla01" class="table table-striped table-bordered table-condensed table-hover table-sm fontSize11" style='background-color:#FFFFFF;'> 
        <thead class="filaFijada fondoAzul letrasBlancas">
            <tr>
                <th scope="col" width="10%">Resultado Esperado</th>
{{--                 @if($proyecto->sePuedeEditar())
                    <th scope="col" width="5%">Opc</th>
                @endif --}}
                <th scope="col" width="10%">Indicador</th>

{{--                 @if($proyecto->sePuedeEditar())
                    <th scope="col" width="5%">Opc</th>
                @endif --}}
                
                <th scope="col" width="10%">Medio de Verificacion</th>
                <th scope="col" width="5%">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i=1;
            @endphp
            
            {{-- @foreach($listaResultadosEsperados as $itemResultado)  
                <tr>
                    <td >
                        <b>{{$i.". "}}</b>
                                
                        {{$itemResultado->descripcion}}
                    </td>         
                    @if($proyecto->sePuedeEditar())
    
                        <td class="text-center">
                            <button href="#" onclick="clickEditarResultadoEsperado({{$itemResultado->codResultadoEsperado}})" class="btn btn-info btn-sm "
                                data-toggle="modal" data-target="#ModalAgregarResultadoEsperado">
                                <i class="fas fa-pen"></i>   
                            </button>
                            <a href="#" onclick="clickEliminarResultadoEsperado({{$itemResultado->codResultadoEsperado}} )" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>   
                            </a>
                        </td>
                    @endif

                    <td></td>
                    @if($proyecto->sePuedeEditar())
                        <td></td>
                    @endif

                    <td></td>
                    <td></td>
                    

                </tr>
                @php
                $j=1;
                @endphp
                @foreach ($itemResultado->getListaIndicadoresResultados() as $indicadorRes)
                    <tr>
                        <td></td>
                        @if($proyecto->sePuedeEditar())
                            <td></td>
                        @endif

                        <td>
                            <b>{{$i.".".$j." "}}</b>
                            
                            {{$indicadorRes->descripcion}}
                        </td>
                        @if($proyecto->sePuedeEditar())
                            <td class="text-center">
                                <button href="#" onclick="clickEditarIndicadorResultado({{$indicadorRes->codIndicadorResultado}})" 
                                    class="btn btn-info btn-sm"  data-toggle="modal" data-target="#ModalAgregarIndicadorResultado" >
                                    <i class="fas fa-pen"></i>   
                                </button>
                                <a href="#" onclick="clickEliminarIndicadorResultado({{$indicadorRes->codIndicadorResultado}})" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>   
                                </a>
                            </td>
                        @endif

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
                            @if($proyecto->sePuedeEditar())
                            <td></td>
                            <td></td>
                            @endif

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
                                

                                @if($proyecto->sePuedeEditar())
                                    <a href="#" onclick="clickEditarMedioVerificacion({{$medioVerif->codMedioVerificacion}})" class="btn btn-info btn-sm"
                                        data-toggle="modal" data-target="#ModalMedioVerificacion">
                                        <i class="fas fa-pen  fa-sm"></i>   
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm" onclick="clickEliminarMedioVerificacion({{$medioVerif->codMedioVerificacion}})">
                                        <i class="fas fa-trash  fa-sm"></i>   
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
            @endforeach --}}
            

        </tbody>
    </table>
    {{-- 
        
        ****************************************************************************************
        TABLA RES -> ACTIVIDADES -> INDICADORES
        --}}

</div> 
