@extends('Layout.Plantilla')

@section('titulo')
  Ver Orden de Compra
@endsection

@section('contenido')
<style>
    .grande{
      width: 60px;
      height: 30px;
  
    }
  
  </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div >
    <p class="h1" style="text-align: center">Ver Orden de Compras</p>
</div>


<form method = "POST" action = "{{route('OrdenCompra.Empleado.Update')}}" id="frmrepo" name="frmrepo"  enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepasEmpleado" id="codigoCedepasEmpleado" value="{{ $empleado->codigoCedepas }}">
    <input type="hidden" name="codOrdenCompra" id="codOrdenCompra" value="{{ $orden->codOrdenCompra }}">
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$empleado->codEmpleado}}">
    
    @csrf
    <div class="container" >
        <div class="row">           
            <div class="col-md"> {{-- COLUMNA IZQUIERDA 1 --}}
                <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                    <div class="row">
                        <div  class="colLabel">
                                <label for="fecha">Fecha de creacion</label>
                        </div>
                        <div class="col">                
                                <div class="input-group date form_date" style="" data-date-format="dd/mm/yyyy" data-provide="datepicker">
                                    <input type="text"  class="form-control" name="fechaHoy" id="fechaHoy" disabled
                                        value="{{$orden->getFechaHoraCreacion()}}" >     
                                </div>
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel">
                                <label for="ComboBoxProyecto" id="lvlProyecto">Proyecto</label>

                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" value="{{$orden->getProyecto()->nombre}}"> 
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel">
                                <label for="" id="">Sede</label>

                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" value="{{$orden->getSede()->nombre}}">  
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel">
                                <label for="fecha">Moneda</label>

                        </div>

                        <div class="col">
                            <input type="text" readonly class="form-control" value="{{$orden->getMoneda()->nombre}}">
                        </div>
                        

                        <div class="w-100"></div>
                        <div  class="colLabel">
                            <label for="fecha">Código Cedepas</label>

                        </div>
                        <div class="col">
                                <input type="text" readonly class="form-control" value="{{$orden->codigoCedepas}}
                                ">    
                        </div>
                        
                      
                        <div class="w-100"></div>
                        <div  class="colLabel">
                            <label for="fecha">Codigo Presupuestal</label>

                        </div>
                        <div class="col">
                                <input type="text" readonly class="form-control" name="partidaPresupuestal" id="partidaPresupuestal" value="{{$orden->partidaPresupuestal}}">    
                        </div>
 

                    </div>


                </div>
                
                
                
                
            </div>


            <div class="col-md"> {{-- COLUMNA DERECHA --}}
                <div class="container">
                    <div class="row">
                        <div  class="colLabel">
                            <label for="" id="">Señores:</label>
                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" name="señores" id="señores" value="{{$orden->señores}}"> 
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}

                        <div  class="colLabel">
                            <label for="" id="">RUC:</label>
                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" name="ruc" id="ruc" value="{{$orden->ruc}}"> 
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}

                        <div  class="colLabel">
                            <label for="" id="">Direccion:</label>
                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" name="direccion" id="direccion" value="{{$orden->direccion}}"> 
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}

                        <div  class="colLabel">
                            <label for="" id="">Atencion:</label>
                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" name="atencion" id="atencion" value="{{$orden->atencion}}"> 
                        </div>

                        <div class="w-100"></div> {{-- SALTO LINEA --}}

                        <div  class="colLabel">
                            <label for="" id="">Referencia:</label>
                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" readonly class="form-control" name="referencia" id="referencia" value="{{$orden->referencia}}"> 
                        </div>
                    </div>
                    <div style="margin-bottom: 1%">
                        <label for="fecha">Observacion:</label>
                        <textarea class="form-control" readonly name="observacion" id="observacion" aria-label="With textarea"
                             cols="3">{{$orden->observacion}}</textarea>
        
                    </div>

                </div>

                
                
            </div>
        </div>
    </div>
    
        {{-- LISTADO DE DETALLES  --}}
    <div class="col-md-12 pt-3">     
        <div class="table-responsive">                           
            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
                <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                    <th width="10%" class="text-center">Cantidad</th>                                        
                    <th width="13%">Unidad Medida</th>                                 
                    <th>Descripcion</th>
                    <th width="12%" class="text-center">Valor Venta</th>
                    <th width="12%" class="text-center">Precio Venta</th>
                    <th width="12%" class="text-center">Total</th>
                    <th width="5%" class="text-center">¿IGV?</th>                                       
                </thead>
                <tbody>
                    @foreach($detalles as $itemdetalle)
                    <tr class="selected" id="fila`+item+`" name="fila` +item+`">          
                        <td style="text-align:center;">               
                            {{$itemdetalle->cantidad}}
                        </td>             
                        <td style="text-align:center;">               
                            {{$itemdetalle->getUnidadMedida()->nombre}}
                        </td> 
                        <td>               
                            {{$itemdetalle->descripcion}}
                        </td> 
                        <td style="text-align:center;">               
                            {{number_format($itemdetalle->valorDeVenta,2)}}
                        </td> 
                        <td style="text-align:center;">               
                            {{number_format($itemdetalle->precioVenta,2)}}
                        </td> 
                        <td  style="text-align:right;">              
                            {{number_format($itemdetalle->subtotal,2)}}
                        </td>   
                        <td style="text-align:center;"> 
                          
                            <input type="checkbox" class="grande" {{$itemdetalle->tieneIGV()}} onclick="return false;">
                                  
                        </td>                                  
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 


        
            

        <div class="row" id="divTotal" name="divTotal">                       
            <div class="col-md-8">
            </div>   
            <div class="col-md-2">                        
                <label for="">Total Gastado: </label>    
            </div>   
            <div class="col-md-2">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">
                <!--
                <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
                <input type="hidden" name="totalRendido" id="totalRendido">                              
                -->
                <input type="text" class="form-control text-right" value="{{number_format($orden->total,2)}}" readonly>   
            </div>   

            <div class="w-100">

            </div>
            <div class="col-md-8"></div>


        </div>
                

            
    </div> 
    
    <div class="row">
        <div class="col">
            @include('OrdenCompra.DesplegableDescargarYEliminarArchivosOrden')

        </div>
        <div class="col">
            
            <a href="{{route('OrdenCompra.descargarPDF',$orden->codOrdenCompra)}}" 
                class='btn btn-info' title="Descargar PDF">
                Descargar PDF
                <i class="fas fa-file-download"></i>
              </a>
              
            <a target="pdf_ordenCompra_{{$orden->codOrdenCompra}}" href="{{route('OrdenCompra.verPDF',$orden->codOrdenCompra)}}" 
                class='btn btn-info'  title="Ver PDF">
                Ver PDF
                <i class="fas fa-file-pdf"></i>
            </a>
        </div>

    </div>
                        
    <div class="row">
        <div class="col">
            <a href="{{route('OrdenCompra.Empleado.Listar')}}" class='btn btn-info  '>
                <i class="fas fa-arrow-left"></i> 
                Regresar al Menú
            </a>   
        </div>
        <div class="col">
            
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

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

@include('Layout.EstilosPegados')

