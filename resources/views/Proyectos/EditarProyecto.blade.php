@extends('Layout.Plantilla')

@section('titulo')
    @if($proyecto->sePuedeEditar())
        Editar
    @else
        Ver
    @endif
     proyecto
@endsection
@section('estilos')

<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">
    {{-- 
    ESTA VISTA SIRVE TANTO PARA 
        El gerente vea el proyecto
        la UGE edite el proyecto (si está en registro)
        la uge vea el proyecto (si esta en ejecucion o finalizado)
    
    --}}
    @php
        if($proyecto->sePuedeEditar()){
            $readonly="";
            $disabled = "";
            $returnFalse = '';
        }
        else{ 
            $readonly="readonly";
            $disabled = 'disabled';
            $returnFalse = 'return false;';
        }
    @endphp
    <style>
        .cuadroCircularPlomo1{
            background-color: rgb(175, 175, 175);
            -moz-border-radius: 7px;
            -webkit-border-radius: 7px;
            /* margin: 0.5%; */
        }

        .cuadroCircularPlomo2{
            background-color: rgba(230, 230, 230, 0.548);
            -moz-border-radius: 7px;
            -webkit-border-radius: 7px;
            /* margin: 0.5%; */
        }
        .col{
            /* background-color: orange; */
            margin-top: 15px;
            padding-bottom: 5px;
            margin: 0.5%;
        }
        
        /* 
            CODIGO OBTENIDO DE
            https://www.it-swarm-es.com/es/html/tabla-con-encabezado-fijo-y-columna-fija-en-css-puro/1072563817/
        */
        .divTablaFijada { /* Este se pone al div table */
            max-width: 100%;
            max-height: 600px;
            overflow: scroll;
        }

        /* ESTE ES EL QUE FIJA LA ROW */
        .filaFijada { 
            position: -webkit-sticky; /* for Safari */
            position: sticky;
            top: 0;
        }
        .fondoAzul{
            background-color:#3c8dbc;
        }

        .letrasBlancas{
            color: #fff;
        }

        .vaAGirar {
            -moz-transition: all 0.25s ease-out;
            -ms-transition: all 0.25s ease-out;
            -o-transition: all 0.25s ease-out;
            -webkit-transition: all 0.25s ease-out;
        }
    
        .rotado {
            -moz-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            -webkit-transform: rotate(90deg);
        }
    

    </style>

@endsection

@section('contenido')



<form id="frmUpdateInfoProyecto" name="frmUpdateInfoProyecto" role="form" action="{{route('GestiónProyectos.update')}}" 
    class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
    <input type="hidden" name="codProyecto" id="codProyecto" value="{{$proyecto->codProyecto}}">

    @csrf 

    @include('Layout.MensajeEmergenteDatos')


    <div class="well">
        <H3 style="text-align: center;">
            @if($proyecto->sePuedeEditar())
                Editar
            @else
                Ver
            @endif
        Proyecto
        </H3>
    </div>
    
    <br>

    <div class="row">
        
        

        <div class="col" style="">
            
            <div class="row">

                <div class="col-3 m-2">
                    <label class="" style="">Nombre del Proyecto:</label>
                    <div class="">
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                            value="{{-- {{$proyecto->nombre}} --}}" placeholder="Nombre..." {{$readonly}} >
                    </div>
                    
                </div>

                <div class="col">
                    
                    <label class="" style="">Cod presupuestal:</label>
                    <input type="text" class="form-control" id="codigoPresupuestal" name="codigoPresupuestal"
                    value="{{-- {{$proyecto->codigoPresupuestal}} --}}"  placeholder="..."  {{$readonly}} >
                </div>
                

                <div class="col">
                    <label class="" style="">Sede Principal:</label>
                    <div class="">
                        <select class="form-control" name="codSede" id="codSede"  {{$disabled}} >
                            {{-- <option value="-1">-- Seleccionar --</option>
                            @foreach($listaSedes as $itemsede)
                            <option value="{{$itemsede->codSede}}"
                                @if($itemsede->codSede == $proyecto->codSedePrincipal)
                                    selected
                                @endif
                                >{{$itemsede->nombre}}</option>    
                            @endforeach --}}
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="">Estado del Proyecto</label>
                    <input type="text" class="form-control" value="{{-- {{$proyecto->getEstado()->nombre}} --}}" readonly
                         title="Esto solo puede ser cambiado por el administrador." style="background-color: bisque">
                </div>
                <div class="col">
                    <label for="">Gerente:</label>
                    <input type="text" class="form-control" value="{{-- {{$proyecto->getGerente()->getNombreCompleto()}} --}}" readonly
                         title="" style="background-color: bisque">
                </div>
                
                <div class="w-100"></div>

                <div class="col">
                    <label class="" style="">
                        {{-- Financiera: --}}
                        <button type="button" class="btn btn-success btn-xs" 
                            data-toggle="modal" {{-- data-target="#ModalContactoFinanciera" --}}>
                             
                            Financiera
                            <i class="fas fa-info-circle "></i>
                        </button>
                    </label>
                    <div class="">
                        <select class="form-control" name="codEntidadFinanciera" id="codEntidadFinanciera"   {{$disabled}} >
                            <option value="-1">-- Seleccionar --</option>
{{--                             @foreach($listaFinancieras as $itemFinanciera)
                            <option value="{{$itemFinanciera->codEntidadFinanciera}}"
                                @if($itemFinanciera->codEntidadFinanciera == $proyecto->codEntidadFinanciera )
                                    selected
                                @endif
                                >{{$itemFinanciera->nombre}}</option>    
                            @endforeach --}}
                        </select>


                    </div>
                   
                </div>
              


                <div class="col">
                    <label for="">Fecha de Inicio: </label>

                    @if($proyecto->sePuedeEditar())
                        
                        <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker"  
                            style=" ">
                            <input type="text"  class="form-control" name="fechaInicio" id="fechaInicio" 
                                    value="{{-- {{$proyecto->getFechaInicio()}} --}}" style="text-align:center;"  {{$readonly}} >

                                    <div class="input-group-btn">                                        
                                <button class="btn btn-primary date-set" type="button" style="display:none" >
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </div>
                        </div>
                    
                    @else
                        <input type="text"  class="form-control" name="fechaInicio" id="fechaInicio" 
                            value="{{-- {{$proyecto->getFechaInicio()}} --}}" style="text-align:center;"  {{$readonly}} >

                    @endif
                    
                </div>


                <div class="col">
                    <label for="">Fecha de Finalización: </label>
                    
                    @if($proyecto->sePuedeEditar())
                        <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker">
                            <input type="text"  class="form-control" name="fechaFinalizacion" id="fechaFinalizacion" 
                                    value="{{-- {{$proyecto->getFechaFinalizacion()}} --}}" style="text-align:center;"  {{$readonly}} >

                            <div class="input-group-btn">        
                                <button class="btn btn-primary date-set" type="button" style="display:none">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </div>
                        </div>


                    @else
                        <input type="text"  class="form-control" name="fechaFinalizacion" id="fechaFinalizacion" 
                            value="{{-- {{$proyecto->getFechaFinalizacion()}} --}}" style="text-align:center;"  {{$readonly}} >

                    @endif
                    



                </div>

                
                <div class="col">
                    <label for="">Duración en meses </label>
                
                    <input type="text"  class="form-control" name="duracionMeses" id="duracionMeses" 
                            value="{{-- {{$proyecto->getDuracion('meses',false)}} --}}" style="text-align:center;" readonly >

                    <div class="input-group-btn">                                        
                        <button class="btn btn-primary date-set" type="button" style="display:none">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </div>
                </div>


                <div class="col">
                    <label for="">Tipo financiamiento</label>
                    


                    <select class="form-control" id="codTipoFinanciamiento" name="codTipoFinanciamiento"  {{$disabled}} >
                        <option value="-1">- Tipo Financiamiento -</option>          
                        @foreach($listaTipoFinanciamiento as $itemTipoFinanciamiento)
                            <option value="{{-- {{$itemTipoFinanciamiento->codTipoFinanciamiento}} --}}" 
                                {{-- {{$itemTipoFinanciamiento->codTipoFinanciamiento==$proyecto->codTipoFinanciamiento ? 'selected':''}} --}}>
                                {{-- {{$itemTipoFinanciamiento->nombre}} --}}
                            </option>                                 
                        @endforeach
                    </select>
                </div>

                <div class="col">
                    <label for="">Moneda del Proyecto</label>
                    <select class="form-control" id="codMoneda" name="codMoneda"  {{$disabled}} >
                        <option value="-1">- Seleccione Moneda -</option>          
                        @foreach($listaMonedas as $itemMoneda)
                            <option value="{{-- {{$itemMoneda->codMoneda}} --}}" 
                                {{$itemMoneda->codMoneda==$proyecto->codMoneda ? 'selected':''}}>
                                {{$itemMoneda->nombre}}
                            </option>                                 
                        @endforeach
                    </select>

                </div>
                <div class="w-100"></div>
                

                <div class="w-100"></div>
                <div class="col">
                    <label class="" style="">Nombre Completo:</label>
                    <div class="">
                        <textarea class="form-control" name="nombreLargo" id="nombreLargo" cols="30" rows="2"  {{$readonly}} 
                        >{{-- {{$proyecto->nombreLargo}} --}}</textarea>
                    </div>
                </div>

                <div class="col">
                    <label class="" style="">Objetivo General:</label>
                    <div class="">
                        <textarea class="form-control" name="objetivoGeneral" id="objetivoGeneral" cols="30" rows="2"  {{$readonly}} 
                        >{{-- {{$proyecto->objetivoGeneral}} --}}</textarea>
                    </div>
                </div>
                

                <div class="w-100"></div>
                
                

                
                <div class="col cuadroCircularPlomo2">
                    <label for="">Cptda Cedepas</label>
                    <input class="form-control text-right" type="number" min="0" name="importeContrapartidaCedepas" id="importeContrapartidaCedepas"  {{$readonly}} 
                            value="{{-- {{$proyecto->importeContrapartidaCedepas}} --}}" onchange="actualizarPresupuestoTotal()">

                </div>
                
                <div class="col cuadroCircularPlomo2">
                    <label for="">Cptda Pob. Beneficiaria</label>
                    <input class="form-control text-right" type="number" min="0" name="importeContrapartidaPoblacionBeneficiaria" id="importeContrapartidaPoblacionBeneficiaria"   {{$readonly}} 
                        value="{{-- {{$proyecto->importeContrapartidaPoblacionBeneficiaria}} --}}" onchange="actualizarPresupuestoTotal()">

                </div>
                
                <div class="col cuadroCircularPlomo2">
                    <label for="">Cptda Otros</label>
                    <input class="form-control text-right" type="number" min="0" name="importeContrapartidaOtros" id="importeContrapartidaOtros"  {{$readonly}} 
                        value="{{-- {{$proyecto->importeContrapartidaOtros}} --}}" onchange="actualizarPresupuestoTotal()">

                </div>
                <div class="col cuadroCircularPlomo2">
                    <label for="">Importe Financiamiento</label>
                    <input class="form-control text-right" type="number" min="0" name="importeFinanciamiento" id="importeFinanciamiento"  {{$readonly}} 
                        value="{{-- {{$proyecto->importeFinanciamiento}} --}}" onchange="actualizarPresupuestoTotal()">

                </div>
                
                <div class="col cuadroCircularPlomo1">
                    <label for="">Presupuesto Total</label>
                    <input class="form-control text-right" type="text" readonly name="importePresupuestoTotal" id="importePresupuestoTotal"   
                        value="{{-- {{$proyecto->importePresupuestoTotal}} --}}">

                    
                </div>
                
                <div class="col">
                    <label for="">Reporte del proyecto</label>
                    
                    <a class="btn btn-primary" href="{{route("GestionProyectos.ExportarModeloMarcoLogico",$proyecto->codProyecto)}}">
                        <i class="fas fa-download"></i>
                       
                        Descargar 
         
                    </a>
                </div>
                <div class="w-100"></div>
                
                    






                <br>
                <div class="col" style=" text-align:center">
                    
                    @if($proyecto->sePuedeEditar())
                        <button type="button" class="btn btn-primary float-right" style="margin-left: 6px" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando"
                            onclick="clickActualizar()">
                            <i class='fas fa-save'></i> 
                            Guardar
                        </button> 
                        
                    @endif
                    
                        
                    





                    @php
                        if(App\Empleado::getEmpleadoLogeado()->esUge())
                            $rutaMenu = 'GestiónProyectos.UGE.Listar';
                        else
                            $rutaMenu = 'GestiónProyectos.Gerente.Listar';
                    @endphp
                    
                    <a href="{{route($rutaMenu)}}" class='btn btn-info float-left'>
                        <i class="fas fa-arrow-left"></i> 
                        Regresar al Menu
                    </a>
                    


                </div>

            </div>

        </div>
        


    </div>



</form>

    <div class="row">
        <div class="col">

            @include('Proyectos.Desplegables.LugaresEjecucion')
            
            @include('Proyectos.Desplegables.PorcentajesObjetivos')
        </div>

        <div class="col">
            
            @include('Proyectos.Desplegables.PoblacionBeneficiaria')

            @include('Proyectos.Desplegables.DesplegableArchivosProyecto')
            
        </div>
        
    </div>

    <div class="row">
        <div class="col">
             
            @include('Proyectos.Desplegables.ObjetivoEspecifico')
            
            
            @include('Proyectos.Desplegables.ResultadoEsperadoIndicadores')
            
           
            @include('Proyectos.Desplegables.ResultadoEsperadoActividades')
            

        </div>
        <div class="w-100"></div>
        <div class="col text-right">
            <a href="{{route('GestionProyectos.RedirigirAVistaMetas',$proyecto->codProyecto)}}" class="btn btn-success">
                <i class="fas fa-bullseye"></i>
                Ver ejecución de Metas
            </a>
        </div>

    </div>
 

    {{-- MODAL DE DATOS FINANCIERA --}}

    
    <div class="modal fade" id="ModalContactoFinanciera" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalContactoFinanciera">Datos de contacto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" action="{{route('GestiónProyectos.UGE.ActualizarContacto',$proyecto->codProyecto)}}" id="formContactoFinanciera" name="formContactoFinanciera">
                            @csrf
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codProyecto" value="{{$proyecto->codProyecto}}" >
                            <div class="row">
 
                                <div class="col">
                                
                                    <label for="">Nombre del contacto</label>
                                    <input type="text" class="form-control" id="contacto_nombres" name="contacto_nombres" value="{{$proyecto->contacto_nombres}}" {{$readonly}}>
                                </div>
                                
                                <div class="w-100"></div>
                                <div class="col">
                                    <label for="">Teléfono</label>
                                    <input type="text" class="form-control" id="contacto_telefono" name="contacto_telefono" placeholder="Añadir código País (+51)"  {{$readonly}}
                                        value="{{$proyecto->contacto_telefono}}">
                                </div>
                          
                                <div class="w-100"></div>
                                <div class="col">
                                    <label for="">E-mail</label>
                                    <input type="text" class="form-control" placeholder="correoejemplo@gmail.com" id="contacto_correo" {{$readonly}}
                                        name="contacto_correo" value="{{$proyecto->contacto_correo}}">
                                </div>
                               
                              </div>


                        </form>
                      

                    </div>
                    <div class="modal-footer">
                        @if($proyecto->sePuedeEditar())
            
                            <button type="button" class="btn btn-primary"  onclick="clickGuardarContacto()">
                                Guardar
                            </button>
                        @endif

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>
 
                    </div>
                
            </div>
        </div>
    </div>


    {{-- MODALES QUE SE USAN EN DISTINTOS DESPLEGABLES
        (tengo k ponerlos aqui porque si lo pongo en algun desplegable y otro lo usa, no lo lee)
        --}}

    <div class="modal fade" id="ModalAgregarResultadoEsperado" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                

                    <div class="modal-header">
                        <h5 class="modal-title" id="">Agregar Resultado Esperado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            
                        <form id="frmAgregarResultadoEsperado" name="frmAgregarResultadoEsperado" action="{{route('GestionProyectos.agregarEditarResultadoEsperado')}}" method="POST">
                           
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codResultadoEsperado" id="codResultadoEsperado" value="0">
                                
                            @csrf
                            <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
            
                            <label for="">Nuevo Resultado Esperado</label>
                            <textarea class="form-control" name="descripcionNuevoResultado" id="descripcionNuevoResultado" cols="15" rows="5" placeholder="Escriba aquí el nuevo resultado esperado..."
                            ></textarea>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>

                        <button type="button" class="m-1 btn btn-primary" onclick="clickGuardarNuevoResEsp()">
                            Guardar <i class="fas fa-save"></i>
                        </button>   
                    </div>
                
            </div>
        </div>
    </div>
    
@endsection

@section('script')  
        @include('Layout.ValidatorJS')
    
     <script>   
        listaPEIs = [];
        poblacionesBeneficiarias = [];
        porcentajesObjetivosEstrategicos = [];
        objetivosEspecificos = [];
        indicadoresObjetivos = [];
        resultadosEsperados = [];
            indicadoresResultados = [];
            mediosVerificacion=[];

            actividadesResultados=[];
            indicadoresActividades = [];
        //estos vectores se cargan con la llamada http get al cargador de datos del proyecto

        $(document).ready(function(){
            recargarTodosLosInvocables();
        });

        function recargarTodosLosInvocables(){
            actualizarResultadosEsperadosActividades();
            actualizarObjetivosEspecificos();
            actualizarResultadosEsperadosIndicadores();
            actualizarPoblacionesBeneficiarias();
            recargarTablaObjetivosEstrategicos();
            recargarLugaresEjecucion();

            recargarDatosProyecto();

        }

        function actualizarObjetivosEspecificos(){
             
            invocarHtmlEnID("{{route('GestionProyectos.InvocarDesplegable.ObjetivosEspecificos',$proyecto->codProyecto)}}",'collapseObjEspec')
        }

        function actualizarResultadosEsperadosActividades(){
             
             invocarHtmlEnID("{{route('GestionProyectos.InvocarDesplegable.ResultadosEsperadosActividades',$proyecto->codProyecto)}}",'collapseResAct')
         }
 
         function actualizarResultadosEsperadosIndicadores(){
             invocarHtmlEnID("{{route('GestionProyectos.InvocarDesplegable.InvocarResultadosEsperadosIndicadores',$proyecto->codProyecto)}}",'collapseResEsp')
         }
 
         function actualizarPoblacionesBeneficiarias(){
            invocarHtmlEnID("{{route('GestionProyectos.InvocarDesplegable.InvocarPoblacionesBeneficiarias',$proyecto->codProyecto)}}",'collapsePobBen')
         }
          
         function recargarTablaObjetivosEstrategicos(){
            invocarHtmlEnID("{{route('GestionProyectos.InvocarDesplegable.InvocarTablaObjEstr',$proyecto->codProyecto)}}",'contenedorTablaObjEstr')
         }


         function recargarLugaresEjecucion(){
            invocarHtmlEnID("{{route('GestionProyectos.InvocarLugaresEjecucion',$proyecto->codProyecto)}}",'contenedorTablaLugares')
         }


         



        function recargarDatosProyecto(){
            ruta = "/GestionProyectos/{{$proyecto->codProyecto}}/cargarMaestrosDetalle";
            
            $.get(ruta, function(dataRecibida,statusCode){
                //console.log('DATA RECIBIDA:');
                //console.log(dataRecibida);
                //console.log('statusCode:' + statusCode);
                objetoMaestro = JSON.parse(dataRecibida);
                objetivosEspecificos = objetoMaestro.objetivosEspecificos;
                indicadoresObjetivos = objetoMaestro.indicadoresObjetivos;
                resultadosEsperados = objetoMaestro.resultadosEsperados;
                indicadoresResultados = objetoMaestro.indicadoresResultados;
                mediosVerificacion = objetoMaestro.mediosVerificacion ;
                actividadesResultados = objetoMaestro.actividadesResultados;
                indicadoresActividades = objetoMaestro.indicadoresActividades;
                poblacionesBeneficiarias = objetoMaestro.poblacionesBeneficiarias;
                porcentajesObjetivosEstrategicos = objetoMaestro.porcentajesObjetivosEstrategicos;

                listaPEIs = objetoMaestro.listaPEIs;
            }).done(function(){
                mensaje = "ÉXITO: GET de " + ruta;
            }).fail(function(){
                mensaje = "Falló: GET de " + ruta;
            }).always(function(dataRecibida,statusCode,xhr){
                //console.log(xhr.status)
                console.log('Datos del proyecto Actualizados.');
            });
        }

        
        function clickActualizar(){
            msjError = validarActualizacion();
            if(msjError!="")
            {
                alerta(msjError);
                return;
            }

            confirmarConMensaje("Confirmacion","¿Desea actualizar la información del proyecto?","warning",submitearActualizacionInfoProyecto);
        }

        function submitearActualizacionInfoProyecto(){
            document.frmUpdateInfoProyecto.submit(); // enviamos el formulario	
        }

        function validarActualizacion(){
            limpiarEstilos([
                'nombre',
                'codigoPresupuestal',
                'codSede',
                'codEntidadFinanciera',
                'fechaInicio',
                'fechaFinalizacion',
                'codTipoFinanciamiento',
                'codMoneda',
                'nombreLargo',
                'objetivoGeneral',
                'importePresupuestoTotal',
                'importeContrapartidaCedepas',
                'importeContrapartidaPoblacionBeneficiaria',
                'importeContrapartidaOtros',
                'importeFinanciamiento'
                ]);

            msjError ="";
            
            msjError = validarTamañoMaximoYNulidad(msjError,'nombre',200,'Nombre');
            msjError = validarTamañoExacto(msjError,'codigoPresupuestal',2,'Código Presupuestal');

            msjError = validarSelect(msjError,'codSede',"-1",'Sede');
            msjError = validarSelect(msjError,'codEntidadFinanciera',"-1",'Entidad financiera');
            
            msjError = validarNulidad(msjError,'fechaInicio',"Fecha de inicio del proyecto");
            msjError = validarNulidad(msjError,'fechaFinalizacion',"Fecha de finalización del proyecto");
              
            msjError = validarSelect(msjError,'codTipoFinanciamiento',"-1",'Tipo de financiamiento');
            msjError = validarSelect(msjError,'codMoneda',"-1",'Moneda');
              
            msjError = validarTamañoMaximoYNulidad(msjError,'nombreLargo',300,'Nombre completo del proyecto');
            
            
            msjError = validarTamañoMaximoYNulidad(msjError,'objetivoGeneral',500,'Objetivo general del proyecto');
            
            msjError = validarNoNegatividadYNulidad(msjError,'importePresupuestoTotal','Importe de financiamiento');
            msjError = validarNoNegatividadYNulidad(msjError,'importeContrapartidaCedepas','Importe de contrapartida CEDEPAS');
            msjError = validarNoNegatividadYNulidad(msjError,'importeContrapartidaPoblacionBeneficiaria','Importe de contrapartida de población beneficiaria');
            msjError = validarNoNegatividadYNulidad(msjError,'importeContrapartidaOtros','Importe de contrapartida otros');
            msjError = validarNoNegatividadYNulidad(msjError,'importeFinanciamiento','Importe total de financiamiento');

            return msjError;

        }
         

        
        
        const objImportePresupuestoTotal = document.getElementById('importePresupuestoTotal');
        const objImporteContrapartidaCedepas = document.getElementById('importeContrapartidaCedepas');
        const objImporteContrapartidaPoblacionBeneficiaria = document.getElementById('importeContrapartidaPoblacionBeneficiaria');
        const objImporteContrapartidaOtros = document.getElementById('importeContrapartidaOtros');
        const objImporteFinanciamiento = document.getElementById('importeFinanciamiento'); 


        function actualizarPresupuestoTotal(){
            total = 
                parseFloat(objImporteContrapartidaCedepas.value) + 
                parseFloat(objImporteContrapartidaPoblacionBeneficiaria.value) + 
                parseFloat(objImporteContrapartidaOtros.value) + 
                parseFloat(objImporteFinanciamiento.value);
        
            total=   Math.round(total * 100) / 100
            objImportePresupuestoTotal.value = total;

        }

        function clickGuardarContacto(){
            msj = validarFormContacto();
            if(msj!=""){
                
                alerta(msj);
                return;
            }

            document.formContactoFinanciera.submit();
        }



        function validarFormContacto(){
            msjError = "";
            limpiarEstilos(['contacto_nombres','contacto_correo','contacto_telefono']);
             
            msjError = validarTamañoMaximoYNulidad(msjError,'contacto_nombres',200,'Nombre del contacto');
            msjError = validarTamañoMaximoYNulidad(msjError,'contacto_correo',200,'Correo del contacto');
            msjError = validarTamañoMaximoYNulidad(msjError,'contacto_telefono',200,'Nro de Teléfono del contacto');
            
            return msjError;

        }
     
    </script>
@endsection

