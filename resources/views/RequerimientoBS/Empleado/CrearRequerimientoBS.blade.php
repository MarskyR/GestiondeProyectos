@extends('Layout.Plantilla')

@section('titulo')
  Registrar Requerimiento de Bienes y Servicios
@endsection

@section('contenido')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div >
    <p class="h1" style="text-align: center">Registrar Requerimiento de Bienes y Servicios</p>


</div>


<form method = "POST" action = "{{route('RequerimientoBS.Empleado.store')}}" id="frmrepo" name="frmrepo"  enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepasEmpleado" id="codigoCedepasEmpleado" value="{{ $empleadoLogeado->codigoCedepas }}">
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$empleadoLogeado->codEmpleado}}">
    
    @csrf
    <div class="container" >
        <div class="row">           
            <div class="col-md"> {{-- COLUMNA IZQUIERDA 1 --}}
                <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                    <div class="row">
                      <div  class="colLabel">
                            <label for="fecha">Fecha</label>
                      </div>
                      <div class="col">
                                               
                                <div class="input-group date form_date" style="width: 100px;" data-date-format="dd/mm/yyyy" data-provide="datepicker">
                                    <input type="text"  class="form-control" name="fechaHoy" id="fechaHoy" disabled
                                        value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" >     
                                </div>
                           
                      </div>

                      <div class="w-100"></div> {{-- SALTO LINEA --}}
                      <div  class="colLabel">
                              <label for="ComboBoxProyecto" id="lvlProyecto">Proyecto</label>

                      </div>
                      <div class="col"> {{-- input de proyecto --}}
                        <select class="form-control"  id="codProyecto" name="codProyecto" 
                                onchange="actualizarCodPresupProyecto()" >
                            <option value="-1">Seleccionar</option>
                            @foreach($proyectos as $itemproyecto)
                                <option value="{{$itemproyecto->codProyecto}}" >
                                    [{{$itemproyecto->codigoPresupuestal}}] {{$itemproyecto->nombre}} 
                                </option>                                 
                            @endforeach 
                        </select>   
                      </div>
                       

                      <div class="w-100"></div>
                      <div  class="colLabel">
                        <label for="fecha">Código Cedepas</label>

                      </div>
                      <div class="col">
                            <input type="text" readonly class="form-control" value="{{App\RequerimientoBS::calcularCodigoCedepas($objNumeracion)}}
                            ">    
                      </div>
                      <div class="w-100"></div>
                      <div  class="colLabel">
                        <label for="fecha">
                            Cta Bancaria Proveedor
                            <br>
                            <b style="color:rgb(145, 145, 145)">
                                (Dato opcional)
                            </b>
                           
                        </label>

                      </div>
                      <div class="col">
                        <input type="text" class="form-control" name="cuentaBancariaProveedor" id="cuentaBancariaProveedor" 
                            value="" placeholder="En caso de no ser BBVA, colocar CCI">    
                      </div>
                      <div class="w-100"></div>
                     


                    </div>


                </div>
                
                
                
                
            </div>


            <div class="col-md"> {{-- COLUMNA DERECHA --}}
                <div class="container">
                    <div style="margin-bottom: 1%">
                        
                        <label for="fecha">Justificación <b id="contador" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                        <textarea class="form-control" name="justificacion" id="justificacion" aria-label="With textarea"
                             cols="3"></textarea>
        
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
                                                    
                    <th> 
                        <div> {{-- INPUT PARA tipo--}}
                            
                            <select class="form-control"  id="ComboBoxUnidad" name="ComboBoxUnidad" >
                                <option value="-1">Seleccionar</option>
                                @foreach($listaUnidadMedida as $itemunidad)
                                    <option value="{{$itemunidad->nombre}}" >
                                        {{$itemunidad->nombre}}
                                    </option>                                 
                                @endforeach 
                            </select>        
                        </div>
                        
                    </th>                                 
                    <th>
                        <div  >  
                            <input type="number" min="0"  class="form-control" name="cantidad" id="cantidad">     
                        </div>
                    </th>
                    <th  class="text-center">
                        <div > {{-- INPUT PARA  concepto--}}
                            <input type="text" class="form-control" name="descripcion" id="descripcion">     
                        </div>

                    </th>
                 
                    <th  class="text-center">
                        <div > {{-- INPUT PARA codigo presup--}}
                            <input type="text" class="form-control" name="codigoPresupuestal" id="codigoPresupuestal">     
                        </div>

                    </th>
                    <th  class="text-center">
                        <div >
                            <button type="button" id="btnadddet" name="btnadddet" 
                                class="btn btn-success btn-sm" onclick="agregarDetalle()" >
                                <i class="fas fa-plus"></i>Agregar
                            </button>
                        </div>      
                    
                    </th>                                            
                    
                </thead>
                
                
                <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                    <!--
                    <th width="10%" class="text-center">Fecha Cbte</th>                                        
                    -->
                    <th width="14%">Unidad Medida</th>                                 
                    <th width="12%"> Cantidad</th>
                    <th width="41%" class="text-center">Descripcion </th>
                    
                    
                    <th width="11%" class="text-center">Cod Presup </th>
                    
                    <th width="7%" class="text-center">Opciones</th>                                            
                    
                </thead>
                <tfoot>

                                                                                    
                </tfoot>
                <tbody>
              
                    

                </tbody>
            </table>
        </div> 


        
            

        <div class="row" id="divTotal" name="divTotal">                       
            <div class="col-md-8">
            </div>   
            <div class="col-md-2">                        
                <!--
                <label for="">Total Gastado: </label>    
                -->
            </div>   
            <div class="col-md-2">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">
                <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
                <input type="hidden" name="totalRendido" id="totalRendido">
                <!--                              
                <input type="text" class="form-control text-right" name="total" id="total" readonly>   
                -->

            </div>   
          
            <div class="w-100">

            </div>
            <div class="col-md-8"></div>



            {{-- Este es para subir todos los archivos x.x  --}}
            <div class="col" id="divEnteroArchivo">            
                <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
                <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                        style="{{App\Configuracion::getDisplayNone()}}" onchange="cambio()">  
                                <input type="hidden" name="nombreImgImagenEnvio" id="nombreImgImagenEnvio">                 
                <label class="label" for="filenames" style="font-size: 12pt;">       
                        <div id="divFileImagenEnvio" class="hovered">       
                        Subir archivos  
                        <i class="fas fa-upload"></i>        
                    </div>       
                </label>       
            </div>    






        </div>
                

            
    </div> 
    <div class="row">
        <div class="col text-left">
            
            <a href="{{route('RequerimientoBS.Empleado.Listar')}}" class='btn btn-info'>
                <i class="fas fa-arrow-left"></i> 
                Regresar al Menú
            </a>  
        </div>
        <div class="col">

        </div>
        <div class="col text-right">

            <button type="button" class="btn btn-primary " id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                onclick="registrar()">
                <i class='fas fa-save'></i> 
                Registrar
            </button> 
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

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

@include('Layout.EstilosPegados')

@section('script')

       {{-- PARA EL FILE  --}}
<script type="application/javascript">
    //se ejecuta cada vez que escogewmos un file

        var detalleReq=[];
        var cont = 0;
        $(document).ready(function(){
            contadorCaracteres('justificacion','contador','{{App\Configuracion::tamañoMaximoResumen}}');

        });
        var listaArchivos = '';
        function registrar(){
            msje = validarFormularioCrear();
            if(msje!="")
                {
                    alerta(msje);
                    return false;
                }
            
            confirmar('¿Está seguro de crear el requerimiento?','info','frmrepo');
            
        }
        

        function validarFormularioCrear(){
            msj='';

            limpiarEstilos(['codProyecto','justificacion','cuentaBancariaProveedor','codigoPresupuestal']);

            msj = validarSelect(msj,'codProyecto',-1,'Proyecto');
            
            msj = validarTamañoMaximoYNulidad(msj,'justificacion',{{App\Configuracion::tamañoMaximoResumen}},'Justificación');
            //solo validamos tam maximo pq no es necesario que se ingrese
            msj = validarTamañoMaximo(msj,'cuentaBancariaProveedor',{{App\Configuracion::tamañoMaximoNroCuentaBanco}},'Cuenta Bancaria del proveedor');
             
            msj = validarCantidadMaximaYNulidadDetalles(msj,'cantElementos',{{App\Configuracion::valorMaximoNroItem}});

            //msj = validarNulidad(msj,'nombresArchivos','Archivos');

            //validamos que todos los items tengan el cod presupuestal correspondiente a su proyecto
            for (let index = 0; index < detalleReq.length; index++) {
                console.log('Comparando ' + index + " starst:" +detalleReq[index].codigoPresupuestal.startsWith(codPresupProyecto) )
                msj = validarCodigoPresupuestal(msj,"colCodigoPresupuestal"+index, codPresupProyecto,"Código presupuestal del Ítem N°" + (index+1));
            }

            
            return msj;
        }
    
    </script>
     
    

    @include('RequerimientoBS.Plantillas.EditCreateReqBS')







@endsection
