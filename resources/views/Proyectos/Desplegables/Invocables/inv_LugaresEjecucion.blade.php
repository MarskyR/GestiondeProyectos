
<table  id="tablaDetallesLugares" class="table table-striped table-bordered table-condensed table-hover table-sm" style='background-color:#FFFFFF;'> 
    <thead class="thead-default" style="background-color:#3c8dbc;color: #fff; font-size:10pt">
        <tr>

            <th  class="text-center">Departamento</th>                                        
            <th class="text-center" >Provincia</th>                                 
            <th  class="text-center">Distrito</th>
            <th class="text-center">Lugar</th>
            @if($proyecto->sePuedeEditar())
                <th class="text-center"> Opc</th>
            @endif
        </tr>
        {{-- <th width="20%" class="text-center">Opciones</th>                                            
    --}}
    </thead>
    <tbody>
        @foreach($lugaresEjecucion as $itemLugarEjecucion)
            <tr class="selected">
                <td  style="text-align:center;">               
                    
                </td>       
                <td  style="text-align:center;"> 
                    
                </td>               
                            
                <td style="text-align:center;">               
                     
                </td>     
                <td class="text-center">
                    
                    
                </td>
                @if($proyecto->sePuedeEditar())
                <td class="text-center">

                        <a href="#" onclick="confirmarEliminarLugarEjecucion({{$itemLugarEjecucion->codLugarEjecucion}})">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        

                </td>
                @endif
            </tr>                
        @endforeach    
    </tbody>
</table>

<div class="modal fade" id="ModalAgregarLugar" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" id="frmAgregarLugar" name="frmAgregarLugar"  method="POST">
                @csrf
                <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
                <input type="{{App\Configuracion::getInputTextOHidden()}}" name="cantidadZonas" id="cantidadZonas" value="1">
                

                <div class="modal-header">
                    <h5 class="modal-title" id="">Agregar Lugar de Ejecución</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        


                        <label for="">Seleccionar Departamento:</label>
                        <select class="form-control " onchange="clickSelectDepartamento()" 
                                id="ComboBoxDepartamento" name="ComboBoxDepartamento">
                            <option value="-1" selected>
                            - Departamento -
                            </option>          
                            
                            @foreach($listaDepartamentos as $itemDepartamento)
                            <option value="{{$itemDepartamento->codDepartamento}}">
                                {{$itemDepartamento->nombre}}
                            </option>                                 
                            @endforeach
                        </select> 

                    

                        <label for="">Seleccionar Provincia:</label>
                        <select class="form-control"  id="ComboBoxProvincia" name="ComboBoxProvincia" onchange="clickSelectProvincia()" >
                            <option value="-1">Provincia</option>
                            
                        </select>  

                        
                        <label for="">Seleccionar Distrito:</label>
                        <select class="form-control"  id="ComboBoxDistrito" name="ComboBoxDistrito" onchange="" >
                            <option value="-1">Distrito</option>
                            
                        </select>  

                        <div class="fondoPlomoCircular mt-2">
                            
                            
                            <div class="row m-1 text-center">
                                <label for="zonaLugarEjecucion">
                                    Ingresar Zonas:
                                </label>
                            </div>
                              
                            <div class="row mt-2">
                                <div class="col text-center">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="agregarCampoLugarEjecucion()">
                                        Nueva Zona
                                        <i class="fas fa-plus fa-sm">
                                        </i> 
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarUltimaFila()">
                                        Eliminar Última
                                        <i class="fas fa-minus-circle"></i>
                                    </button>
                                </div>
                            </div>


                            <div id="listaDeZonas" class="text-center ml-2 mr-2">



                                <div class="row" id="fila1">
                                    <div class="col-8 mb-1">
                                        <input type="text" class="form-control" name="zonaLugarEjecucion1" id="zonaLugarEjecucion1"
                                        placeholder="Zona N°1">
                                    </div>
                                </div>


                                

                            </div>
                          

                            
                        </div>
                      
                       
                        

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="m-1 btn btn-primary" onclick="clickGuardarNuevoLugar()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
            </form>
        </div>
    </div>
</div>
