
@extends('Layout.Plantilla')

@section('titulo')
    Cambiar mi contraseña
@endsection

@section('contenido')
@include('Layout.MensajeEmergenteDatos')
    <br>
    <div class="well"><H3 style="text-align: center;">Cambiar contraseña</H3></div>
    <br>
 

    <form id="frmUsuario" name="frmUsuario" role="form" action="{{route('GestionUsuarios.updateUsuario')}}" class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
        @csrf 
            <input type="text" class="form-control" id="codEmpleado" name="codEmpleado" placeholder="Codigo" value="{{ $empleado->codEmpleado}}" hidden>
            
            <div class="container">
                <div class="row">
                    <div class="col-2">

                    </div>
                    <div class="col">
                        <div class=" ">
                            <label class=" col-form-label" style="">Usuario:</label>
                            <div class="">
                                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingrese nueva usuario..." value="{{$usuario->usuario}}">
                            </div>
                        </div>

                        <div class=" ">
                            <label class=" col-form-label" style="">Contraseña:</label>
                            <div class="">
                                <input type="password" class="form-control" id="password1" name="password1" placeholder="Ingrese nueva contrañesa...">
                            </div>
                        </div>
            
                        <div class=" ">
                            <label class=" col-form-label" style="">Repita Contraseña:</label>
                            <div class="">
                                <input type="password" class="form-control" id="password2" name="password2" placeholder="Ingrese nueva contrañesa...">
                            </div>
                        </div>

                        <br>
            
                        <button type="button" class="btn btn-primary float-right" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                            onclick="registrar()">
                            <i class='fas fa-save'></i> 
                            Actualizar Contraseña
                        </button> 

                        <a href="{{route('GestionUsuarios.Listar')}}" class="btn btn-info float-left"><i class="fas fa-arrow-left"></i> Regresar al Menu</a>
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
            
            limpiarEstilos(['password1','password2','usuario']);
            msj = validarNulidad(msj,'usuario','Usuario');
            msj = validarTamañoMaximoYNulidad(msj,'password1',{{App\Configuracion::tamañoMaximoContra}},'Contraseña nueva');
            msj = validarTamañoMaximoYNulidad(msj,'password2',{{App\Configuracion::tamañoMaximoContra}},'Repita Contraseña nueva');
            msj = validarContenidosIguales(msj,'password1','password2','Las contraseñas actuales deben coincidir');

            return msj;
        }
    </script>

@endsection