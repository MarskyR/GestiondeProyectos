@extends('Layout.Plantilla')

@section('titulo')
    @if($reposicion->verificarEstado('Creada') || 
        $reposicion->verificarEstado('Subsanada') )
        {{-- Estados en los que es valido Evaluar --}}
        Revisar Reposición de Gastos

    @else 
        Ver Reposición de Gastos

    @endif
@endsection

@section('contenido')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div class="row">
    <div class="col-md-10">
        <p class="h1" style="text-align:center">
            @if($reposicion->verificarEstado('Creada') || 
                $reposicion->verificarEstado('Subsanada') )
                {{-- Estados en los que es valido Evaluar --}}
                Revisar Reposición de Gastos
                <br>
                <button id="botonActivarEdicion"  class="btn btn-success"  onclick="desOactivarEdicion()">
                    Activar Edición
                </button>
        
            @else 
                Ver Reposición de Gastos

            @endif
               
            
                
        </p>
    </div>
    <div class="col-md-2">
        <br>
        <a  href="{{route('ReposicionGastos.exportarPDF',$reposicion->codReposicionGastos)}}" 
            class="btn btn-warning btn-sm btn-right" style="margin-left:60px;">
            <i class="entypo-pencil"></i>
            PDF
          </a>
        <a target="blank" href="{{route('ReposicionGastos.verPDF',$reposicion->codReposicionGastos)}}" 
            class="btn btn-warning btn-sm btn-right">
            <i class="entypo-pencil"></i>
            verPDF
        </a>
    </div>
</div>


<form method = "POST" action = "{{route('ReposicionGastos.Gerente.aprobar')}}" onsubmit="return validarTextos()"   id="frmRepo"
    enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$reposicion->codEmpleadoSolicitante}}">
    <input type="hidden" name="codReposicionGastos" id="codReposicionGastos" value="{{$reposicion->codReposicionGastos}}">
    
    @csrf
   
        @include('ReposicionGastos.PlantillaVerREP')
      
      
         


        {{-- LISTADO DE DETALLES  --}}
      
        <div class="row" id="divTotal" name="divTotal">      
            <div class="col"> 
                @include('ReposicionGastos.DesplegableDescargarArchivosRepo')
            </div>
            <!--
            <div class="col"></div>
            -->
            <div class="col-md-2">                        
                <label for="">Total Gastado: </label>    
            </div>   
            <div class="col-md-2">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">
                <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
                <input type="hidden" name="totalRendido" id="totalRendido">

                <input type="text" class="form-control text-right" name="total"
                        id="total" readonly value="{{number_format($reposicion->totalImporte,2)}}">   

            </div>   

        </div>
       
        <div class="col-md-12 text-center">  
            <div id="guardar">
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <a href="{{route('ReposicionGastos.Gerente.Listar')}}" class='btn btn-info float-left'>
                                <i class="fas fa-arrow-left"></i> 
                                Regresar al Menú
                            </a>
                        </div>
                        <div class="col">
                            @if($reposicion->verificarEstado('Creada') || $reposicion->verificarEstado('Subsanada') )
                            <div class="row">
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
                                    <a href="#" class="btn btn-danger float-right" style="margin-right:6px;" onclick="clickRechazar()">
                                        <i class="fas fa-times"></i> 
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

    

</form>

    <!-- MODAL -->
    <div class="modal fade" id="ModalObservar" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalObservar">Observar Reposición de Gastos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formObservar" name="formObservar" action="{{route('ReposicionGastos.observar')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codReposicionGastosModal" id="codReposicionGastosModal" value="{{$reposicion->codReposicionGastos}}">
                            
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
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

<style>


.hovered:hover{
background-color:rgb(97, 170, 170);
}



</style>

@section('script')



       {{-- PARA EL FILE  --}}
    <script type="application/javascript">
    //se ejecuta cada vez que escogewmos un file

        const textResum = document.getElementById('resumen');
        var codPresupProyecto = "{{$reposicion->getProyecto()->codigoPresupuestal}}";

        $(document).ready(function(){
            textResum.classList.add('inputEditable');
            contadorCaracteres('resumen','contador','{{App\Configuracion::tamañoMaximoResumen}}');
            contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
        });
       
        function clickRechazar() {
            confirmarConMensaje('¿Esta seguro de rechazar la reposicion?','','warning',ejecutarRechazar);
        }
        function ejecutarRechazar() {
            window.location.href="{{route('ReposicionGastos.rechazar',$reposicion->codReposicionGastos)}}"; 
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
            

            confirmarConMensaje('¿Esta seguro de observar la reposicion?','','warning',ejecutarObservar);
        }
        function ejecutarObservar() {
            document.formObservar.submit();
        }

        

 
     

        function cambiarEstilo(name, clase){
            document.getElementById(name).className = clase;
        }
        

        function validarEdicion(){
            cambiarEstilo('resumen','form-control');
            msj="";
            
            if(textResum.value==''){
                cambiarEstilo('resumen','form-control-undefined');
                msj= "Debe ingresar el resumen de la actividad";
            }else if(textResum.value.length>{{App\Configuracion::tamañoMaximoResumen}} ){
                cambiarEstilo('resumen','form-control-undefined');
                msj='La longitud de la resumen tiene que ser maximo de {{App\Configuracion::tamañoMaximoResumen}} caracteres.';
                msj=msj+' El tamaño actual es de '+textResum.value.length+' caracteres.';
            }
            
            
            i=1;
            @foreach ($detalles as $itemDetalle)
                
                inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleReposicion}}');
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
            confirmar('¿Está seguro de Aprobar la Reposición?','info','frmRepo');
            

        }





        var edicionActiva = false;
        function desOactivarEdicion(){
            
            console.log('Se activó/desactivó la edición : ' + edicionActiva);
            
            

            @foreach ($detalles as $itemDetalle)
                inputt = document.getElementById('CodigoPresupuestal{{$itemDetalle->codDetalleReposicion}}');
                
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

    
    </script>
     










@endsection
