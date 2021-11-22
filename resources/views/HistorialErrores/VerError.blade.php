@extends('Layout.Plantilla')

@section('titulo')
Ver Error
@endsection

{{-- ESTA VISTA LA USA EL EMPLEADO, PARA VER UNA SOLICITUD DE FONDOS --}}

@section('contenido')

    <div class="container">
        <div class="row">
            <div  class="colLabel">
                    <label for="fecha">Fecha y Hora: </label>
            </div>
            <div class="col">
                                        
                        <div class="input-group date form_date " style="" data-date-format="dd/mm/yyyy" data-provide="datepicker">
                            <input type="text"  class="form-control" name="fecha" id="fecha" disabled
                                value="{{$error->fechaHora}}" >     
                        </div>
    
            </div>
            
            
            <div  class="colLabel">
                    <label for="codSolicitud">Empleado</label>
    
            </div>
            <div class="col"> {{-- Combo box de empleado --}}
                    <input readonly  type="text" class="form-control" name="" id="" readonly value="{{$error->getEmpleado()->getNombreCompleto()}}">     
            </div>
            
            
            <div  class="colLabel">
                <label for="codSolicitud">IP</label>
    
            </div>
            <div class="col"> {{-- Combo box de empleado --}}
                    <input readonly  type="text" class="form-control"  readonly value="{{$error->ipEmpleado}}">     
            </div>
            <div class="w-100"></div> {{-- SALTO LINEA --}}
            
            
    
    
    
            <div  class="colLabel">
                <label for="fecha">Controller: </label>
            </div>
            <div class="col">
                                        
                        <div class="input-group date form_date " style="" data-date-format="dd/mm/yyyy" data-provide="datepicker">
                            <input type="text"  class="form-control"  disabled
                                value="{{$error->controllerDondeOcurrio}}" >     
                        </div>
    
            </div>
            
            
            <div  class="colLabel">
                <label for="codSolicitud">Funcion</label>
    
            </div>
            <div class="col"> {{-- Combo box de empleado --}}
                    <input readonly  type="text" class="form-control"   readonly value="{{$error->funcionDondeOcurrio}}">     
            </div>
            
            <div  class="colLabel">
                <label for="codSolicitud">Codigo del Error</label>
    
            </div>
            <div class="col"> {{-- Combo box de empleado --}}
                    <input readonly  type="text" class="form-control"   readonly value="{{$error->codErrorHistorial}}">     
            </div>
            <div class="colLabel" style="margin-top: 7px"> {{-- Combo box de empleado --}}
                <label style="color: red">({{$error->estadoError==1?'Solucionado':'No Solucionado'}})</label>
            </div>
            <div class="w-100"></div> {{-- SALTO LINEA --}}
            <div class="col"> {{-- Combo box de empleado --}}
                <form method = "POST" action = "{{route('HistorialErrores.guardarRazonSolucionError')}}" id="frmsolucion" name="frmsolucion">
                    @csrf
                    <input type="hidden" name="codErrorHistorial" id="codErrorHistorial" value="{{$error->codErrorHistorial}}">
                    <div class="row">
                        <div class="col">
                            <label for="">Razon del error:</label>
                            <div class="w-100"></div> {{-- SALTO LINEA --}}
                            <div class="col">
                                <textarea class="form-control" style="font-size: 9.5pt" name="razon" id="razon" rows="3">{{$error->razon}}</textarea>
                            </div>
                        </div>
                        <div class="col">
                            <label for="">Solucion del error:</label>
                            <div class="w-100"></div> {{-- SALTO LINEA --}}
                            <div class="col">
                                <textarea class="form-control" style="font-size: 9.5pt" name="solucion" id="solucion" rows="3">{{$error->solucion}}</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

    
    
    
    
    
            <div class="w-100"></div> {{-- SALTO LINEA --}}
            <label for="">Mensaje del error:</label>
            <div class="w-100"></div> {{-- SALTO LINEA --}}
            
            <div class="col">
                <textarea class="form-control" style="font-size: 9.5pt"   cols="30" rows="29" disabled>{{$error->descripcionError}}</textarea>
    
            </div>
            <div class="w-100"></div>
            <label for="">Para ver la petición POST ver la consola en JAVASCRIPT:</label>
            <div class="w-100"></div> {{-- SALTO LINEA --}}
            
            <div class="col" id="paraJSON" style="display: none">
                {{$error->formulario}}
    
            </div>
            
            <div class="w-100"></div> {{-- SALTO LINEA --}}
            <label for=""></label>
            <div class="col"> {{-- Combo box de empleado --}}
                <a href="{{route('HistorialErrores.Listar')}}" class="btn btn-success">Regresar al listado</a> 
                <button type="button" class="btn btn-primary" id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                    onclick="registrar()">
                    <i class='fas fa-save'></i> 
                    Guardar
                </button> 
            </div>
        </div>

    </div>
    
           
    

 
@endsection

@section('script')
<script>
    $(document).ready(function(){
        console.log('REPORTE DEL REQUEST EN JSON:');

        cargarJSON();

    });

    function cargarJSON(){
        texto = document.getElementById('paraJSON').innerHTML;
        objeto = JSON.parse(texto);
        console.log(objeto);
        

    }
    function registrar(){
        confirmar('¿Está seguro de guardar los cambios?','info','frmsolucion');
    }


</script>

@endsection