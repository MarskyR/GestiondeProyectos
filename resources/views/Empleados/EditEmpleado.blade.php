@extends('Layout.Plantilla')

@section('titulo')
    Editar Datos de Empleado
@endsection
 

@section('contenido')

    
    <div class="well"><H3 style="text-align: center;">EDITAR EMPLEADO</H3></div>
    <br>
    <form id="frmempresa" name="frmempresa" role="form" action="{{route('GestionUsuarios.updateEmpleado')}}" class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
            @csrf 
            <input type="text" class="form-control" id="codEmpleado" name="codEmpleado" placeholder="Codigo" value="{{ $empleado->codEmpleado}}" hidden>
           
            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Codigo:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Codigo..." value="{{$empleado->codigoCedepas}}">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Nombres:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres..." value="{{$empleado->nombres}}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Apellidos:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos..." value="{{$empleado->apellidos}}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">DNI:</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="DNI" name="DNI" placeholder="DNI..." value="{{$empleado->dni}}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Correo:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="correo" name="correo" placeholder="algo@gmail.com" value="{{$empleado->correo}}">
                </div>
            </div>

            


            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Puesto:</label>
                <div class="col-sm-4">
                    <select class="form-control" name="codPuesto" id="codPuesto">
                    @foreach($puestos as $itempuesto)
                    <option value="{{$itempuesto->codPuesto}}" {{$itempuesto->codPuesto==$empleado->codPuesto ? 'selected':''}}>{{$itempuesto->nombre}}</option>    
                    @endforeach
                    </select>
                </div>  
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Sede:</label>
                <div class="col-sm-4">
                    <select class="form-control" name="codSede" id="codSede">
                        @foreach($sedes as $itemsede)
                        <option value="{{$itemsede->codSede}}" {{$itemsede->codSede==$empleado->codSede ? 'selected':''}}>{{$itemsede->nombre}}</option>    
                        @endforeach
                    </select>
                </div>
            </div>
                
            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Sexo:</label>
                <div class="col-sm-4">
                    <select class="form-control" name="sexo" id="sexo">
                        <option value="-1">- Sexo -</option>
                        <option value="M" {{'M'==$empleado->sexo ? 'selected':''}}>Mujer</option>
                        <option value="H" {{'H'==$empleado->sexo ? 'selected':''}}>Hombre</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Fecha Nacimiento:</label>
                <div class="col-sm-4">
                    <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker">
                    {{-- INPUT PARA EL CBTE DE LA FECHA --}}
                        <input type="text" style="text-align: center" class="form-control" name="fechaNacimiento" id="fechaNacimiento"
                                value="{{date('d/m/Y',strtotime($empleado->fechaNacimiento))}}" style="font-size: 10pt;"> 
                        
                        <div class="input-group-btn">                                        
                            <button class="btn btn-primary date-set" type="button"   onclick="">
                                <i class="fas fa-calendar fa-xs"></i>
                            </button>
                        </div>
                    </div>   
                </div>
            </div>
                  
            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Cargo:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo..." value="{{$empleado->nombreCargo}}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Direccion:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Direccion..." value="{{$empleado->direccion}}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-1 col-form-label" style="margin-left:350px;">Telefono:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono..." value="{{$empleado->nroTelefono}}">
                </div>
            </div>

            <br>
              
            <div class="row">
                <div class="col">
                    <a href="{{route('GestionUsuarios.Listar')}}" class="btn btn-info">

                        <i class="fas fa-arrow-left"></i> 
                        Regresar al Menu
                    </a>
                </div>
               
                <div class="col"></div>
                <div class="col"></div>
                <div class="col">
                    <input type="button" class="btn btn-primary " value="Registrar" onclick="validarregistro()" />
                </div>
            </div>
            

    </form>
@endsection

@section('script')
@include('Layout.ValidatorJS')
   

<script type="text/javascript"> 
               
    function limpiarEstilos(listaInputs){
         listaInputs.forEach(element => {
            cambiarEstilo(element,'form-control')
         });

    }
    
    function cambiarEstilo(name, clase){
        document.getElementById(name).className = clase;
    }
    function validarFormulario(){
        limpiarEstilos(
            ['nombres','apellidos','DNI','correo','codPuesto','codigo','sexo','fechaNacimiento','cargo','direccion','telefono']);
        msj = "";
        
       
        msj = validarTamañoMaximoYNulidad(msj,'codigo',{{App\Configuracion::tamañoMaximoCodigoCedepas}},'Código del Colaborador');
        
        msj = validarTamañoMaximoYNulidad(msj,'nombres',{{App\Configuracion::tamañoMaximoNombres}},'nombres');
        msj = validarTamañoMaximoYNulidad(msj,'correo',{{App\Configuracion::tamañoMaximoCorreo}},'correo');
        msj = validarTamañoMaximoYNulidad(msj,'apellidos',{{App\Configuracion::tamañoMaximoApellidos}},'apellidos');
        msj = validarNulidad(msj,'DNI','DNI');

        msj = validarNulidad(msj,'fechaNacimiento','Fecha de Nacimiento');
         
        msj = validarTamañoMaximoYNulidad(msj,'cargo',{{App\Configuracion::tamañoMaximoNombreCargo}},'Nombre del Cargo');
        msj = validarTamañoMaximoYNulidad(msj,'direccion',{{App\Configuracion::tamañoMaximoDireccion}},'Direccion');
        msj = validarTamañoMaximoYNulidad(msj,'telefono',{{App\Configuracion::tamañoMaximoTelefono}},'Telefono');

        msj = validarTamañoExacto(msj,'DNI',8,'DNI'); 
        msj = validarRegExpNombres(msj,'nombres');
        msj = validarRegExpApellidos(msj,'apellidos');
      
        msj = validarSelect(msj,'codPuesto',-1,'Puesto');
        msj = validarSelect(msj,'sexo',-1,'Sexo');

        return msj;

    }

    function validarregistro(){
        msj = validarFormulario();
        if(msj!=''){
            alerta(msj);
            return;
        }
        
        confirmarConMensaje('Confirmacion','¿Desea editar el empleado?','warning',ejecutarSubmit);
    }

    function ejecutarSubmit(){

        document.frmempresa.submit(); // enviamos el formulario	  

    }
    
</script>

 @endsection