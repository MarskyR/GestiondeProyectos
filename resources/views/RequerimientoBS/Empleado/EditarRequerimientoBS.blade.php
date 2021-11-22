@extends('Layout.Plantilla')

@section('titulo')
Editar Requerimiento de Bienes y Servicios
@endsection


@section('tiempoEspera')
<div class="loader" id="pantallaCarga"></div>
@endsection


@section('contenido')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div >
    <p class="h1" style="text-align: center">Editar Requerimiento de Bienes y Servicios</p>


</div>

@include('Layout.MensajeEmergenteDatos')


<form method = "POST" action = "{{route('RequerimientoBS.Empleado.update')}}" id="frmrepo" name="frmrepo"  enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepasEmpleado" id="codigoCedepasEmpleado" value="{{ $empleadoLogeado->codigoCedepas }}">
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$empleadoLogeado->codEmpleado}}">
    <input type="hidden" name="codRequerimiento" id="codRequerimiento" value="{{$requerimiento->codRequerimiento}}">
    
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
                                        <input type="text" class="form-control" readonly value="{{$requerimiento->fechaHoraEmision}}">          
                            
                            
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
                                    <option value="{{$itemproyecto->codProyecto}}" {{$itemproyecto->codProyecto==$requerimiento->codProyecto ? 'selected' : ''}}>
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
                                <input type="text" readonly class="form-control" value="{{$requerimiento->codigoCedepas}}
                                ">    
                        </div>
                        
                        <div class="w-100"></div>
                            
                        <div  class="colLabel">
                            <label for="fecha">Cta Bancaria Proveedor</label>

                        </div>
                        <div class="col">
                                <input type="text" class="form-control" name="cuentaBancariaProveedor" id="cuentaBancariaProveedor"
                                     value="{{$requerimiento->cuentaBancariaProveedor}}" placeholder="En caso de no ser BBVA, colocar CCI"> 
                        </div>


                    </div>


                </div>
                
                
                
                
            </div>


            <div class="col-md"> {{-- COLUMNA DERECHA --}}
                <div class="container">
                    

                    <div style="margin-bottom: 1%">
                        
                        <label for="fecha">Justificación de la solicitud <b id="contador" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                        <textarea class="form-control" name="justificacion" id="justificacion" aria-label="With textarea"
                             cols="3">{{$requerimiento->justificacion}}</textarea>
        
                    </div>
                    <div class="row">
                        <div  class="colLabel">
                            <label for="estado">Estado de la Solicitud 
                                @if($requerimiento->verificarEstado('Observada')){{-- Si está observada --}} <br> & Observación @endif:</label>
                        </div>
                        <div class="col"> {{-- Combo box de estado --}}
                            <textarea readonly type="text" class="form-control" name="estado" id="estado"
                            style="background-color: {{$requerimiento->getColorEstado()}} ;
                                color:{{$requerimiento->getColorLetrasEstado()}}; text-align:left;   
                                
                            "
                            readonly rows="1">{{$requerimiento->getNombreEstado()}}{{$requerimiento->getObservacionONull()}}</textarea>
                                
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
                            <input type="number" min="0" class="form-control" name="cantidad" id="cantidad">     
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
            <div class="col">
                @include('RequerimientoBS.Plantillas.Desplegables.Empleado_DescargarEliminarArchivosEmp')

            </div>   
            <div class="col">                        
                
            </div>   
            <div class="col">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">
                <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
                <input type="hidden" name="totalRendido" id="totalRendido">
                

            </div>   
           
            <div class="w-100">

            </div>
            <div class="col-md-8"></div>



             {{-- Este es para subir todos los archivos x.x  --}}
            <div class="col BordeCircular" id="divEnteroArchivo">    
                <div class="row">
                    <div class="col ">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_noSubir" value="0" checked>
                            <label class="form-check-label" for="ar_noSubir">
                                No subir archivos
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_añadir" value="1">
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
                                Subir archivos comprobantes  
                                <i class="fas fa-upload"></i>        
                            </div>       
                        </label>  

                    </div>
                </div>
            
                
            </div>    







        </div>
                

            
    </div> 
    
    <div class="col-md-12 text-center">  
        <div id="guardar">
            <div class="form-group">
                <!--
                <button class="btn btn-primary" type="submit"
                    id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando">
                    <i class='fas fa-save'></i> 
                    Registrar
                </button>   -->
                <button type="button" class="btn btn-primary" id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                    onclick="registrar()"><i class='fas fa-save'></i> 
                    Guardar Cambios
                </button>
                
                <a href="{{route('RequerimientoBS.Empleado.Listar')}}" class='btn btn-info float-left'><i class="fas fa-arrow-left"></i> Regresar al Menú</a>              
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

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

@include('Layout.EstilosPegados')

@section('script')


       {{-- PARA EL FILE  --}}
<script type="application/javascript">
    //se ejecuta cada vez que escogewmos un file

        var cont=0;
        var total=0;
        var detalleReq=[];
        
        $(window).load(function(){
            cargarDetallesRequerimiento();
            actualizarCodPresupProyecto();
            $(".loader").fadeOut("slow");
            contadorCaracteres('justificacion','contador','{{App\Configuracion::tamañoMaximoResumen}}');
        });

        function cargarDetallesRequerimiento(){
            //console.log('aaaa ' + '/listarDetallesDereposicion/'+);
            //obtenemos los detalles de una ruta GET 
            $.get('/listarDetallesDeRequerimiento/'+{{$requerimiento->codRequerimiento}}, function(data)
            {      
                listaDetalles = data;
                    for (let index = 0; index < listaDetalles.length; index++) {     
                        detalleReq.push({
                            tipo:listaDetalles[index].codUnidadMedida,
                            cantidad:listaDetalles[index].cantidad,
                            descripcion:listaDetalles[index].descripcion,         
                            codigoPresupuestal:listaDetalles[index].codigoPresupuestal
                        });  
                        
                    }
                    cont = listaDetalles.length;
                    actualizarTabla();                
            });
        }

        var listaArchivos = '';
        function registrar(){
            msje = validarFormularioEditar();
            if(msje!="")
                {
                    alerta(msje);
                    return false;
                }
            
            confirmar('¿Está seguro de guardar los cambios del requerimiento?','info','frmrepo');
            
        }
       

        function validarFormularioEditar(){
            msj='';

            limpiarEstilos(['codProyecto','justificacion','cuentaBancariaProveedor']);
            
            msj = validarSelect(msj,'codProyecto',-1,'Proyecto');
            
            msj = validarTamañoMaximoYNulidad(msj,'justificacion',{{App\Configuracion::tamañoMaximoResumen}},'Justificación');
          

            msj = validarTamañoMaximo(msj,'cuentaBancariaProveedor',{{App\Configuracion::tamañoMaximoNroCuentaBanco}},'Cuenta Bancaria del proveedor');
            
            msj = validarCantidadMaximaYNulidadDetalles(msj,'cantElementos',{{App\Configuracion::valorMaximoNroItem}});

            //msj = validarNulidad(msj,'nombresArchivos','Archivos');


            
            seleccionadoAñadirArchivos = document.getElementById('ar_añadir').checked;
            seleccionadoSobrescribirArchivos = document.getElementById('ar_sobrescribir').checked;
            
            if(seleccionadoAñadirArchivos || seleccionadoSobrescribirArchivos){
                //ahora sí validamos si se ha ingresado algun archivo
                if( document.getElementById('nombresArchivos').value =="")
                {    
                    msjAux="Debe seleccionar los archivos que se "
                    if(seleccionadoAñadirArchivos)
                        msjAux+="añadirán";
                    else 
                        msjAux+= "sobrescribirán";
                    
                    msj = msjAux;
                }
            }
            
            
            //validamos que todos los items tengan el cod presupuestal correspondiente a su proyecto
            for (let index = 0; index < detalleReq.length; index++) {
                console.log('Comparando ' + index + " starst:" +detalleReq[index].codigoPresupuestal.startsWith(codPresupProyecto) )
                msj = validarCodigoPresupuestal(msj,"colCodigoPresupuestal"+index, codPresupProyecto,"Código presupuestal del Ítem N°" + (index+1));
            }
            if( document.getElementById('nombresArchivos').value !=""){
                if(!seleccionadoAñadirArchivos && !seleccionadoSobrescribirArchivos){
                    msj = "Seleccione la modalidad con la que se subirán los archivos.";
                }
            }
            
            
            return msj;
        }
    
    </script>
     
    

    @include('RequerimientoBS.Plantillas.EditCreateReqBS')







@endsection
