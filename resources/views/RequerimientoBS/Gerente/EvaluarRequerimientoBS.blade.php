@extends('Layout.Plantilla')

@section('titulo')
    @if($requerimiento->listaParaAprobar())
        Evaluar
    @else   
        Ver
    @endif
        Requerimiento de Bienes y Servicios

@endsection

@section('contenido')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <div >
        <p class="h1" style="text-align: center">
            @if($requerimiento->listaParaAprobar())
                Evaluar Requerimiento de Bienes y Servicios
                <button id="botonActivarEdicion" class="btn btn-success"  onclick="desOactivarEdicion()">
                    Activar Edición
                </button>
        
            @else   
                Ver Requerimiento de Bienes y Servicios
            @endif
            
            
        </p>

    </div>
<form method = "POST" action = "{{route('RequerimientoBS.Gerente.aprobar')}}" onsubmit=""   id="frmRepo"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$requerimiento->codEmpleadoSolicitante}}">
    <input type="hidden" name="codRequerimiento" id="codRequerimiento" value="{{$requerimiento->codRequerimiento}}">

    @include('RequerimientoBS.Plantillas.PlantillaVerRequerimiento')
    
    <div class="row text-center">  

        <div class="col">
            <a href="{{route('RequerimientoBS.Gerente.Listar')}}" class='btn btn-info float-left'>
                <i class="fas fa-arrow-left"></i> 
                Regresar al Menú
            </a>
               

        </div>
        <div class="col">


        </div>
        <div class="col">
            <div class="row">
                
                @if($requerimiento->verificarEstado('Creada') || $requerimiento->verificarEstado('Subsanada') )
                    <div class="col">
                        <a id="botonAprobar" href="#" class="btn btn-success float-right" onclick="aprobar()">
                            <i class="fas fa-check"></i> 
                            Aprobar
                        </a>
                        <button id="botonObservar" type="button" class='btn btn-warning float-right' style="margin-right:6px;"
                            data-toggle="modal" data-target="#ModalObservar">
                            <i class="fas fa-eye-slash"></i>
                            Observar
                        </button> 
                        <a href="#" class="btn btn-danger float-right" style="margin-right:6px;"onclick="clickRechazar()">
                            <i class="fas fa-times"></i> 
                            Rechazar
                        </a>
                    </div>    

                @endif 
            </div>
              
        </div>
        
    </div>
</form>


    <!-- MODAL -->
    <div class="modal fade" id="ModalObservar" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalObservar">Observar Requerimiento de Bienes y Servicios</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formObservar" name="formObservar" action="{{route('RequerimientoBS.observar')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codRequerimientoModal" id="codRequerimientoModal" value="{{$requerimiento->codRequerimiento}}">
                            
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

                        <button id="botonGuardarObservacion" type="button" onclick="clickObservar()" class="btn btn-primary">
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
{{-- *****************************************************************************x******************************** --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>



@include('Layout.EstilosPegados')

@section('script')


<script>
    const justificacion = document.getElementById('justificacion');
    var codPresupProyecto = "{{$requerimiento->getProyecto()->codigoPresupuestal}}";
    
    $(document).ready(function(){
        contadorCaracteres('justificacion','contador','{{App\Configuracion::tamañoMaximoResumen}}');
        contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
    });

    function clickRechazar() {
        confirmarConMensaje('¿Esta seguro de rechazar el requerimiento?','','warning',ejecutarRechazar);
    }
    function ejecutarRechazar() {
        window.location.href="{{route('RequerimientoBS.rechazar',$requerimiento->codRequerimiento)}}";
    }

    
    function clickObservar() {
        texto=$('#observacion').val();
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

        confirmarConMensaje('¿Esta seguro de observar el requerimiento?','','warning',ejecutarObservar);
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
        confirmar('¿Está seguro de Aprobar el requerimiento?','info','frmRepo');
        

    }
    function cambiarEstilo(name, clase){
        document.getElementById(name).className = clase;
    }
    function validarEdicion(){
        cambiarEstilo('justificacion','form-control');
        msj="";
        
        if(justificacion.value==''){
            cambiarEstilo('justificacion','form-control-undefined');
            msj= "Debe ingresar el resumen de la actividad";
        }else if(justificacion.value.length>{{App\Configuracion::tamañoMaximoJustificacion}} ){
            cambiarEstilo('justificacion','form-control-undefined');
            msj='La longitud de la justificación tiene que ser maximo de {{App\Configuracion::tamañoMaximoJustificacion}} caracteres.';
            msj=msj+' El tamaño actual es de '+justificacion.value.length+' caracteres.';
        }
            
        
        
        i=1;
        @foreach ($detalles as $itemDetalle)
            
            inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleRequerimiento}}');
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



    
    
    var edicionActiva = false;
    function desOactivarEdicion(){
        
        console.log('Se activó/desactivó la edición : ' + edicionActiva);
        
        @foreach ($detalles as $itemDetalle)
            inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleRequerimiento}}');
            
            if(edicionActiva){
                inputt.classList.add('inputEditable');
                inputt.setAttribute("readonly","readonly",false);
                justificacion.setAttribute("readonly","readonly",false);
            }else{
                inputt.classList.remove('inputEditable');
                inputt.removeAttribute("readonly"  , false);
                justificacion.removeAttribute("readonly"  , false);
                
            }
        @endforeach
        edicionActiva = !edicionActiva;
        
        
    }
</script>


@endsection
