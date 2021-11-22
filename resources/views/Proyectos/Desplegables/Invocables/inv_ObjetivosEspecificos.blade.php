


    @csrf
    @if($proyecto->sePuedeEditar())
        <div class="row">
            
    
            <div class="col">
                    
                <button type="button" id="" class="btn btn-primary m-2" onclick="clickAgregarObjEspecifico()"
                    data-toggle="modal" data-target="#ModalObjetivoEspecifico">
                    Agregar Objetivo Específico
                    <i class="fas fa-plus"></i>
                </button>


                {{-- Este modal sirve tanto para agregar como para editar --}}
                <div class="modal fade" id="ModalObjetivoEspecifico" tabindex="-1" aria-labelledby="" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            
    
                                <div class="modal-header">
                                    <h5 class="modal-title" id="tituloModalObjetivoEspecifico">Agregar Objetivo Específico</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="" method="POST" id="frmAgregarObjEspec" name="frmAgregarObjEspec">
                                        
                                        @csrf
                                        <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">

                                        {{-- Si se creará uno nuevo, está en 0, si se va a editar tiene el codigo del obj a editar --}}
                                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codObjetivoEspecifico" id="codObjetivoEspecifico" value="0">

                                        <label for="">Descripción del objetivo:</label>
                                        <textarea class="form-control" name="descripcionObjetivo" id="descripcionObjetivo" cols="15" rows="6"
                                        ></textarea>
        
                                    </form>
    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Salir
                                    </button>
    
                                    <button type="button" class="btn btn-primary"   onclick="agregarObjetivoEspecifico()">
                                        Guardar <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="col">
                    <button type="button" id="" class="btn btn-primary m-2" onclick="clickAgregarIndicadorObjetivo()"
                        data-toggle="modal" data-target="#ModalIndicadorObjetivoEspecifico">
                        Agregar Indicador
                        <i class="fas fa-plus"></i>
                    </button>



                    <div class="modal fade" id="ModalIndicadorObjetivoEspecifico" tabindex="-1" aria-labelledby="" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                
        
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="TituloModalIndicadorObjetivoEspecifico">Agregar Indicador</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formAgregarEditarIndicador" name="formAgregarEditarIndicador" action="{{route('GestionProyectos.agregarEditarIndicador')}}" method="POST">
                                            <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">

                                            {{-- Si se creará uno nuevo, está en 0, si se va a editar tiene el codigo del indicador a editar --}}
                                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codIndicadorObjetivo" id="codIndicadorObjetivo" value="0">

                                            
                                            @csrf
                                            
                                            <label for="">Seleccionar Objetivo</label>
                                            <select class="form-control"  id="ComboBoxObjetivoEspecifico" name="ComboBoxObjetivoEspecifico" onchange="" >
                                                <option value="-1"> -- Obj Esp -- </option>
                                                @foreach($listaObjetivosEspecificos as $itemObjEspecifico)
                                                    <option value="{{$itemObjEspecifico->codObjEspecifico}}">
                                                        {{$itemObjEspecifico->getDescripcionAbreviada()}}
                                                    </option>
                                                    
                                                @endforeach
                                            </select> 
                                            <label for="">Descripción del Indicador</label>
                                            <textarea class="form-control" name="descripcionNuevoIndicador" id="descripcionNuevoIndicador" cols="3" rows="8"
                                            ></textarea>
                                            
                                            
                
                                        </form>
                
        
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Salir
                                        </button>
                                        
                                        <button type="button"  onclick="clickAgregarIndicadorObjEsp()" class="btn btn-primary">
                                        Agregar <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                
                            </div>
                        </div>
                    </div>


            </div>
        </div>

    @endif


    <div class="table-responsive " >                           
        <table  id="TablaObjEsp" class="table table-striped table-bordered table-condensed table-hover table-sm" style='background-color:#FFFFFF;'> 
            <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                

                <th  class="text-center">Obj Esp</th>    
                
{{--                 @if($proyecto->sePuedeEditar())
                    <th class="text-center">Opciones</th>                                    
                @endif --}}
                <th class="text-center"> Indicadores</th>
                
{{--                 @if($proyecto->sePuedeEditar())
                    <th class="text-center">Opciones</th>
                @endif --}}
                
            </thead>
            <tbody>
                @php    /* ESTO ES PARA NUMERAR LAS SUBDIVISIONES, ESTA NUMERACIÓN ES SOLO VISUAL, NO SE ALMACENA EN LA BD */
                    $i=1;
                @endphp

                {{-- @foreach($listaObjetivosEspecificos as $itemObjEspecifico)
                    <tr  class="selected">
                        <td colspan="1">   
                            <b>{{$i.". "}}</b>    
                                    
                            {{$itemObjEspecifico->descripcion}}
                            
                        </td>       

                        @if($proyecto->sePuedeEditar())
                        <td class="text-center">
                            <button href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ModalObjetivoEspecifico"
                                onclick="clickEditarObjEspecifico('{{$itemObjEspecifico->codObjEspecifico}}',`{{$itemObjEspecifico->descripcion}}`)">
                                <i class="fas fa-pen"></i>   
                            </button>
                            <a href="#" onclick="clickEliminarObjEspecifico({{$itemObjEspecifico->codObjEspecifico}})" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>   
                            </a>
                        </td>
                        @endif

                        
                        <td></td>
                        @if($proyecto->sePuedeEditar())
                            <td></td>
                        @endif

                    </tr>    
                    @php
                        $j=1;
                    @endphp        

                    @foreach ($itemObjEspecifico->getListaDeIndicadores() as $itemIndicadorObjEsp)
                        <tr>
                            <td></td>
                            @if($proyecto->sePuedeEditar())
                                <td></td>
                            @endif

                            <td colspan="1">
                                <b>{{$i.".".$j." "}}</b>
                                        
                                {{$itemIndicadorObjEsp->descripcion}}
                            </td>
                            @if($proyecto->sePuedeEditar())                                    
                            <td class="text-center">
                                <button type="button" href="#" class="btn btn-info btn-sm " data-toggle="modal" data-target="#ModalIndicadorObjetivoEspecifico"
                                    onclick="clickEditarIndicadorObjetivo('{{$itemIndicadorObjEsp->codIndicadorObj}}')">
                                    <i class="fas fa-pen"></i>   
                                </button>
                                <a href="#" onclick="clickEliminarIndicador({{$itemIndicadorObjEsp->codIndicadorObj}})" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>   
                                </a>
                            </td>
                            @endif
                        </tr>
                        @php
                            $j++;
                        @endphp

                    @endforeach

                    @php
                    $i++;
                    @endphp
                @endforeach   --}}  
            </tbody>
        </table>
    </div> 




 