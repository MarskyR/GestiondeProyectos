@extends('Layout.Plantilla')

@section('titulo')
Contabilizar Rendición
@endsection

@section('contenido')
<div >
    <p class="h1" style="text-align: center">Contabilizar Rendición de  Gastos</p>
</div>


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
                        <th width="10%" class="text-center">Cod Presup </th>
                        <th width="10%" class="text-center">Contabilizado</th>
                        
                        
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
                                    {{$rendicion->getMoneda()->simbolo}} {{  number_format($itemDetalle->importe,2)  }}
                                </td>               
                                <td style="text-align:center;">               
                                    {{$itemDetalle->codigoPresupuestal  }}
                                </td>               
                                <td style="text-align:center;">               
                                    <input id="checkBoxContabilizar{{$itemDetalle->codDetalleRendicion}}" type="checkbox"  readonly
                                    @if($rendicion->verificarEstado('Contabilizada')) {{-- Ya está contabilizada --}}
                                        @if($itemDetalle->contabilizado=='1')
                                            checked
                                        @endif    
                                        onclick="return false;"
                                    @else {{-- Caso normal cuando se está contabilizando --}}
                                        onclick="contabilizarItem({{$itemDetalle->codDetalleRendicion}})"
                                    @endif
                                    
                                    >
                                </td>               
                                            
                            </tr> 
                        @endforeach
                                       







                    </tbody>
                </table>
            </div> 
                
                <input type="{{App\Configuracion::getInputTextOHidden()}}" id="listaContabilizados" 
                    name = "listaContabilizados" value="">

                <div class="row" id="divTotal" name="divTotal">       
                    <div class="col"> 
                    
                        @include('RendicionGastos.DesplegableDescargarArchivosRend')
                        
                        <a href="{{route('RendicionGastos.ListarRendiciones')}}" 
                            class='btn btn-primary' style="float:left;">
                            <i class="fas fa-undo"></i>
                            Regresar al menú
                        </a>    
                               
                    
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
             
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        @if($rendicion->verificarEstado('Aprobada') )    
                                            <input type="hidden" value="{{$solicitud->codSolicitud}}" name="codSolicitud" id="codSolicitud">
                                            <button id="botonContabilizar" type="button" onclick="guardarContabilizar()"
                                                class='btn btn-success float-right'  style="margin-left: 6px">
                                                <i class="fas fa-check"></i>
                                                Guardar como Contabilizado
                                            </button>    
                                            <button id="botonObservar" type="button" class='btn btn-warning float-right'
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
        
        <div class="col-md-12 text-center">  
            <div id="guardar">
                <div class="form-group">
                   
                </div>    
            </div>
        </div>
    
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

@section('script')
       
     <script>
       
       
        $(document).ready(function(){
            contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
    
        });
    
        @if (App\Configuracion::enProduccion)
            document.getElementById('listaContabilizados').type = "hidden";
        @endif

        var listaItems = [];

        function contabilizarItem(item){
            
            if( listaItems.indexOf(item)==-1  )
            { //no lo tiene 
                listaItems.push(item);
            }
            else 
            { //ya lo tiene, lo quitamos
                let pos = listaItems.indexOf(item);
                listaItems.splice(pos,1);
                
            }
           
            $('#listaContabilizados').val(listaItems);

        }

        /*
        function guardarContabilizar (){
            codRendicion = {{$rendicion->codRendicionGastos}};

            msjExtra = "";
            if(listaItems.length==0){
                msjExtra = "No ha marcado ningún Ítem... ";
                
            }


            swal({//sweetalert
                title: msjExtra+'¿Seguro de contabilizar la rendición?',
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
                location.href = '/rendicion/contabilizar/'+ codRendicion +'*' +listaItems;
            });
        }
        */
        function guardarContabilizar() {
            msjExtra = "";
            if(listaItems.length==0){
                alerta('No ha marcado ningún Item... ');
                msjExtra = "No ha marcado ningún Item... ";
                return false;
            }
            confirmarConMensaje('¿Seguro de contabilizar la rendicion?','','warning',ejecutarContabilizar);
        }
        function ejecutarContabilizar() {
            codRendicion = {{$rendicion->codRendicionGastos}};
            location.href = '/rendicion/contabilizar/'+ codRendicion +'*' +listaItems;
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
