@extends('Layout.Plantilla')
@section('titulo')
@if($solicitud->verificarEstado('Creada'))
    Revisar Solicitud
@else
    Ver Solicitud
@endif 
@endsection
{{-- ESTA VISTA SIRVE TANTO COMO PARA REVISAR (aprobar rechazar observar)  COMO PARA VERLA NOMAS LUEGO--}}
@section('contenido')
    <div>
        <p class="h1" style="text-align: center">
            @if($solicitud->verificarEstado('Creada'))
                Revisar Solicitud de Fondos
            
                <br>
                <button class="btn btn-success"  onclick="desOactivarEdicion()" id="botonActivarEdicion">
                    Activar Edición
                </button>
          
            

            @else
                Ver Solicitud de Fondos
            @endif 
                

        </p>
        
    </div>


<form method = "POST" action = "{{route('SolicitudFondos.Gerente.Aprobar')}}"  
     id="frmSoli" >    
    {{-- CODIGO DEL EMPLEADO --}}
        <input type="hidden" name="codigoCedepas" id="codigoCedepas" 
            value="{{ $empleadoLogeado->codigoCedepas }}">
        <input type="hidden" value="{{$solicitud->codSolicitud}}" name="codSolicitud" id="codSolicitud">
                                
        @csrf
        @include('SolicitudFondos.Plantillas.VerSOF')
                
        <div class="row" id="divTotal" name="divTotal">                       
            <div class="col-md-8">
            </div>   
            <div class="col-md-2">                        
                <label for="">Total : </label>    
            </div>   
            <div class="col-md-2">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">                              
                <input type="text" class="form-control text-right" name="total" id="total" value="{{number_format($solicitud->totalSolicitado,2)}}" readonly>                              
            </div>   
        </div>
                    

    
            <br>
        <div class="col-md-12">  
            <div id="guardar">
                <div class="form-group">
                    
                    
                    <div class="">
                        <div class="row">
                            <div class="col">
                                @include('SolicitudFondos.Plantillas.DesplegableDescargarArchivosSoli')
                                <a href="{{route('SolicitudFondos.Gerente.Listar')}}" class='btn btn-info'>
                                    <i class="fas fa-arrow-left"></i> 
                                    Regresar al Menú
                                </a>
                            </div>
                            <div class="col">

                            </div>
                            <div class="col">
                                @if($solicitud->verificarEstado('Creada') || $solicitud->verificarEstado('Subsanada') )
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" id="botonAprobar" onclick="aprobar()" class='btn btn-success float-right' style="margin-left: 6px">
                                                <i class="fas fa-check"></i>
                                                Aprobar
                                            </button>   
                                            <button type="button" class='btn btn-warning float-right' style="margin-left: 6px"
                                                data-toggle="modal" data-target="#ModalObservar">
                                                <i class="fas fa-eye-slash"></i>
                                                Observar
                                            </button> 
                                            <a href="#" onclick="clickRechazar()" 
                                                class='btn btn-danger float-right'>
                                                <i class='fas fa-ban'></i>
                                                Rechazar
                                            </a>   
                                        </div>
                                    </div>
                                @endif
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
                        <h5 class="modal-title" id="TituloModalObservar">Observar Solicitud de Fondos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formObservar" name="formObservar" action="{{route('solicitudFondos.observar')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codSolicitudModal" id="codSolicitudModal" value="{{$solicitud->codSolicitud}}">
                            
                            <div class="row">
                                <div class="col-5">
                                    
                                    <label>Observacion <b id="contador2" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                                </div>
                                <div class="w-100"></div> {{-- SALTO LINEA --}}
                                <div class="col">
                                    <textarea class="form-control" name="observacion" id="observacion" cols="30" rows="4" placeholder='Ingrese observación aquí...'></textarea> 
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>

                        <button type="button" onclick="clickObservar()" class="btn btn-primary">
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



@section('script')
        
     <script>
        var cont=0;
        
        var total=0;
        var detalleSol=[];
        const textJustificacion = document.getElementById('justificacion');
        var codPresupProyecto = "{{$solicitud->getProyecto()->codigoPresupuestal}}";


        $(document).ready(function(){
            contadorCaracteres('justificacion','contador','{{App\Configuracion::tamañoMaximoJustificacion}}');
            contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
        });
        /*
        function observar(){


            textoObs = $('#observacion').val();
            if(textoObs==""){
                alerta('No ha ingresado ninguna observación.');
                return; 
            }


            swal(
                {//sweetalert
                    title: 'Confirmación',
                    text: '¿Desea observar la Solicitud?',     //mas texto
                    type: 'info',//e=[success,error,warning,info]
                    showCancelButton: true,//para que se muestre el boton de cancelar
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText:  'SÍ',
                    cancelButtonText:  'NO',
                    closeOnConfirm:     true,//para mostrar el boton de confirmar
                    html : true
                },
                function(value){//se ejecuta cuando damos a aceptar
                    if(value){

                        codigoSolicitud = {{$solicitud->codSolicitud}};
                        console.log('Se presionó el botón observar, el texto observación es ' + textoObs + ' y el cod de la solicitud es ' +  codigoSolicitud);
            
                        location.href = '/SolicitudFondos/Observar/'+ codigoSolicitud +'*' +textoObs;
                    }
                }
            );



        }
        */
        function clickRechazar() {
            confirmarConMensaje('¿Esta seguro de rechazar la solicitud?','','warning',ejecutarRechazar);
        }
        function ejecutarRechazar() {
            window.location.href="{{route('solicitudFondos.rechazar',$solicitud->codSolicitud)}}";
        }

        
        function clickObservar() {
            texto = $('#observacion').val();
            if(texto==''){
                alerta('Ingrese observacion');
                return false;
            }

            tamañoActualObs = texto.length;
            tamañoMaximoObservacion =  {{App\Configuracion::tamañoMaximoObservacion}};
            if( tamañoActualObs  > tamañoMaximoObservacion){
                alerta('La observación puede tener máximo hasta ' +    tamañoMaximoObservacion + 
                    " caracteres. (El tamaño actual es " + tamañoActualObs + ")");
                return false;
            }

            confirmarConMensaje('¿Esta seguro de observar la solicitud?','','warning',ejecutarObservar);
            
        }
        function ejecutarObservar() {
            document.formObservar.submit();
        }


        function aprobar(){
            msje = validarEdicion();
            if(msje!="")
            {
                alerta(msje);
                return false;
            }

            console.log('TODO OK');
            confirmar('¿Está seguro de Aprobar la Solicitud?','info','frmSoli');
        }



        var edicionActiva = false;
        function desOactivarEdicion(){
            console.log('Se activó/desactivó la edición : ' + edicionActiva);

            @foreach ($detallesSolicitud as $itemDetalle)
                inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleSolicitud}}');
                console.log(inputt);
                if(edicionActiva){
                    inputt.classList.add('inputEditable');
                    inputt.setAttribute("readonly","readonly",false);
                    textJustificacion.setAttribute("readonly","readonly",false);
                }else{
                    inputt.classList.remove('inputEditable');
                    inputt.removeAttribute("readonly"  , false);
                    textJustificacion.removeAttribute("readonly"  , false);
                }
            @endforeach
            edicionActiva = !edicionActiva;
            
            
        }
    
        function cambiarEstilo(name, clase){
            document.getElementById(name).className = clase;
        }
  
        function validarEdicion(){
            cambiarEstilo('justificacion','form-control');
            msj="";
            
            if(textJustificacion.value==''){
                cambiarEstilo('justificacion','form-control-undefined');
                msj= "Debe ingresar la justificacion.";
            }else if(textJustificacion.value.length>{{App\Configuracion::tamañoMaximoJustificacion}} ){
                cambiarEstilo('justificacion','form-control-undefined');
                msj='La longitud de la justificación tiene que ser maximo de {{App\Configuracion::tamañoMaximoJustificacion}} caracteres.';
                msj=msj+' El tamaño actual es de '+textJustificacion.value.length+' caracteres.';
            }
            
            
            i=1;
            @foreach ($detallesSolicitud as $itemDetalle)
                
                inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleSolicitud}}');
                if(!inputt.value.startsWith(codPresupProyecto) )
                    msj="El codigo presupuestal del item " + i + " no coincide con el del proyecto ["+ codPresupProyecto +"] .";

                if(inputt.value.length>{{App\Configuracion::tamañoMaximoCodigoPresupuestal}} ){
                    msj='La longitud del Codigo Presupuestal del item ' + i + ' tiene que ser maximo de {{App\Configuracion::tamañoMaximoCodigoPresupuestal}} caracteres.';
                    msj=msj+' El tamaño actual es de '+inputt.value.length+' caracteres.';
                }
                i++;
            @endforeach


            return msj;
        }
    
    
    
    
    
    
    </script>
     










@endsection
