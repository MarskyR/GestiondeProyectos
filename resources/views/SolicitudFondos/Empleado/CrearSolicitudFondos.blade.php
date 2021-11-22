@extends('Layout.Plantilla')

@section('titulo')
Crear Solicitud
@endsection
@section('contenido')
<div >
    <p class="h1" style="text-align: center">Registrar Nueva Solicitud de Fondos</p>
</div>

<form method = "POST" action = "{{route('SolicitudFondos.Empleado.Guardar')}}" 
onsubmit="" id="frmsoli" name="frmsoli"  enctype="multipart/form-data" >
        
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden"   id="codigoCedepas" value="{{ $empleadoLogeado->codigoCedepas }}">
    
    @csrf
    <div class="container" style="">
        <div class="row">           
            <div class="col-md" style=""> {{-- COLUMNA IZQUIERDA 1 --}}
                <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                    <div class="row">
                      <div  class="colLabel">
                            <label for="fecha">Fecha Actual:</label>
                      </div>
                      <div class="col">
                                <div  style="width: 300px; " >
                                    <input type="text" style="margin:0px auth;" class="form-control" name="fecha" id="fecha" disabled 
                                        value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" >     
                                </div>
                      </div>
                      
                      <div class="w-100"></div> {{-- SALTO LINEA --}}
                    

                      <div  class="colLabel">
                            <label for="fecha">Girar a la orden de:</label>

                      </div>
                      <div class="col">
                            <input type="text" class="form-control" name="girarAOrden" 
                            id="girarAOrden" value="{{App\Empleado::getEmpleadoLogeado()->getNombreCompleto()}}">    

                      </div>
                      <div class="w-100"></div> {{-- SALTO LINEA --}}
                      <div  class="colLabel">
                            <label for="fecha">Nro Cuenta:</label>

                      </div>
                      <div class="col">
                            <input type="text" class="form-control" name="nroCuenta" id="nroCuenta">    
                      </div>
                      <div class="w-100"></div> {{-- SALTO LINEA --}}
                      <div  class="colLabel">
                            <label for="fecha">Banco:</label>

                      </div>
                      <div class="col"> {{-- Combo box de banco --}}
                            <select class="form-control"  id="ComboBoxBanco" name="ComboBoxBanco" >
                                <option value="-1">-- Seleccionar -- </option>
                                @foreach($listaBancos as $itemBanco)
                                    <option value="{{$itemBanco['codBanco']}}" >
                                        {{$itemBanco->nombreBanco}}
                                    </option>                                 
                                @endforeach 
                            </select>      
                      </div>
                      
                      <div class="w-100"></div> {{-- SALTO LINEA --}}
                      <div  class="colLabel">
                            <label for="codSolicitud">Código Solicitud:</label>

                      </div>
                      <div class="col"> 
                          {{-- ESTE INPUT REALMENTE NO SE USARÁ PORQUE EL CODIGO cedep SE CALCULA EN EL BACKEND (pq es más actual) --}}
                            <input type="text" class="form-control" 
                                value="{{App\SolicitudFondos::calcularCodigoCedepas($objNumeracion)}}" 
                                readonly>     
                      </div>



                    </div>


                </div>
                
                
                
                
            </div>


            <div class="col-md"> {{-- COLUMNA DERECHA --}}
                
                
                <div style="margin-bottom: 1%">
                    <label for="fecha">Justificación <b id="contador" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                    <textarea class="form-control" name="justificacion" id="justificacion" aria-label="With textarea"
                         rows="3"></textarea>
    
                </div>
                
                <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                    <div class="row">
                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel2">
                                <label for="ComboBoxProyecto">Proyecto y cod:</label>

                        </div>
                        <div class="col"> {{-- Combo box de proyecto --}}
                                <select class="form-control"  id="ComboBoxProyecto" name="ComboBoxProyecto" onchange="actualizarCodPresupProyecto()" >
                                    <option value="-1">-- Seleccionar -- </option>
                                    @foreach($listaProyectos as $itemProyecto)
                                        <option value="{{$itemProyecto['codProyecto']}}" >
                                            [{{$itemProyecto->codigoPresupuestal}}] {{$itemProyecto->nombre}} 
                                        </option>                                 
                                    @endforeach 
                                </select>      

                        </div>



                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                       

                        <div class="colLabel2">
                                <label for="ComboBoxMoneda">Moneda:</label>
                        </div>
                        <div class="col"> {{-- Combo box de itemMoneda --}}
                                <select class="form-control"  id="ComboBoxMoneda" name="ComboBoxMoneda" >
                                    <option value="-1">-- Seleccionar --</option>
                                    @foreach($listaMonedas as $itemMoneda)
                                        <option value="{{$itemMoneda->codMoneda}}" >
                                            {{$itemMoneda->nombre}}
                                        </option>                                 
                                    @endforeach 
                                </select>      
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
    
      
    

           
        


    {{-- LISTADO DE DETALLES  --}}
    <div class="col-md-12 pt-3">     
        <div class="table-responsive">                           
            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
                <thead >
                    <th width="6%" class="text-center">
                        <div> {{-- INPUT PARA ITEM --}}
                            <input type="text" style="text-align: center" class="form-control" readonly   id="item" value="1">     
                        </div>    
                    </th>                                           
                    <th width="40%"> 
                        <div> {{-- INPUT PARA CONCEPTO--}}
                            <input type="text" class="form-control"  id="concepto">     
                        </div>
                        
                    </th>                                 
                    <th width="10%">
                        <div > {{-- INPUT PARA importe--}}
                            <input type="number" min="0" class="form-control"   id="importe">     
                        </div>
                    </th>
                    <th width="15%" class="text-center">
                        <div> {{-- INPUT PARA codigo presup--}}
                            <input type="text" class="form-control"  id="codigoPresupuestal">     
                        </div>

                    </th>
                    <th width="10%" class="text-center">
                        <div>
                            <button type="button" id="btnadddet"  
                                class="btn btn-success" onclick="agregarDetalle()" >
                                <i class="fas fa-plus"></i>
                                    Agregar
                            </button>
                        </div>      
                    
                    </th>                                            
                    
                </thead>
                
                
                <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                    <th  class="text-center">Ítem</th>                                        
                    <th >Concepto</th>                                 
                    <th > Importe</th>
                    <th  class="text-center">Código Presupuestal</th>
                    <th  class="text-center">Opciones</th>                                            
                    
                </thead>
                <tfoot>

                                                                                    
                </tfoot>
                <tbody>
                    
                </tbody>
            </table>
        </div> 
        

        <div class="row" id="divTotal"  >                       
            <div class="col-md-8">
            </div>   
            <div class="col-md-2">                        
                <label for="">Total : </label>    
            </div>   
            <div class="col-md-2">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">                              
                <input type="hidden" class="form-control text-right" name="total" id="total" readonly>   
                <input type="text" class="form-control text-right" name="totalMostrado" id="totalMostrado" readonly>   
                                            
            </div>   
        </div>
                

            
    </div> 
        
    <br>
    <div class="row">

        <div class="col">
            
            <a href="{{route('SolicitudFondos.Empleado.Listar')}}" class='btn btn-info'>
                <i class="fas fa-arrow-left"></i> 
                Regresar al Menu
            </a>  

        </div>

        <div class="col"></div>
        {{-- Este es para subir todos los archivos x.x  --}}
        <div class="col" id="divEnteroArchivo">            
            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
            <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                    style="{{App\Configuracion::getDisplayNone()}}" onchange="cambio()">  
                            <input type="hidden" name="nombreImgImagenEnvio" id="nombreImgImagenEnvio">                 
            <label class="label" for="filenames" style="font-size: 12pt;">       
                    <div id="divFileImagenEnvio" class="hovered">       
                    Subir archivos comprobantes  
                    <i class="fas fa-upload"></i>        
                </div>       
            </label>       
        </div>    

        <div class="col text-center">  
            <div id="guardar">
                <div class="form-group">
                    
                    


                    <button type="button" class="btn btn-primary" id="btnRegistrar" name="btnRegistrar"
                        data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                            onclick="registrar()">
                        
                        <i class='fas fa-save'></i> 
                        Registrar
                    </button> 
                               
                </div>    
            </div>
        </div>
   
        
    </div>
</form>
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
@include('Layout.ValidatorJS')
@include('Layout.EstilosPegados')

@section('script')
     {{-- <script src="/public/select2/bootstrap-select.min.js"></script>      --}}
     <script>
        var cont=0;
        
        var detalleSol=[];
      
        
        $(document).ready(function(){
            contadorCaracteres('justificacion','contador','{{App\Configuracion::tamañoMaximoJustificacion}}');
        });
 
        function registrar(){
            msj=validarFormCreate();
            if(msj!=''){
                alerta(msj);
                return false;
            }

            confirmar('¿Seguro de crear la solicitud?','info','frmsoli');//[success,error,warning,info]
        }
        


         //Retorna '' si es que todo esta OK y el STRING mensaje de error si no
        function validarFormCreate(){
            msj='';

            limpiarEstilos(['justificacion','ComboBoxProyecto','ComboBoxMoneda','ComboBoxBanco','girarAOrden','nroCuenta']);
            document.getElementById('justificacion').rows = 3;
            
            msj = validarTamañoMaximoYNulidad(msj,'justificacion',{{App\Configuracion::tamañoMaximoJustificacion}},'Justificación')
            msj = validarSelect(msj,'ComboBoxProyecto','-1','Proyecto');
            msj = validarSelect(msj,'ComboBoxMoneda','-1','Moneda');
            msj = validarSelect(msj,'ComboBoxBanco','-1','Banco');
            msj = validarTamañoMaximoYNulidad(msj,'girarAOrden',{{App\Configuracion::tamañoMaximoGiraraAOrdenDe}},'Girar a orden de');
            msj = validarTamañoMaximoYNulidad(msj,'nroCuenta',{{App\Configuracion::tamañoMaximoNroCuentaBanco}},'Número de Cuenta');
            msj = validarCantidadMaximaYNulidadDetalles(msj,'cantElementos',{{App\Configuracion::valorMaximoNroItem}});
             


            for (let index = 0; index < detalleSol.length; index++) {
                console.log('Comparando  ' +codPresupProyecto+' empiezaCon ' + codPresupProyecto.startsWith( detalleSol[index].codigoPresupuestal) )
                
                msj = validarCodigoPresupuestal(msj,"colCodigoPresupuestal"+index, codPresupProyecto,"Código presupuestal del Ítem N°" + (index+1))

            }

            
            return msj;
        }
    
       

       
    
    </script>
     

     @include('SolicitudFondos.Plantillas.CrearEditarSOF-JS')
    
    








@endsection
