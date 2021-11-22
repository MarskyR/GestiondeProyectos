@extends('Layout.Plantilla')

@section('titulo')
    Crear Colaborador
@endsection

@section('estilos')
<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">
@endsection

@section('contenido')

    

    
    <div class="well">
        <H3 style="text-align: center;">
            CREAR COLABORADOR
        </H3>
    </div>
    <br>
    <div class="container">
        <form id="frmempresa" name="frmempresa" role="form" action="{{route('GestionUsuarios.store')}}" class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
            @csrf 
                <div class="row">
                    
                    <div class="col">
    
                        <div class="row">
                            <div class="col">
                                <label class="" >Usuario:</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario..." >
                              
                            </div>
                
                            <div class="col">
                                <label class="" >Contraseña:</label>
                                <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Contraseña...">
                                
                            </div>
                
                            <div class="col">
                                <label class="" >Repetir Contraseña:</label>
                                <input type="password" class="form-control" id="contraseña2" name="contraseña2" placeholder="Contraseña...">
                            
                            </div>
    
                        </div>
                      
    
                    </div>
                     
                </div>
    
    
                <hr>
                
                <div class="row">
                    
                    <div class="col">
                        <div class="row">
    
                                
                            <div class="col">
                                <label class="" >Codigo:</label>
                                
                                <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Codigo..." >
                            
                            </div>
                
                            <div class="col">
                                <label class="" >Nombres:</label>
                                
                                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres..." >
                            
                            </div>
                
                            <div class="col">
                                <label class="" >Apellidos:</label>
                                
                                <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos..." >
                            
                            </div>
                            <div class="col">
                                <label class="" >Correo:</label>
                                <input type="text" class="form-control" id="correo" name="correo" placeholder="example@gmail.com" >
                            </div>
                        
                            <div class="w-100"></div>
                            <br>
                            <div class="col">
                                <label class="" >DNI:</label>
                                
                                <input type="number" class="form-control" id="DNI" name="DNI" placeholder="DNI..." >
                            
                            </div>
                
                            <div class="col">
                                <label class="" >Puesto:</label>
                                
                                <select class="form-control" name="codPuesto" id="codPuesto">
                                    <option value="-1">- Puesto -</option>
                                    @foreach($puestos as $itempuesto)
                                    <option value="{{$itempuesto->codPuesto}}">
                                        {{$itempuesto->nombre}}
                                    </option>    
                                    @endforeach
                                </select>
                            
                            </div>
                        
                            <div class="col">
                                <label class="" >Sede:</label>
                            
                                <select class="form-control" name="codSede" id="codSede">
                                    @foreach($sedes as $itemsede)
                                    <option value="{{$itemsede->codSede}}">{{$itemsede->nombre}}</option>    
                                    @endforeach
                                </select>
                            
                            </div>

                            <!-- NUEVO -->
                            <div class="w-100"></div>
                            <br>
                            <div class="col">
                                <label class="" >Sexo:</label>
                                
                                <select class="form-control" name="sexo" id="sexo">
                                    <option value="-1">- Sexo -</option>
                                    <option value="M">Mujer</option>
                                    <option value="H">Hombre</option>
                                </select>
                            
                            </div>
                
                            <div class="col">
                                <label class="" >Fecha Nacimiento:</label>
                                
                                <div class="col">
                                    <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker">
                                    {{-- INPUT PARA EL CBTE DE LA FECHA --}}
                                        <input type="text" style="text-align: center" class="form-control" name="fechaNacimiento" id="fechaNacimiento"
                                                value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" style="font-size: 10pt;"> 
                                        
                                        <div class="input-group-btn">                                        
                                            <button class="btn btn-primary date-set" type="button"   onclick="">
                                                <i class="fas fa-calendar fa-xs"></i>
                                            </button>
                                        </div>
                                    </div>   
                                </div>
                            
                            </div>
                        
                            <div class="col">
                                <label class="" >Cargo:</label>
                                
                                <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo..." >
                            
                            </div>

                            <div class="col">
                                <label class="" >Direccion:</label>
                                
                                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Direccion..." >
                            
                            </div>

                            <div class="col">
                                <label class="" >Telefono:</label>
                                
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono..." >
                            
                            </div>
                
                        </div>
    
                    </div>
                  
                </div>
                
                <br>
    
                <div class="row text-center">
                    <div class="col">
    
                                
                        <a href="{{route('GestionUsuarios.Listar')}}" class="btn btn-info">
                            <i class="fas fa-arrow-left"></i>
                            Regresar al Menu
                        </a>
                    </div>
                    <div class="col"></div>
                   
                    <div class="col"></div>
                    <div class="col">
    
                        <input type="button" class="btn btn-primary" value="Registrar" onclick="validarregistro()">
    
                    </div>
                    
                </div>
    
                   
    
        </form>
    </div>
    
@endsection


@section('script')  
      
@include('Layout.ValidatorJS')
     
<script type="text/javascript"> 
       

    function validarregistro(){
        msj = validarFormulario();
        if(msj!=''){
            alerta(msj);
            return;
        }
        
        confirmarConMensaje('Confirmacion','¿Desea crear el empleado y su usuario?','warning',ejecutarSubmit);
    }

    function ejecutarSubmit(){

        document.frmempresa.submit(); // enviamos el formulario	  

    }

 
    function validarFormulario(){
        limpiarEstilos(
            ['usuario','contraseña','contraseña2','nombres','apellidos','DNI','correo','codPuesto','codigo','sexo','fechaNacimiento','cargo','direccion','telefono']);
        msj = "";
        
        msj = validarTamañoMaximoYNulidad(msj,'usuario',{{App\Configuracion::tamañoMaximoUsuario}},'usuario');
        msj = validarTamañoMaximoYNulidad(msj,'codigo',{{App\Configuracion::tamañoMaximoCodigoCedepas}},'Código del Colaborador');
        msj = validarTamañoMaximoYNulidad(msj,'contraseña',{{App\Configuracion::tamañoMaximoContra}},'contraseña');
        msj = validarTamañoMaximoYNulidad(msj,'contraseña2',{{App\Configuracion::tamañoMaximoContra}},'Repetir contraseña');
        msj = validarTamañoMaximoYNulidad(msj,'nombres',{{App\Configuracion::tamañoMaximoNombres}},'nombres');
        msj = validarTamañoMaximoYNulidad(msj,'correo',{{App\Configuracion::tamañoMaximoCorreo}},'correo');
        msj = validarTamañoMaximoYNulidad(msj,'apellidos',{{App\Configuracion::tamañoMaximoApellidos}},'apellidos');
        msj = validarNulidad(msj,'DNI','DNI');
        msj = validarNulidad(msj,'fechaNacimiento','Fecha de Nacimiento');

        msj = validarTamañoMaximoYNulidad(msj,'cargo',{{App\Configuracion::tamañoMaximoNombreCargo}},'Nombre del Cargo');
        msj = validarTamañoMaximoYNulidad(msj,'direccion',{{App\Configuracion::tamañoMaximoDireccion}},'Direccion');
        msj = validarTamañoMaximoYNulidad(msj,'telefono',{{App\Configuracion::tamañoMaximoTelefono}},'Telefono');
        /*
        msj = validarNulidad(msj,'cargo','Nombre del Cargo');
        msj = validarNulidad(msj,'direccion','Direccion');
        msj = validarNulidad(msj,'telefono','Telefono');
        */
        msj = validarTamañoExacto(msj,'DNI',8,'DNI'); 
        msj = validarRegExpNombres(msj,'nombres');
        msj = validarRegExpApellidos(msj,'apellidos');
        msj = validarContenidosIguales(msj,'contraseña','contraseña2','Las contraseñas deben coincidir');
        msj = validarSelect(msj,'codPuesto',-1,'Puesto');
        msj = validarSelect(msj,'sexo',-1,'Sexo');
        

        return msj;

    }
    
</script>
@endsection
