@extends('Layout.Plantilla')
@section('titulo')
Ver Rendición
@endsection
@section('estilos')
  
@endsection

@section('contenido')
<div >
    <p class="h1" style="text-align: center">Ver Rendición de Gastos</p>


</div>

<form method = "POST" action = "{{route('RendicionGastos.Empleado.Store')}}"  >
    
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepas" id="codigoCedepas" value="{{ $empleado->codigoCedepas }}">
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codigoSolicitud" id="codigoSolicitud" value="{{ $solicitud->codSolicitud }}">
    
    @csrf
    
        @include('RendicionGastos.PlantillaVerRG')
      
           
        
        

        {{-- LISTADO DE DETALLES  --}}
        
        <div class="table-responsive">                           
            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
                
                <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                    <th width="13%" class="text-center">Fecha</th>                                        
                    <th width="13%">Tipo CDP</th>
                            
                    <th width="10%"> N° Cbte</th>
                    <th width="20%" class="text-center">Concepto </th>
                    <th width="10%" class="text-center">Importe </th>
                    <th width="10%" class="text-center">Cod Presupuesto </th>
                    
                    @if($rendicion->verificarEstado('Contabilizada'))
                        <th>Contabilizado</th>  
                    @endif
                </thead>
                
                <tbody>
                    @foreach($detallesRend as $itemDetalle)
                        <tr class="selected" id="filaItem" name="filaItem">                
                            <td style="text-align:center;">     
                                {{$itemDetalle->getFecha()  }}
                                
                            </td>               
                        
                            <td style="text-align:center;">               
                                {{$itemDetalle->getNombreTipoCDP()  }}
                            </td>               
                            
                        
                            <td style="text-align:center;">               
                                {{$itemDetalle->nroComprobante  }}
                            </td>               
                            <td> 
                                {{$itemDetalle->concepto  }}
                            
                            </td>               
                            <td  style="text-align:right;">               
                                {{$rendicion->getMoneda()->simbolo}} {{  number_format($itemDetalle->importe,2)  }}
                            </td>               
                            <td style="text-align:center;">               
                                {{$itemDetalle->codigoPresupuestal  }}
                            </td>               
                                        

                            @if($rendicion->verificarEstado('Contabilizada'))
                                <td style="text-align:center;">               
                                    <input type="checkbox" onclick="return false;"
                                        @if($itemDetalle->contabilizado=='1')
                                            checked
                                        @endif
                                    >
                                </td> 
                            @endif

                        </tr> 
                    @endforeach
                            

                </tbody>
            </table>
        </div> 
            
        <div class="row" id="divTotal" name="divTotal">    
            <div class="col">
                @include('RendicionGastos.DesplegableDescargarArchivosRend')
                



                <div id="guardar">
                    <div class="form-group"><!--
                        <a href="{{route('RendicionGastos.ListarRendiciones')}}" 
                            class='btn btn-primary' style="float:left;">
                            <i class="fas fa-undo"></i>
                            Regresar al menú
                        </a>    -->
                        <a href="{{route('RendicionGastos.Empleado.Listar')}}" class='btn btn-info float-left'>
                            <i class="fas fa-arrow-left"></i>
                            Regresar al Menú
                        </a>        
                    </div>    
                </div>
                

            </div>
            
            <div class="col">
                <div class="row">
                        
                    <div class="col">                        
                        <label for="">Total Rendido/Gastado: </label>    
                    </div>   
                    <div class="col">
                        {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                        <input type="hidden" name="cantElementos" id="cantElementos">                              
                        <input type="text" class="form-control text-right" name="total" id="total" readonly value="{{number_format(($rendicion->totalImporteRendido),2)}}">                              
                    </div>   

                    <div class="w-100"></div>
                    
                    <div class="col">                        
                        <label for="">Total Recibido: </label>    
                    </div>   
                    <div class="col">
                    
                        <input type="text" class="form-control text-right" name="total" id="total" readonly value="{{number_format($rendicion->totalImporteRecibido,2)}}">                              
                    </div>   
                    <div class="w-100"></div>  
                    <div class="col">                        
                        <label for="">

                            @if($rendicion->saldoAFavorDeEmpleado>0) {{-- pal empl --}}
                                Saldo a favor del Colaborador: 
                            @else
                                Saldo a favor de Cedepas: 
                            @endif
                            
                        </label>    
                    </div>   
                    <div class="col">
                    
                        <input type="text" class="form-control text-right" name="total" id="total" 
                        readonly value="{{number_format(abs($rendicion->saldoAFavorDeEmpleado),2)}}">                              
                    </div>   
                    <div class="w-100"></div>

                    


                </div>
            </div>

                
            
            
        </div>
                    

        
</form>
@endsection

{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}

<style>
    input[type='checkbox'] {
        /* -webkit-appearance:none; */
        width:25px;
        height:25px;
        background:white;
        border-radius:15px;
        border:2px solid #555;
    }
    input[type='checkbox']:checked {
        background: #abd;
    }
</style>