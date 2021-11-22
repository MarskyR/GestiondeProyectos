 
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
            
            <button type="button" id="" class="btn btn-primary m-2" onclick="limpiarModalActividad()"
                data-toggle="modal" data-target="#ModalActividad">
                Agregar Actividad
                <i class="fas fa-plus"></i>
            </button>



            
            
            
        </div>
        <div class="col">

            
            <button type="button" id="" class="btn btn-primary m-2" onclick="limpiarModalIndicador()" 
                data-toggle="modal" data-target="#ModalIndicadorActividad">
                Agregar Indicador
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
@endif


<div class="table-responsive divTablaFijada" style="margin: 5px">          
             
    {{-- 
        
        ****************************************************************************************
        TABLA RES -> ACTIVIDADES -> INDICADORES
        --}}
    
    
    <table id="" class="table table-striped table-bordered table-condensed table-hover table-sm fontSize11" style=''> 
        <thead class="filaFijada fondoAzul letrasBlancas">
            <th width="10%">Resultado Esperado</th>
            @if($proyecto->sePuedeEditar())
                <th width="5%">Opciones</th>
            @endif

            <th width="10%">Actividad</th>
            
            @if($proyecto->sePuedeEditar())
                <th width="5%">Opciones</th>
            @endif
             
            <th width="10%">Indicador de Actividad</th>
            <th width="5%">Opciones</th>

        </thead>
        <tbody>
            @php
                $i=1;
            @endphp
            {{-- @foreach ($listaResultadosEsperados  as $itemResultado)
                <tr>
                    <td>
                        <b>{{$i.". "}} </b>       
                        {{$itemResultado->descripcion}}
                    </td>
                    @if($proyecto->sePuedeEditar())
                    <td>
                        <button href="#" onclick="clickEditarResultadoEsperado({{$itemResultado->codResultadoEsperado}})" class="btn btn-info btn-sm"
                            data-toggle="modal" data-target="#ModalAgregarResultadoEsperado">
                            <i class="fas fa-pen  fa-sm"></i>   
                        </button>
                        <button href="#"  onclick="clickEliminarResultadoEsperado({{$itemResultado->codResultadoEsperado}} )" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash  fa-sm"></i>   
                        </button>
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
                        @if($proyecto->sePuedeEditar())
                        <td>
                            <button type="button" href="#" class="btn btn-info btn-sm " onclick="clickEditarActividad({{$actividad->codActividad}})"
                            data-toggle="modal" data-target="#ModalActividad">
                                <i class="fas fa-pen  fa-sm"></i>   
                            </button>
                            <button type="button" href="#" class="btn btn-danger btn-sm" onclick="clickEliminarActividad({{$actividad->codActividad}})" >
                                <i class="fas fa-trash  fa-sm"></i>   
                            </button>
                        </td>
                        @endif
                    
                        <td></td>
                        <td></td>
                      
                        
                    </tr>
                    @php
                        $k=1;
                    @endphp
                    @foreach ($actividad->getListaIndicadores() as $indicador)
                        <tr>
                            <td></td>
                            @if($proyecto->sePuedeEditar())
                                <td></td>
                            @endif
                            <td></td>
                            @if($proyecto->sePuedeEditar())
                                <td></td>
                            @endif
                            
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
            @endforeach --}}


        </tbody>
    </table>
</div> 



{{-- ------------------------- MODALES EMERGENTES ------------------------}}



<div class="modal fade" id="ModalIndicadorActividad" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            

                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalIndicadorActividad">Agregar Indicador</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    

                    <form id="frmAgregarIndicador" name="frmAgregarIndicador" action="{{route('GestionProyectos.agregarEditarIndicadorActividad')}}" method="POST">
                        <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codIndicadorActividad" id="codIndicadorActividad" value="0">
                        
                        @csrf
                        
                        <div class="row">
                           
                            <div class="col">

    
                                <label for="">Seleccionar Actividad</label>
                                <select class="form-control"  id="ComboBoxActividad" name="ComboBoxActividad" onchange="" >
                                    <option value="-1" selected> -- Actividad -- </option>
                                    @foreach($listaActividades as $itemActividad)
                                        <option value="{{$itemActividad->codActividad}}">
                                            {{$itemActividad->getDescripcionAbreviada()}}
                                        </option>
                                        
                                    @endforeach
                                </select> 
    
                            </div>

                            
                            <div class="w-100"></div>
                            <div class="col">
                                <label for="">Meta</label>
                                <input type="text" class="form-control" name="" placeholder="Se calculará en base a la programación de metas mensuales." id="metaIndicador" readonly>
                            </div>
                            <div class="w-100"></div>


                            <div class="col">
                                <label for="">Unidad de Medida</label>
                                <textarea class="form-control" name="unidadNuevoIndicador" id="unidadNuevoIndicador" cols="" rows="5"
                                ></textarea>
                                
                            </div>
                            
                        </div>                            
                        
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="m-1 btn btn-primary" onclick="clickAgregarIndicador()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
        </div>
    </div>
</div>


<div class="modal fade" id="ModalActividad" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            

                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalActividad">Agregar Actividad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmActividad" name="frmActividad" action="{{route('GestionProyectos.agregarEditarActividad')}}" method="POST">
                        <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codActividad" id="codActividad" value="0">
                        
                        @csrf
                        
                        <div class="row">
                           
                            <div class="col">

    
                                <label for="">Seleccionar Resultado Esperado</label>
                                <select class="form-control"  id="ComboBoxResultadoEsperado" name="ComboBoxResultadoEsperado" onchange="" >
                                    <option value="-1" selected> -- Res Esp -- </option>
                                    @foreach($listaResultadosEsperados as $itemResEsperado)
                                        <option value="{{$itemResEsperado->codResultadoEsperado}}">
                                            {{$itemResEsperado->getDescripcionAbreviada()}}
                                        </option>
                                        
                                    @endforeach
                                </select> 
    
                            </div>

                            
                            <div class="w-100"></div>
                            <div class="col">
                                <label for="">Descripción de la actividad</label>
                                <textarea class="form-control" name="descripcionNuevaActividad" id="descripcionNuevaActividad" cols="3" rows="6"
                                ></textarea>

                            </div>

                        </div>                            
                        
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="m-1 btn btn-primary" onclick="clickAgregarActividad()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
        </div>
    </div>
</div>
