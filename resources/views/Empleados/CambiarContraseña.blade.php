
@extends('Layout.Plantilla')

@section('titulo')
    Cambiar mi contraseña
@endsection

@section('contenido')

    <br>
    <div class="well"><H3 style="text-align: center;">Cambiar mi contraseña</H3></div>
    <br>

        @if (session('datos'))
            <div class ="alert alert-warning alert-dismissible fade show mt-3" role ="alert">
                {{session('datos')}}
            <button type = "button" class ="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true"> &times;</span>
            </button>
            
            </div>
        @endif

    <form id="frmUsuario" name="frmUsuario" role="form" action="{{route('GestionUsuarios.updateContrasena')}}" class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
        @csrf 
            <input type="text" class="form-control" id="codEmpleado" name="codEmpleado" placeholder="Codigo" value="{{ $empleado->codEmpleado}}" hidden>
            
            <div class="container">
                <div class="row">
                    <div class="col-2">

                    </div>
                    <div class="col">

                        <div class=" ">
                            <label class=" col-form-label" style="">Contraseña Actual:</label>
                            <div class="">
                                <input type="password" class="form-control" id="contraseñaActual1" name="contraseñaActual1" placeholder="Contraseña Actual...">
                            </div>
                        </div>
            
                        <div class=" ">
                            <label class=" col-form-label" style="">Repita Contraseña actual:</label>
                            <div class="">
                                <input type="password" class="form-control" id="contraseñaActual2" name="contraseñaActual2" placeholder="Contraseña actual...">
                            </div>
                        </div>

                        <br><br>

                        <div class=" ">
                            <label class=" col-form-label" style="">Nueva Contraseña:</label>
                            <div class="">
                                <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Nueva Contraseña...">
                            </div>
                        </div>
            
                        <div class=" ">
                            <label class=" col-form-label" style="">Repetir Nueva Contraseña:</label>
                            <div class="">
                                <input type="password" class="form-control" id="contraseña2" name="contraseña2" placeholder="Nueva Contraseña...">
                            </div>
                        </div>

                        <br>
                        <button type="button" class="btn btn-primary float-right" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                            onclick="registrar()">
                            <i class='fas fa-save'></i> 
                            Actualizar Contraseña
                        </button> 


                    </div>
                    <div class="col-2">


                    </div>
                </div>
            </div>
            

            
    </form>
@endsection
@include('Layout.ValidatorJS')
@section('script')
    <script>
        function registrar(){
            msje = validarCambioContraseña();
            if(msje!="")
                {
                    alerta(msje);
                    return false;
                }
            
            confirmar('¿Seguro de guardar la contraseña?','info','frmUsuario');
            
        }
                    
        function validarCambioContraseña(){ //Retorna TRUE si es que todo esta OK y se puede hacer el submit
            msj='';
            
            limpiarEstilos(['contraseñaActual1','contraseñaActual2','contraseña','contraseña2']);
            msj = validarTamañoMaximoYNulidad(msj,'contraseñaActual1',{{App\Configuracion::tamañoMaximoContra}},'Contraseña Actual');
            msj = validarTamañoMaximoYNulidad(msj,'contraseñaActual2',{{App\Configuracion::tamañoMaximoContra}},'Repita Contraseña actual');
            msj = validarContenidosIguales(msj,'contraseñaActual1','contraseñaActual2','Las contraseñas actuales deben coincidir');
            msj = validarTamañoMaximoYNulidad(msj,'contraseña',{{App\Configuracion::tamañoMaximoContra}},'Nueva Contraseña');
            msj = validarTamañoMaximoYNulidad(msj,'contraseña2',{{App\Configuracion::tamañoMaximoContra}},'Repetir Nueva Contraseña');
            msj = validarContenidosIguales(msj,'contraseña','contraseña2','Las contraseñas nuevas deben coincidir');

            return msj;
        }
        
    </script>

@endsection