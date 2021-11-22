@extends('Layout.Plantilla')

@section('titulo')
    @if($requerimiento->listaParaAtender())
        Atender 
    @else   
        Ver
    @endif
        Requerimiento de Bienes y Servicios

@endsection

@section('contenido')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <div >
        <p class="h1" style="text-align: center">
            @if($requerimiento->listaParaAtender())
                Atender 
            @else   
                Ver
            @endif
            Requerimiento de Bienes y Servicios

        </p>

    </div>
    @include('Layout.MensajeEmergenteDatos')
    
    
    @include('RequerimientoBS.Plantillas.PlantillaVerRequerimiento')
    
       
        
    <div class="row">
        
        @if($requerimiento->puedeMarcarFactura())
                
            <div class="col">
                <button type="button" onclick="clickMarcarQueYaTieneFactura()" class="btn btn-success  ">
                    Factura en Sistema 
                    <i class="fas fa-check"></i>
                </button>
            </div>
        @endif


        @if($requerimiento->adminPuedeSubirArchivos())
            <div class="col BordeCircular" id="divEnteroArchivo">    
                <form method = "POST" action = "{{route('RequerimientoBS.Administrador.subirArchivosAdministrador')}}" 
                    id="frmSubirArchivosAdmin" name="frmSubirArchivosAdmin"  enctype="multipart/form-data">
                    @csrf
                    <input type="{{App\Configuracion::getInputTextOHidden()}}" id="codRequerimiento" name="codRequerimiento" value="{{$requerimiento->codRequerimiento}}">
                    
                    <div class="row">
                        <div class="col ">
                        
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_añadir" value="1" checked>
                                <label class="form-check-label" for="ar_añadir">
                                    Añadir Archivos
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_sobrescribir" value="2">
                                <label class="form-check-label" for="ar_sobrescribir">
                                    Sobrescribir archivos
                                </label>
                            </div>
                    

                        </div>
                        <div class="w-100"></div>
                        <div class="col">
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
                            <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                                    style="display: none" onchange="cambio()">  
                                            <input type="hidden" name="nombreImgImagenEnvio" id="nombreImgImagenEnvio">                 
                            <label class="label" for="filenames" style="font-size: 12pt;">       
                                    <div id="divFileImagenEnvio" class="hovered">       
                                    Seleccionar archivos comprobantes  
                                    <i class="fas fa-upload"></i>        
                                </div>       
                            </label>  

                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-left">
                            <button type="button" class="btn btn-primary" onclick="clickSubirArchivos()">
                                <i class="fas fa-save"></i>
                                Guardar archivos
                            </button>
                        </div>
                        @if($requerimiento->getCantidadArchivosAdmin()!=0)
                            
                            <div class="col text-right">
                                <button type="button" class="btn btn-danger btn-xs" onclick="clickEliminarArchivosAdmin()">
                                    <i class="fas fa-trash"></i>
                                    Eliminar mis archivos
                                </button>

                            </div>
                       
                        @endif
                    </div>
                   

                </form>
            </div>    
        @else
            <div class="col"></div>
        @endif

        @if($requerimiento->listaParaAtender())
            <div class="col">

                <button id="botonAtender" type="button" onclick="clickAtenderReq()" class="btn btn-success  ">
                    Atender Requerimiento
                </button>

                <button type="button" class='btn btn-warning ' style="margin-right:6px;"
                    data-toggle="modal" data-target="#ModalObservar">
                    <i class="fas fa-eye-slash"></i>
                    Observar
                </button> 

                <a href="#" class="btn btn-danger" style=""onclick="clickRechazar()">
                    <i class="fas fa-times"></i> 
                    Rechazar
                </a>

            </div>
        @else
            <div class="col"></div>
        @endif
       
    </div>
    
 

    <div class="row">  
            <div class="col">
                <a href="#" class='btn btn-info' onclick="clickVolverMenu()">
                    <i class="fas fa-arrow-left"></i> 
                    Regresar al Menú
                </a>      
            </div>
            

                
 
    </div>
        
       



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

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

@include('Layout.EstilosPegados')

@section('script')

<script>
    

    var RequerimientoAtendido = 
        @if($requerimiento->verificarEstado('Atendida') || $requerimiento->verificarEstado('Contabilizada')  )
            true;
        @else 
            false;
        @endif
    
    
    function clickVolverMenu(){
        if(!RequerimientoAtendido){
            confirmarConMensaje('No ha atendido el requerimiento.','¿Seguro que desea volver al listado de requerimientos? <br> No ha marcado el requerimiento como atendido.','warning',ejecutarVolverAlMenu);
        }else 
            ejecutarVolverAlMenu();
    }

    function ejecutarVolverAlMenu(){
        location.href = "{{route('RequerimientoBS.Administrador.Listar')}}";

    }

    $(document).ready(function(){
        contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
    });

    function clickObservar(){
        
        observacion = document.getElementById('observacion').value;
        if(observacion==""){
            alerta("Ingrese una observación válida");
            return;
        }
        tamañoActualObs = observacion.length;
        tamañoMaximoObservacion =  {{App\Configuracion::tamañoMaximoObservacion}};
        if( tamañoActualObs  > tamañoMaximoObservacion){
            alerta('La observación puede tener máximo hasta ' +    tamañoMaximoObservacion + 
                " caracteres. (El tamaño actual es " + tamañoActualObs + ")");
            return false;
        }


        confirmarConMensaje('Confirmación',"¿Desea observar el requerimiento?","warning",ejecutarObservacion);

    }

    function ejecutarObservacion(){
        document.formObservar.submit();
    }

    
    function clickRechazar() {
        confirmarConMensaje('¿Esta seguro de rechazar el requerimiento?','','warning',ejecutarRechazar);
    }
    function ejecutarRechazar() {
        window.location.href="{{route('RequerimientoBS.rechazar',$requerimiento->codRequerimiento)}}";
    }



    function clickAtenderReq(){


        confirmarConMensaje("Confirmación","¿Seguro que desea atender el requerimiento?","warning",submitearAtender);
    }


    function submitearAtender(){
        location.href="{{route('RequerimientoBS.Administrador.Atender',$requerimiento->codRequerimiento)}}"
    }


    function clickSubirArchivos(){
        msjError = validarArchivos();
        if(msjError!="")
        {
            alerta(msjError);
            return ;
        }  

        confirmarConMensaje('Confirmación','¿Desea subir los archivos ingresados?','warning',ejecutarSubirArchivos);
    }

    function ejecutarSubirArchivos(){

        document.frmSubirArchivosAdmin.submit();

    }


    function clickEliminarArchivosAdmin(){
        
        confirmarConMensaje('Confirmación','¿Desea eliminar sus archivos de administrador?','warning',ejecutarEliminarArchivosAdmin);
    }

    function ejecutarEliminarArchivosAdmin(){
        location.href = "{{route('RequerimientoBS.Administrador.eliminarArchivosAdmin',$requerimiento->codRequerimiento)}}"

    }

    function cambio(){
        msjError = validarArchivos();
        if(msjError!=""){
            alerta(msjError);
            return;
        }

        listaArchivos="";
        cantidadArchivos = document.getElementById('filenames').files.length;

        console.log('----- Cant archivos seleccionados:' + cantidadArchivos);
        for (let index = 0; index < cantidadArchivos; index++) {
            nombreAr = document.getElementById('filenames').files[index].name;
            console.log('Archivo ' + index + ': '+ nombreAr);
            listaArchivos = listaArchivos +', '+  nombreAr; 
        }
        listaArchivos = listaArchivos.slice(1, listaArchivos.length);
        document.getElementById("divFileImagenEnvio").innerHTML= listaArchivos;
        $('#nombresArchivos').val(listaArchivos);
    }

    function validarArchivos(){
        cantidadArchivos = document.getElementById('filenames').files.length;
        
        msj="";
        for (let index = 0; index < cantidadArchivos; index++) {
            var imgsize = document.getElementById('filenames').files[index].size;
            nombre = document.getElementById('filenames').files[index].name;
            if(imgsize > {{App\Configuracion::pesoMaximoArchivoMB}}*1000*1000 ){
                msj=('El archivo '+nombre+' supera los  {{App\Configuracion::pesoMaximoArchivoMB}}Mb, porfavor ingrese uno más liviano o comprima.');
            }
        }
        
        if(cantidadArchivos=="")
            msj="Debe seleccionar archivos a subir.";

        return msj;

    }





    function clickMarcarQueYaTieneFactura(){


        confirmarConMensaje('Confirmación','¿Desea marcar la factura como HABIDA? <br> Asegúrese de que la factura se encuentre en los archivos del Colaborador/Administrador.','warning',ejecutarMarcarFacturaHabida);
    }
    function ejecutarMarcarFacturaHabida(){
        location.href = "{{route('RequerimientoBS.Administrador.marcarQueYaTieneFactura',$requerimiento->codRequerimiento)}}"


    }












</script>


<style>
    .BordeCircular{
        border-radius: 10px;
        background-color:rgb(190, 190, 190)
    }
    .hovered:hover{
        background-color:rgb(97, 170, 170);

    }
</style>
@endsection
