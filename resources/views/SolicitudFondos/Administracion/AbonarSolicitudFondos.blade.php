@extends('Layout.Plantilla')

@section('titulo')
@if($solicitud->verificarEstado('Aprobada'))
    Abonar Solicitud
@else 
    Ver Solicitud
@endif
@endsection

@section('contenido')

<div>
    <p class="h1" style="text-align: center">
        @if($solicitud->verificarEstado('Aprobada'))
            Abonar a Solicitud de Fondos Aprobada
        @else 
            Ver Solicitud de Fondos
        @endif
    
        
    
    </p>
</div>

<form method = "POST" action = "{{route('SolicitudFondos.Administracion.Abonar')}}" id="frmsoli"  
enctype="multipart/form-data">
    {{-- Para saber en el post cual solicitud es  --}}    
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
                                <a href="{{route('SolicitudFondos.Administracion.Listar',$solicitud->codSolicitud)}}" 
                                    class='btn btn-primary'>
                                    <i class="fas fa-undo"></i>
                                    Regresar al menú
                                </a>

                            </div>
                            <div class="col">
                                <a href="{{route('solicitudFondos.descargarPDF',$solicitud->codSolicitud)}}" class='btn btn-info'  title="Descargar PDF">
                                    Descargar PDF <i class="fas fa-file-download">
                                    </i>
                                </a>
                                <a target="pdf_solicitud_{{$solicitud->codSolicitud}}" href="{{route('solicitudFondos.verPDF',$solicitud->codSolicitud)}}" class='btn btn-info'  title="Ver PDF">
                                    Ver PDF <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                            
                            
                            <div class="col">
                                @if($solicitud->verificarEstado('Aprobada'))
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" class='btn btn-success float-right' id="botonAbonar" onclick="marcarComoAbonada()" style="margin-left: 6px">
                                                <i class="fas fa-check"></i>
                                                Marcar como Abonada
                                            </button>
                                            <button type="button" class='btn btn-warning float-right' style="margin-left: 6px"
                                                data-toggle="modal" data-target="#ModalObservar">
                                                <i class="fas fa-eye-slash"></i>
                                                Observar
                                            </button> 
                                            <a href="{{route('solicitudFondos.rechazar',$solicitud->codSolicitud)}}" 
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


<style>
    
    .hovered:hover{
    background-color:rgb(97, 170, 170);
}

    </style>

@include('Layout.EstilosPegados')
@section('script')

    

     <script src="/public/select2/bootstrap-select.min.js"></script>     
     <script>
        var cont=0;
        
      
        var total=0;
  
        var importes=[];
        var controlproducto=[];
        var totalSinIGV=0;
    
        $(document).ready(function(){
            contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
        });
        



        function marcarComoAbonada(){
            

            confirmar('¿Está seguro de marcar como abonada la solicitud?','info','frmsoli');//[success,error,warning,info]
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

        /* function validar(){
            if($('#nombreImgImagenEnvio').val() == '')
                {
                    alerta('Debe subir el comprobante del deposito.')
                    return false;
                }

        } */

        


    
    
    

    
    
    
    </script>
     










@endsection
