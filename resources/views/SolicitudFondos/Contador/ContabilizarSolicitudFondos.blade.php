@extends('Layout.Plantilla')
@section('titulo')
@if($solicitud->verificarEstado('Contabilizada'))
Ver
@else
Contabilizar
@endif
Solicitud
@endsection

@section('contenido')

{{-- ESTA VISTA SE USA PARA VER Y PARA CONTABILIZAR --}}
<div >
    
    <p class="h1" style="text-align: center"> 
        @if($solicitud->verificarEstado('Contabilizada'))
        Ver
        @else
        Contabilizar
        @endif
        Solicitud de Fondos
    </p>
</div>

        
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepas" id="codigoCedepas" 
        value="{{ $empleadoLogeado->codigoCedepas }}">

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
                <input type="text" class="form-control text-right" name="total" 
                    value="{{number_format($solicitud->totalSolicitado,2)}}" id="total" readonly>                              
            </div>   
        </div>
                    

        
        <div class="col-md-12 text-center">  
            <div id="guardar">
                <div class="form-group">
                    
                    <br>
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                @include('SolicitudFondos.Plantillas.DesplegableDescargarArchivosSoli')
                                <a href="{{route('SolicitudFondos.Contador.Listar')}}" 
                                    class='btn btn-primary' style="float:left;">
                                    <i class="fas fa-undo"></i>
                                    Regresar al menú
                                </a>

                            </div>
                            <div class="col"></div>
                       
                          
                            
                        @if($solicitud->verificarEstado('Abonada') )
                                
                              @csrf     
                                <input type="hidden" value="{{$solicitud->codSolicitud}}" name="codSolicitud" id="codSolicitud">
                                                              
                                <div class="col">
                                    <a href="#" id="botonContabilizar" onclick="clickOnContabilizar()" 
                                        class='btn btn-success'  style="float:right;">
                                        <i class="fas fa-check"></i>
                                        Marcar como contabilizada
                                    </a>    
                                </div>
                        
                        
                        @endif
                        </div>
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


@include('Layout.EstilosPegados')

<style>
    
    
    </style>


@section('script')
     <script src="/public/select2/bootstrap-select.min.js"></script>     
     <script>
       
        $(document).ready(function(){
            
    
        });

        function clickOnContabilizar(){
            confirmarConMensaje("Confirmar","¿Desea marcar como contabilizada la solicitud?","info",contabilizar);
        }

        function contabilizar(){
            location.href = "{{route('SolicitudFondos.Contador.Contabilizar',$solicitud->codSolicitud)}}";
        }
        /*
        function observar(){

            textoObs = $('#observacion').val();
            codigoSolicitud = {{$solicitud->codSolicitud}};
            console.log('Se presionó el botón observar, el textoobservacion es ' + textoObs + ' y el cod de la solicitud es ' +  codigoSolicitud);
            location.href = '/SolicitudFondos/Observar/'+ codigoSolicitud +'*' +textoObs;

        }
        */
    
    
       
    
    
    
    </script>
     










@endsection
