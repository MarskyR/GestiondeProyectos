@section('estilos')
<style>
     
    .rowParaCentrarVert{
        display: flex;
        align-items: center;
    }

    
</style>

@endsection

<div class="container">
    <div class="row">           
        <div class="col-md" > {{-- COLUMNA IZQUIERDA 1 --}}
            <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                <div class="row rowParaCentrarVert">
                    <div  class="colLabel">
                            <label for="fecha">Fecha emisión</label>
                    </div>
                    <div class="col">
                                         
                        <input type="text"  class="form-control" name="fecha" id="fecha" disabled
                            value="{{$solicitud->fechaHoraEmision}}" >     
                    
                       

                    </div>

                    @if(App\Configuracion::activarVerHistorialDeDocumentos)
                        
                        <div class="col">
                            <button type="button" class="btn btn-primary btn-sm fontSize8" style=""  
                                data-toggle="modal" data-target="#ModalHistorial">
                                Ver Historial  
                            </button>
                        </div>
                    @endif

                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="fecha">Girar a la orden de</label>

                    </div>
                    <div class="col">
                            <input readonly type="text" class="form-control" name="girarAOrden" id="girarAOrden" value="{{$solicitud->girarAOrdenDe}}">    

                    </div>
                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="fecha">N° Cuenta</label>

                    </div>
                    <div class="col">
                            <input readonly  type="text" class="form-control" name="nroCuenta" 
                                id="nroCuenta" value="{{$solicitud->numeroCuentaBanco}}">    
                    </div>
                    
                    <div  class="colLabel" style="width: 10%">
                            <label for="fecha">Banco:</label>

                    </div>
                    <div class="col"> {{-- Combo box de banco --}}
                            <input type="text" class="form-control" name="banco" id="banco" readonly value="{{$solicitud->getNombreBanco()}}">     
                                    
                    </div>
                    
                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="codSolicitud">Código Solicitud</label>

                    </div>
                    <div class="col"> {{-- Combo box de empleado --}}
                            <input readonly  type="text" class="form-control" name="" id="" readonly value="{{$solicitud->codigoCedepas}}">     
                    </div>

                    <div  class="colLabel" style="width: 10%">
                        <label for="moneda">Moneda</label>
                    </div>
                    <div class="col"> {{-- Combo box de moneda --}}
                        <input readonly  type="text" class="form-control" name="moneda" id="moneda" 
                            readonly value="{{$solicitud->getMoneda()->nombre}}">     
                                
                    </div>


                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="ComboBoxProyecto">Proyecto</label>

                    </div>
                    <div class="col"> {{-- Combo box de proyecto --}}
                            <input readonly  type="text" class="form-control" name="proyecto" id="proyecto" readonly 
                            value="{{"[".$solicitud->getProyecto()->codigoPresupuestal."] ".$solicitud->getNombreProyecto()}}">     
                        
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md"> {{-- COLUMNA DERECHA --}}
            <div style="margin-bottom: 1%">
                
                <label for="fecha">Justificación <b id="contador" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                <textarea readonly  class="form-control" name="justificacion" id="justificacion"
                aria-label="With textarea" >{{$solicitud->justificacion}}</textarea>
    

            </div>
            
            <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                <div class="row">
                    
                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="estado">Estado de la Solicitud 
                                @if($solicitud->verificarEstado('Observada'))
                                {{-- Si está observada --}}
                                & Observación 
                                @endif:
                            </label>
                    </div>
                    <div class="col"> {{-- Combo box de estado --}}
                        <textarea readonly type="text" class="form-control" name="estado" id="estado"
                        style="background-color: {{$solicitud->getColorEstado()}} ;
                            color:{{$solicitud->getColorLetrasEstado()}};"
                        readonly
                        >{{$solicitud->getNombreEstado()}}{{$solicitud->getObservacionONull()}}</textarea>   
                    </div>


                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="evaluador">Aprobado por:</label>
                    </div>
                    <div class="col"> {{-- Combo box de  --}}
                        <input readonly  type="text" class="form-control" name="evaluador" id="evaluador" readonly value="{{$solicitud->getNombreEvaluador()}}">     
                                
                    </div>




                </div>
            </div>
        </div>
    </div>
</div>


{{-- LISTADO DE DETALLES  --}}

<div class="table-responsive">                           
    <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
        
        
        
        <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
            <th width="10%" class="text-center">Ítem</th>                                        
            <th width="40%">Concepto</th>                                 
            <th width="10%"> Importe</th>
            <th width="15%" class="text-center">Código Presupuestal</th>
        </thead>
        
        <tbody>
            @foreach($detallesSolicitud as $itemDetalle)
                <tr class="selected" id="fila{{$itemDetalle->nroItem}}" name="fila{{$itemDetalle->nroItem}}">
                    <td style="text-align:center;">               

                       <input type="text" class="inputLigero form-control" name="colItem{{$itemDetalle->nroItem}}" 
                            id="colItem{{$itemDetalle->nroItem}}" value="{{$itemDetalle->nroItem}}" readonly>   
                    </td>               
                    
                    <td> 
                       <input type="text" class="inputLigero form-control" name="colConcepto{{$itemDetalle->nroItem}}" 
                            id="colConcepto{{$itemDetalle->nroItem}}" value="{{$itemDetalle->concepto}}" readonly> 
                    </td>           
                    
                    
                    <td>               
                       <input type="text" class="inputLigero form-control" name="colImporte{{$itemDetalle->nroItem}}" 
                       id="colImporte{{$itemDetalle->nroItem}}"  value="{{number_format($itemDetalle->importe,2)}}" 
                        style="text-align: right;" readonly> 
                    </td>          

                    <td style="text-align:center;">               
                    <input type="text" class="inputLigero form-control" name="CodigoPresupuestal{{$itemDetalle->codDetalleSolicitud}}" 
                        id="CodigoPresupuestal{{$itemDetalle->codDetalleSolicitud}}" value="{{$itemDetalle->codigoPresupuestal}}" 
                        readonly>
                    </td>               
                    
                </tr>                
            
            @endforeach    



        </tbody>
    </table>
</div> 

@php
    $listaOperaciones =  $solicitud->getListaOperaciones();
@endphp

@include('Operaciones.ModalHistorialOperaciones')



@include('Layout.EstilosPegados')
