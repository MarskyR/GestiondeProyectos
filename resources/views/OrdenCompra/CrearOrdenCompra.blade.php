@extends('Layout.Plantilla')

@section('titulo')
  Registrar Orden de Compra
@endsection

@section('tiempoEspera')
<div class="loader" id="pantallaCarga"></div>
@endsection

@section('contenido')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div >
    <p class="h1" style="text-align: center">Registrar Orden de Compra</p>
</div>


<form method = "POST" action = "{{route('OrdenCompra.Empleado.Guardar')}}" id="frmrepo" name="frmrepo"  enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepasEmpleado" id="codigoCedepasEmpleado" value="{{ $empleado->codigoCedepas }}">
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$empleado->codEmpleado}}">
    
    @csrf
    <div class="row">           
        <div class="col-md"> {{-- COLUMNA IZQUIERDA 1 --}}
            <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                <div class="row">
                    <div  class="colLabel">
                            <label for="fecha">Fecha emisión:</label>
                    </div>
                    <div class="col">                
                            <div class="input-group date form_date"  data-date-format="dd/mm/yyyy" data-provide="datepicker">
                                <input type="text"  class="form-control" name="fechaHoy" id="fechaHoy" disabled
                                    value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" >     
                            </div>
                    </div>

                     
                    <div  class="colLabel2">
                            <label for="" id="">Sede:</label>

                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <input type="text" readonly class="form-control" value="{{$empleado->getSedeContadorOAdministrador()->nombre}}">  
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="ComboBoxProyecto" id="lvlProyecto">Proyecto:</label>

                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <select class="form-control"  id="codProyecto" name="codProyecto" 
                                onchange="actualizarCodPresupProyecto()" >
                            <option value="-1">Seleccionar</option>
                            @foreach($proyectos as $itemproyecto)
                            <option value="{{$itemproyecto->codProyecto}}">
                                [{{$itemproyecto->codigoPresupuestal}}]
                                {{$itemproyecto->nombre}}
                            </option>
                            @endforeach
                        </select>   
                    </div>


                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel">
                            <label for="fecha">Moneda:</label>

                    </div>

                    <div class="col">
                        <select class="form-control"  id="codMoneda" name="codMoneda" >
                            <option value="-1">Seleccionar</option>
                            @foreach($monedas as $itemmoneda)
                            <option value="{{$itemmoneda->codMoneda}}">{{$itemmoneda->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    

 
                    <div  class="colLabel2">
                        <label for="fecha">Cod Orden:</label>

                    </div>
                    <div class="col">
                            <input type="text" readonly class="form-control" 
                            value="{{App\OrdenCompra::calcularCodigoCedepasLibre($objNumeracion)}}">    
                    </div>
                    
                    
                    <div class="w-100"></div>
                    <div  class="colLabel">
                        <label for="fecha">Código Presupuestal:</label>

                    </div>
                    <div class="col">
                            <input type="text" class="form-control" name="partidaPresupuestal" id="partidaPresupuestal" value="">    
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}

                    <div class="colLabel">
                        <label for="fecha">Observación:
                            <b id="contadorObservacion" style="color: rgba(0, 0, 0, 0.548)"></b>
                        </label>
                    </div>
                    <div class="col">
                        <textarea class="form-control" name="observacion" id="observacion" aria-label="With textarea" rows="2"
                        ></textarea>
                    </div>

                </div>


            </div>
            
            
            
            
        </div>


        <div class="col-md"> {{-- COLUMNA DERECHA --}}
            <div class="container">
                <div class="row">
                    <div  class="colLabel">
                        <label for="" id="">RUC:
                            <b id="contadorRUC" style="color: rgba(0, 0, 0, 0.548)"></b>
                             
                        </label>
                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        
                        <div class="row">
                            <div class="col-8">
                                <input type="number" class="form-control" name="ruc" id="ruc" value=""> 
                        

                            </div>
                            <div class="col">
                                <button type="button" title="Buscar por RUC en la base de datos de Sunat" class="btn btn-info " id="botonBuscarPorRUC" onclick="consultarPorRuc()" >
                                    <i class="fas fa-search"></i>
                                    Buscar
                                </button>

                            </div>
                        </div>
                        
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}

                    <div  class="colLabel">
                        <label for="" id="">Señores:</label>
                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <input type="text" class="form-control" name="señores" id="señores" value=""> 
                    </div>



                    

                    <div class="w-100"></div> {{-- SALTO LINEA --}}

                    <div  class="colLabel">
                        <label for="" id="">Dirección:</label>
                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <input type="text" class="form-control" name="direccion" id="direccion" value=""> 
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}

                    <div  class="colLabel">
                        <label for="" id="">Atención:</label>
                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <input type="text" class="form-control" name="atencion" id="atencion" value=""> 
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}

                    <div  class="colLabel">
                        <label for="" id="">Referencia:</label>
                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <input type="text" class="form-control" name="referencia" id="referencia" value=""> 
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
                    <th class="text-center">
                        <div  >  
                            <input type="number" min="0" class="form-control" name="cantidadDetalle" id="cantidadDetalle" value="1" onchange="cambioValorInput()">     
                        </div>
                    </th>
                    <th class="text-center">
                        <div  >
                            <select class="form-control"  id="comboUnidadMedida" name="comboUnidadMedida" >
                                <option value="-1">Seleccionar</option>
                                @foreach($unidades as $itemunidad)
                                <option value="{{$itemunidad->codUnidadMedida}}">{{$itemunidad->nombre}}</option>
                                @endforeach
                            </select>    
                        </div>
                    </th>
                    <th class="text-center">
                        <div  >
                            <input type="text" class="form-control" name="descripcionDetalle" id="descripcionDetalle">     
                        </div>
                    </th>                                          
                    <th class="text-center">
                        <div class="input-group" onclick="clickDiv(1)">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <input type="radio" name="tipoValorGroup" id="valorVentaRadio" onchange="cambioTipoValorRadio()" checked>
                              </div>
                            </div>
                            <input type="number" min="0" class="form-control" aria-label="Text input with radio button" name="valorVentaDetalle" id="valorVentaDetalle" value="0" onchange="cambioValorInput()">
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="input-group" onclick="clickDiv(2)">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <input type="radio" name="tipoValorGroup" id="precioVentaRadio" onchange="cambioTipoValorRadio()">
                              </div>
                            </div>
                            <input type="number" min="0" class="form-control" aria-label="Text input with radio button" name="precioVentaDetalle" id="precioVentaDetalle" value="0" readonly onchange="cambioValorInput()">
                        </div>
                    </th>
                    <th class="text-center">
                        <div  >
                            <input type="number" min="0" class="form-control" name="totalDetalle" id="totalDetalle" readonly>     
                        </div>
                    </th>
                    <th class="text-center">
                      
                        <input class="CBgrande" type="checkbox" name="exoneradoIGV" id="exoneradoIGV" onchange="cambioEstadoIGV()"  >
                       
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
                    <th width="10%" class="text-center">Cantidad</th>                                        
                    <th width="13%">Unidad Medida</th>                                 
                    <th>Descripcion</th>
                    <th width="12%" class="text-center">Valor Venta</th>
                    <th width="12%" class="text-center">Precio Venta</th>
                    <th width="12%" class="text-center">Total</th>
                    <th width="5%" class="text-center">¿IGV?</th>

                    <th width="8%" class="text-center">Opciones</th>                                            
                </thead>
                <tfoot></tfoot>
                <tbody></tbody>
            </table>
        </div> 


        
            

        <div class="row" id="divTotal" name="divTotal">                       
            <div class="col-md-8">
            </div>   
            <div class="col-md-2">                        
                <label for="">Importe Total: </label>    
            </div>   
            <div class="col-md-2">
                {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                <input type="hidden" name="cantElementos" id="cantElementos">
                <!--
                <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
                <input type="hidden" name="totalRendido" id="totalRendido">                              
                -->
                <input type="text" class="form-control text-right" name="totalMostrado" id="totalMostrado" readonly>   
                <input type="hidden" class="form-control text-right" name="total" id="total" readonly>   
            </div>   

            <div class="w-100">

            </div>
            <div class="col-md-8"></div>


{{-- 
            Este es para subir todos los archivos x.x   --}}
            <div class="col" id="divEnteroArchivo">            
                <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
                <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                        style="display: none" onchange="cambio()">  
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
        <div class="col">

            <a href="{{route('OrdenCompra.Empleado.Listar')}}" class='btn btn-info '>
                <i class="fas fa-arrow-left"></i> 
                Regresar al Menú
            </a>  

        </div>
        <div class="col">


        </div>
        <div class="col">

            <button type="button" class="btn btn-primary" id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                onclick="registrar()">
                <i class='fas fa-save'></i> 
                Guardar
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


	<!-- INI API GOOGLE MAPS -->
	{{-- <script src="https://jslibs1.sunat.gob.pe/sunatrecaptcha/3.0.2/sunatrecaptcha3.js"></script> --}}
	<!-- FIN API GOOGLE MAPS -->
	
       {{-- PARA EL FILE  --}}
<script type="application/javascript">
    //se ejecuta cada vez que escogewmos un file
        var codPresupProyecto = -1;
        
        
        $(document).ready(function(){
            $(".loader").fadeOut("slow");

            contadorCaracteres('ruc','contadorRUC',11);
            contadorCaracteres('observacion','contadorObservacion',{{App\Configuracion::tamañoObservacionOC}});
            
        });
        
        function registrar(){
            msje = validarFormularioCrear();
            if(msje!="")
                {
                    alerta(msje);
                    return false;
                }
            
            confirmar('¿Está seguro de crear la orden de compra?','info','frmrepo');
            
        }

        /* llama a mi api que se conecta  con la api de la sunat
            si encuentra, llena con los datos que encontró
            si no tira mensaje de error
        */
        function consultarPorRuc(){

            msjError="";
            ruc=$("#ruc").val();    
            if(ruc=='')
                msjError=("Por favor ingrese el ruc");  
            
            
            if(ruc.length!=11)
                msjError=("Por favor ingrese el ruc completo. Solo 11 digitos.");  
            

            if(msjError!=""){
                alerta(msjError);
                return;
            }

            $(".loader").show();//para mostrar la pantalla de carga

            $.get('/ConsultarAPISunat/ruc/'+ruc,
            function(data)
            {     
                console.log("IMPRIMIENDO DATA como llegó:");
                console.log(data);
                
                if(data==1){
                    alerta("Persona juridica no encontrada.");   

                }else{
                    console.log('DATA PARSEADA A JSON:')
                    personaJuridicaEncontrada = JSON.parse(data)
                    console.log(personaJuridicaEncontrada);
                

                    document.getElementById('señores').value = personaJuridicaEncontrada.razonSocial;

                    
                    //document.getElementById('actividadPrincipal').value = personaJuridicaEncontrada.actEconomicas;
                    document.getElementById('direccion').value = personaJuridicaEncontrada.direccion;

                }
             
                $(".loader").fadeOut("slow");
            }
            );
        }

/* 
        function generarNuevoToken(){

            grecaptcha.execute(site_key_sunat, {action: 'consultaRUC01'}).then(function(token) {
               console.log('hola token=' + token);
                
            });
            
        }
             */
    	
        

    </script>
     


@endsection
@include('OrdenCompra.CrearEditarOrdenJS')