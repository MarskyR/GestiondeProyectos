
@extends('Layout.Plantilla')
@section('titulo')
    Mis Datos
@endsection
@section('contenido')


<div class="well">
    <H3 style="text-align: center;">
        Mis datos personales
    </H3>
</div>

<br>
    @include('Layout.MensajeEmergenteDatos')
    
    <form id="frmEmpleado" name="frmEmpleado" role="form" action="{{route('GestionUsuarios.updateDPersonales')}}" 
        class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
        @csrf 
            <input type="text" class="form-control" id="codEmpleado" name="codEmpleado" placeholder="Codigo" value="{{ $empleado->codEmpleado}}" hidden>
            
            <div class="row">
                <div class="col"></div>
                <div class="col-4">
                    <label class="col-form-label" style="">Nombres:</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres..." value="{{$empleado->nombres}}" readonly>
                
                </div>
                
                <div class="col-4">
                    <label class="col-form-label" style="">Apellidos:</label>
                
                    <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos..." value="{{$empleado->apellidos}}" readonly>
                
                </div>
                <div class="col"></div>
                
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col-4">
                    <label class="col-form-label" style="">DNI:</label>
                
                    <input type="number" class="form-control" id="DNI" name="DNI" placeholder="DNI..." value="{{$empleado->dni}}" readonly>
               
                </div>
              
                <div class="col-4">
                    <label class="col-form-label" style="">Correo:</label>
                
                    <input type="text" class="form-control" id="correo" name="correo" placeholder="correo..." value="{{$empleado->correo}}">
                
                </div>
                <div class="col"></div>
                
            </div>

            <div class="row">
                <div class="col"></div>
                <div class="col-4">
                    <label class="col-form-label" style="">Sexo:</label>
                   
                    <select class="form-control" name="sexo" id="sexo">
                        <option value="-1" >- Sexo -</option>
                        <option value="M" {{'M'==$empleado->sexo ? 'selected':''}}>Mujer</option>
                        <option value="H" {{'H'==$empleado->sexo ? 'selected':''}}>Hombre</option>
                    </select>
                
                </div>
                
                <div class="col-4">
                    <label class="col-form-label" style="">Fecha Nacimiento:</label>
                    <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker">
                        {{-- INPUT PARA EL CBTE DE LA FECHA --}}
                        <input type="text" style="text-align: center" class="form-control" name="fechaNacimiento" id="fechaNacimiento"
                                value="{{$empleado->fechaNacimiento=='0000-00-00' ? Carbon\Carbon::now()->format('d/m/Y') : date('d/m/Y',strtotime($empleado->fechaNacimiento))}}" style="font-size: 10pt;"> 
                        
                        <div class="input-group-btn">                                        
                            <button class="btn btn-primary date-set" type="button"   onclick="">
                                <i class="fas fa-calendar fa-xs"></i>
                            </button>
                        </div>
                    </div>   

                </div>
                <div class="col"></div>
                
            </div>

            <div class="row">
                <div class="col"></div>
                <div class="col-4">
                    <label class="col-form-label" style="">Nombre del cargo:</label>
                   
                    <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo..." value="{{$empleado->nombreCargo}}">
                    
                </div>
                
                <div class="col-4">
                    <label class="col-form-label" style="">Direccion:</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Direccion..." value="{{$empleado->direccion}}">
                
                </div>
                <div class="col"></div>
                
            </div>
             
            <div class="row">
                <div class="col"></div>
                <div class="col-4">
                    <label class="col-form-label" style="">Telefono:</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono..." value="{{$empleado->nroTelefono}}">
              
                </div>
                <div class="col"></div>
                
            </div>
             
    

            <div class="row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
                <div class="col">
                    <button type="button" class="btn btn-primary" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                        onclick="validarCambioDatos()">
                       
                        Actualizar Datos
                        <i class='fas fa-save'></i> 
                    </button> 

                </div>
                
                   
            </div>
               
    </form>

@endsection
@section('script')
@include('Layout.ValidatorJS')
   
<script type="text/javascript"> 
      
    function validarFormulario(){
        limpiarEstilos(
            ['correo','sexo','fechaNacimiento','cargo','direccion','telefono']);
        msj = "";
        msj = validarTamañoMaximoYNulidad(msj,'correo',{{App\Configuracion::tamañoMaximoCorreo}},'correo');
        msj = validarNulidad(msj,'fechaNacimiento','Fecha de Nacimiento');
        msj = validarTamañoMaximoYNulidad(msj,'cargo',{{App\Configuracion::tamañoMaximoNombreCargo}},'Nombre del Cargo');
        msj = validarTamañoMaximoYNulidad(msj,'direccion',{{App\Configuracion::tamañoMaximoDireccion}},'Direccion');
        msj = validarTamañoMaximoYNulidad(msj,'telefono',{{App\Configuracion::tamañoMaximoTelefono}},'Telefono');
        msj = validarSelect(msj,'sexo',-1,'Sexo');

        return msj;
    }

    function validarCambioDatos(){
        msj = validarFormulario();
        if(msj!=''){
            alerta(msj);
            return;
        }
        confirmarConMensaje('Confirmacion','¿Seguro de guardar los cambios de los datos personales?','warning',ejecutarSubmit);
    }

    function ejecutarSubmit(){

        document.frmEmpleado.submit(); // enviamos el formulario	  

    }
    
</script>



@endsection



