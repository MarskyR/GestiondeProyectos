@extends('Layout.Plantilla')

@section('titulo')
    @if($rendicion->verificarEstado('Creada') || $rendicion->verificarEstado('Subsanada') )
        Revisar Rendición
    @else 
        Ver Rendición
    @endif
@endsection

@section('contenido')
<div >
    {{-- ESTE ARCHIVO SIRVE TANTO COMO VER Y COMO REVISAR(aprobar/observar) --}}
    
    <p class="h1" style="text-align: center">
        @if($rendicion->verificarEstado('Creada') || $rendicion->verificarEstado('Subsanada') )

            Revisar Rendición de  Gastos
            <br>
            <button id="botonActivarEdicion" class="btn btn-success"  onclick="desOactivarEdicion()">
                Activar Edición
            </button>
          
        @else 
            Ver Rendición de Gastos
        @endif
    </p>
</div>

<form method = "POST" action = "{{route('RendicionGastos.Gerente.Aprobar')}}"  
enctype="multipart/form-data" id="frmRend" >
    
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepas" id="codigoCedepas" value="{{ $empleado->codigoCedepas }}">
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codigoSolicitud" id="codigoSolicitud" value="{{ $solicitud->codSolicitud }}">
    <input type="hidden" name="codRendicionGastos" id="codRendicionGastos" value="{{ $rendicion->codRendicionGastos }}">
    

    @csrf
    @include('RendicionGastos.PlantillaVerRG')
      
    
        {{-- LISTADO DE DETALLES  --}}
        <div class="col-md-12 pt-3">     
            <div class="table-responsive">                           
                <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
                    
                    
                    
                    <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                        <th width="13%" class="text-center">Fecha</th>                                        
                        <th width="13%">Tipo CDP</th>
                                                   
                        <th width="10%"> N° Cbte</th>
                        <th width="20%" class="text-center">Concepto </th>
                        <th width="10%" class="text-center">Importe </th>
                        <th width="10%" class="text-center">
                            
                            
                            Cod Presup
                        </th>
                        
                        
                    </thead>
                    <tfoot>

                                                                                        
                    </tfoot>
                    <tbody>
                        @foreach($detallesRend as $itemDetalle)
                            <tr class="selected" id="filaItem" name="filaItem">                
                                <td style="text-align:center;">     
                                    {{$itemDetalle->fecha  }}
                                    
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
                                    {{$rendicion->getMoneda()->simbolo}}   {{  number_format($itemDetalle->importe,2)  }}
                                </td>               
                                <td style="text-align:center;">             
                                    
                                    <input type="text"  id="CodigoPresupuestal{{$itemDetalle->codDetalleRendicion}}" 
                                                        name="CodigoPresupuestal{{$itemDetalle->codDetalleRendicion}}" 
                                        value="{{$itemDetalle->codigoPresupuestal}}" readonly class="inputEditable form-control">
                                    
                                </td>               
                                            
                            </tr> 
                        @endforeach
                                       







                    </tbody>
                </table>
            </div> 
                
                      
          

            <div class="row" id="divTotal" name="divTotal">                       
                <div class="col">
                    @include('RendicionGastos.DesplegableDescargarArchivosRend')
                    

                    <a href="{{route('RendicionGastos.Gerente.Listar')}}" class='btn btn-info float-left'>
                        <i class="fas fa-arrow-left"></i>
                        Regresar al Menu
                    </a> 


                </div>   

                <div class="col" >
                    <div class="row">
                        
                        <div class="col">                        
                            <label for="">Total Rendido/Gastado: </label>    
                        </div>   
                        <div class="col">
                            {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                            <input type="hidden" name="cantElementos" id="cantElementos">                              
                            <input type="text" class="form-control text-right" name="totalRendido" id="totalRendido" readonly 
                                value="{{number_format(($rendicion->totalImporteRendido),2)}}">                              
                        </div>   



                        <div class="w-100"></div>
                        <div class="col">                        
                            <label for="">Total Recibido: </label>    
                        </div>   

                        <div class="col">
                        
                            <input type="text" class="form-control text-right" name="totalRecibido" id="totalRecibido" readonly 
                                value="{{number_format($rendicion->totalImporteRecibido,2)}}">                              
                        </div>   
                        <div class="w-100"></div>
                        <div class="col">                        
                            <label for="">

                                @if($rendicion->saldoAFavorDeEmpleado>0) {{-- pal empl --}}
                                    Saldo a favor del Empl: 
                                @else
                                    Saldo a favor de Cedepas: 
                                @endif
                                
                            </label>    
                        </div>   
                        <div class="col">
                            <input type="text" class="form-control text-right" name="totalSaldo" id="totalSaldo" 
                            readonly value="{{number_format(abs($rendicion->saldoAFavorDeEmpleado),2)}}">                              
                        </div>   
                        
                        
                        <div class="w-100"></div>
                
                        <div class="col"> 
                            <div class="row">
                                <div class="col">
                                    @if($rendicion->listaParaAprobar())
                                        <a id="botonAprobar" href="#" class="btn btn-success float-right" onclick="aprobar()" style="margin-left: 6px">
                                            <i class="fas fa-check"></i> 
                                            Aprobar
                                        </a>    
                                    @endif
                                    
                                    @if($rendicion->verificarEstado('Creada') || $rendicion->verificarEstado('Subsanada') ) 
                                        <button  id="botonObservar" type="button" class='btn btn-warning float-right'
                                            data-toggle="modal" data-target="#ModalObservar">
                                            <i class="fas fa-eye-slash"></i>
                                            Observar
                                        </button> 
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                </div>



            </div>
                    
                
                
        </div>
        
        


   

</form>


    <!-- MODAL -->
    <div class="modal fade" id="ModalObservar" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalObservar">Observar Rendición de Gastos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formObservar" name="formObservar" action="{{route('RendicionGastos.Observar')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codRendicionGastosModal" id="codRendicionGastosModal" value="{{$rendicion->codRendicionGastos}}">
                            
                            <div class="row">
                                <div class="col-5">
                                    
                                    <label>Observacion <b id="contador2" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                                </div>
                                <div class="w-100"></div> {{-- SALTO LINEA --}}
                                <div class="col">
                                    <textarea class="form-control" name="observacion" id="observacion" cols="30" rows="4"  placeholder='Ingrese observación aquí...'></textarea> 
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>

                        <button id="guardarObservacion" type="button" onclick="observarRendicion()" class="btn btn-primary">
                           Guardar <i class="fas fa-save"></i>
                        </button>
                    </div>
            </div>
        </div>
    </div>
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
    
    .hovered:hover{
        background-color:rgb(97, 170, 170);
    }
    input[type='checkbox'] {
        /* -webkit-appearance:none; */
        width:10px;
        height:10px;
        background:white;
        border-radius:15px;
        border:2px solid #555;
      
    }
    input[type='checkbox']:checked {
        background: #abd;
    }
    
    .inputEditable{
        background-color: rgb(196, 196, 196);
    }


</style>


@section('script')
       
     <script>
        var cont=0;
        
        var total=0;
        var detalleRend=[];
        const textResum = document.getElementById('resumen');
        var codPresupProyecto = "{{$solicitud->getProyecto()->codigoPresupuestal}}";

        $(document).ready(function(){
            textResum.classList.add('inputEditable');
            contadorCaracteres('resumen','contador','{{App\Configuracion::tamañoMaximoResumen}}');
            contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
        });
    
        
        function cambiarEstilo(name, clase){
            document.getElementById(name).className = clase;
        }
         
        function validarEdicion(){
            cambiarEstilo('resumen','form-control');
            msj="";
            
            if(textResum.value=='' ){
                cambiarEstilo('resumen','form-control-undefined');
                msj='Debe ingresar la resumen';
            }else if(textResum.value.length>{{App\Configuracion::tamañoMaximoResumen}} ){
                cambiarEstilo('resumen','form-control-undefined');
                msj='La longitud de la resumen tiene que ser maximo de {{App\Configuracion::tamañoMaximoResumen}} caracteres.';
                msj=msj+' El tamaño actual es de '+textResum.value.length+' caracteres.';
            }
            
            i=1;
            @foreach ($detallesRend as $itemDetalle)
                
                inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleRendicion}}');
                if(!inputt.value.startsWith(codPresupProyecto) )
                    msj= "El codigo presupuestal del item " + i + " no coincide con el del proyecto ["+ codPresupProyecto +"] .";

                if(inputt.value.length>{{App\Configuracion::tamañoMaximoCodigoPresupuestal}} ){
                    msj='La longitud del Codigo Presupuestal del item ' + i + ' tiene que ser maximo de {{App\Configuracion::tamañoMaximoCodigoPresupuestal}} caracteres.';
                    msj=msj+' El tamaño actual es de '+inputt.value.length+' caracteres.';
                }
                i++;
            @endforeach


            return msj;
        }

        function aprobar(){
            msje = validarEdicion();
            if(msje!="")
                {
                    alerta(msje);
                    return false;
                }
            console.log('TODO OK');
            confirmar('¿Está seguro de Aprobar la Rendición?','info','frmRend');
            

        }





        var edicionActiva = false;
        function desOactivarEdicion(){
            
            console.log('Se activó/desactivó la edición : ' + edicionActiva);
            
            

            @foreach ($detallesRend as $itemDetalle)
                inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleRendicion}}');
                
                if(edicionActiva){
                    inputt.classList.add('inputEditable');
                    inputt.setAttribute("readonly","readonly",false);
                    textResum.setAttribute("readonly","readonly",false);
                }else{
                    inputt.classList.remove('inputEditable');
                    inputt.removeAttribute("readonly"  , false);
                    textResum.removeAttribute("readonly"  , false);
                    
                }
            @endforeach
            edicionActiva = !edicionActiva;
            
            
        }

        /*
        function observarRendicion(){
            textoObs = $('#observacion').val();
            codigoSolicitud = {{$rendicion->codRendicionGastos}};
            console.log('Se presionó el botón observar, el texto observación es ' + textoObs + ' y el cod de la rendición es ' +  codigoSolicitud);
            if(textoObs==''){
                alerta('Debe ingresar la observación');
            }else{
                swal({//sweetalert
                    title:'¿Seguro de observar la rendición?',
                    text: '',
                    type: 'info',  
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText:  'SÍ',
                    cancelButtonText:  'NO',
                    closeOnConfirm:     true,//para mostrar el boton de confirmar
                    html : true
                },
                function(){
                    location.href = '/rendiciones/observar/'+ codigoSolicitud +'*' +textoObs;
                });
            }
        }
        */
        function observarRendicion() {
            textoObs = $('#observacion').val();
            codigoSolicitud = {{$rendicion->codRendicionGastos}};
            console.log('Se presionó el botón observar, el texto observación es ' + textoObs + ' y el cod de la rendición es ' +  codigoSolicitud);
            if(textoObs==''){
                alerta('Debe ingresar la observación');
                return false;
            }
            
            tamañoActualObs = textoObs.length;
            tamañoMaximoObservacion =  {{App\Configuracion::tamañoMaximoObservacion}};
            if( tamañoActualObs  > tamañoMaximoObservacion){
                alerta('La observación puede tener máximo hasta ' +    tamañoMaximoObservacion + 
                    " caracteres. (El tamaño actual es " + tamañoActualObs + ")");
                return false;
            }

            confirmarConMensaje('¿Esta seguro de observar la rendición?','','warning',ejecutarObservar);
        }
        function ejecutarObservar() {
            document.formObservar.submit();
        }


    
 
    
    </script>
     










@endsection
