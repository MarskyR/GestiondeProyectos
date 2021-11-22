
@if($proyecto->sePuedeEditar())
    {{-- aqui boton para abrir modal --}}
    <button type="button" id="" class="btn btn-primary m-2" onclick="limpiarModalPoblacionBeneficiaria()"
        data-toggle="modal" data-target="#ModalPoblacionBeneficiaria">
        Agregar Población Beneficiaria
        <i class="fas fa-plus"></i>
    </button>

@endif


<div class="table-responsive ">                           
    <table  id="tablaDetallesLugares" class="m-2 table-sm table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF; font-size:10pt'> 
        <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
            <th  class="text-center">Descripcion</th>                                        
            <th> Opc</th>
        
        </thead>
        <tbody>
            @php
                $i=1;
            @endphp
            @foreach($poblacionesBeneficiarias as $itemPobBen)
                <tr class="selected">
                    <td  style="">               
                        {{-- <b>{{$i.". "}}</b>  {{$itemPobBen->descripcion}} --}}
                    </td>       
                    
                    <td style="text-align: center;">

                            
{{--                             <a href="{{route('GestionProyectos.verPoblacionBeneficiaria',$itemPobBen->codPoblacionBeneficiaria)}}" class="btn btn-success  btn-sm">
                                <i class="fas fa-users"></i>
                                Ver Lista
                            </a> --}}
                            @if($proyecto->sePuedeEditar())
{{--                                 <button type="button" href="#" onclick="clickEditarPoblacionBeneficiaria({{$itemPobBen->codPoblacionBeneficiaria}})" 
                                    class="btn btn-info btn-sm" data-toggle="modal" data-target="#ModalPoblacionBeneficiaria">
                                    <i class="fas fa-pen"></i>
                                </button> --}}
                                
{{--                                 <a href="#" onclick="confirmarEliminarPoblacionBeneficiaria({{$itemPobBen->codPoblacionBeneficiaria}})" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </a> --}}
                            @endif
                    </td>
                            
                </tr>          
                @php
                    $i++;
                @endphp      
            @endforeach    
        </tbody>
    </table>
</div> 

<div class="row">
    <div class="col m-2">
        
        <a href="{{route('GestiónProyectos.ExportarPoblacion',$proyecto->codProyecto)}}" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            Descargar Reporte de población
        </a>
    </div>
    
</div>