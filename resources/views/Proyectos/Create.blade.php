@extends('Layout.Plantilla')

@section('titulo')
    Crear Proyecto
@endsection
@section('estilos')
<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">
@endsection

@section('contenido')

    

<form id="frmUpdateInfoProyecto" name="frmUpdateInfoProyecto" role="form" action="{{route('GestiónProyectos.store')}}" 
class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">

    @csrf 

    @include('Layout.MensajeEmergenteDatos')


    <div class="well">
        <H3 style="text-align: center;">
            CREAR PROYECTO
        </H3>
    </div>

    <br>

    <div class="row">
        
        

        <div class="col" style="">
            
            <div class="row">

                <div class="col">
                    <label class="" style="">Nombre del Proyecto (corto):</label>
                    <div class="">
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                            value="" placeholder="Nombre..." >
                    </div>

                </div>
                
                


                <div class="col">
                    
                    <label class="" style="">Codigo presupuestal:</label>
                    <input type="text" class="form-control" id="codigoPresupuestal" name="codigoPresupuestal"
                    value=""  placeholder="..." >
                </div>
                

                <div class="col">
                    <label class="" style="">Sede Principal:</label>
                    <div class="">
                        <select class="form-control" name="codSede" id="codSede">
                            <option value="-1" selected>-- Seleccionar --</option>
                            @foreach($listaSedes as $itemsede)
                            <option value="{{$itemsede->codSede}}"
                                
                                >{{$itemsede->nombre}}</option>    
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="">Plan Estratégico</label>
                    <select class="form-control" name="codPEI" id="codPEI">
                        <option value="-1">-- Seleccionar --</option>
                        @foreach($listaPEIs as $itemPEI)
                        <option value="{{$itemPEI->codPEI}}"
                            >{{$itemPEI->getPeriodo()}}</option>    
                        @endforeach
                    </select>
    

                </div>
                


                <div class="w-100"></div>

                <div class="col">
                    <label class="" style="">Financiera:</label>
                    <div class="">
                        <select class="form-control" name="codEntidadFinanciera" id="codEntidadFinanciera">
                            <option value="-1" selected>-- Seleccionar --</option>
                            @foreach($listaFinancieras as $itemFinanciera)
                            <option value="{{$itemFinanciera->codEntidadFinanciera}}"
                            
                                >{{$itemFinanciera->nombre}}</option>    
                            @endforeach
                        </select>


                    </div>
                </div>



                <div class="col">
                    <label for="">Fecha de Inicio: </label>
                    <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker"  
                        style=" ">
                        <input type="text"  class="form-control" name="fechaInicio" id="fechaInicio" 
                                value="" style="text-align:center;">

                                <div class="input-group-btn">                                        
                            <button class="btn btn-primary date-set" type="button" style="display:none">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <label for="">Fecha de Finalización: </label>
                    <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker"  
                        style=" ">
                        <input type="text"  class="form-control" name="fechaFinalizacion" id="fechaFinalizacion" 
                                value="" style="text-align:center;">

                        <div class="input-group-btn">                                        
                            <button class="btn btn-primary date-set" type="button" style="display:none">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <label for="">Tipo financiamiento</label>
                    


                    <select class="form-control" id="codTipoFinanciamiento" name="codTipoFinanciamiento">
                        <option value="-1">- Tipo Financiamiento -</option>          
                        @foreach($listaTipoFinanciamiento as $itemTipoFinanciamiento)
                            <option value="{{$itemTipoFinanciamiento->codTipoFinanciamiento}}" 
                                >{{$itemTipoFinanciamiento->nombre}}
                            </option>                                 
                        @endforeach
                    </select>
                </div>

                <div class="col">
                    <label for="">Moneda del Proyecto</label>
                    <select class="form-control" id="codMoneda" name="codMoneda">
                        <option value="-1">- Seleccione Moneda -</option>          
                        @foreach($listaMonedas as $itemMoneda)
                            <option value="{{$itemMoneda->codMoneda}}"
                                >{{$itemMoneda->nombre}}
                            </option>                                 
                        @endforeach
                    </select>

                </div>
                <div class="w-100"></div>
                

                <div class="w-100"></div>
                <div class="col">
                    <label class="" style="">Nombre del proyecto (Completo):</label>
                    <div class="">
                        <textarea class="form-control" name="nombreLargo" id="nombreLargo" cols="30" rows="2"
                        ></textarea>
                    </div>
                </div>

                <div class="col">
                    <label class="" style="">Objetivo General:</label>
                    <div class="">
                        <textarea class="form-control" name="objetivoGeneral" id="objetivoGeneral" cols="30" rows="2"
                        ></textarea>
                    </div>
                </div>
                

                <div class="w-100"></div>
                
                

                
                <div class="col cuadroCircularPlomo2">
                    <label for="">Cptda Cedepas</label>
                    <input class="form-control" type="number" min="0" name="importeContrapartidaCedepas" id="importeContrapartidaCedepas"
                            value="0" onchange="actualizarPresupuestoTotal()">

                </div>
                
                <div class="col cuadroCircularPlomo2">
                    <label for="">Cptda Pob. Beneficiaria</label>
                    <input class="form-control" type="number" min="0" name="importeContrapartidaPoblacionBeneficiaria" id="importeContrapartidaPoblacionBeneficiaria"  
                        value="0" onchange="actualizarPresupuestoTotal()">

                </div>
                
                <div class="col cuadroCircularPlomo2">
                    <label for="">Cptda Otros</label>
                    <input class="form-control" type="number" min="0" name="importeContrapartidaOtros" id="importeContrapartidaOtros" 
                        value="0" onchange="actualizarPresupuestoTotal()">

                </div>
                <div class="col cuadroCircularPlomo2">
                    <label for="">Importe Financiamiento</label>
                    <input class="form-control" type="number" min="0" name="importeFinanciamiento" id="importeFinanciamiento" 
                        value="0" onchange="actualizarPresupuestoTotal()">

                </div>
                
                <div class="col cuadroCircularPlomo1">
                    <label for="">Presupuesto Total</label>
                    <input class="form-control" type="text" readonly name="importePresupuestoTotal" id="importePresupuestoTotal"   
                        value="0">

                    
                </div>
                    
                <div class="w-100"></div>
                
                    






                <br>
                <div class="col" style=" text-align:center">
                    <!--
                    <button class="btn btn-primary"  style="" onclick="validarregistro()"> 
                        <i class="far fa-save"></i>
                        Guardar
                    </button>
                    -->
                    
                    <button type="button" class="btn btn-primary float-right" style="margin-left: 6px" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando"
                        onclick="clickActualizar()">
                        <i class='fas fa-save'></i> 
                        Guardar
                    </button> 
                    
                    
                    <a href="{{route('GestiónProyectos.UGE.Listar')}}" class='btn btn-info float-left'>
                        <i class="fas fa-arrow-left"></i> 
                        Regresar al Menu
                    </a>
                    
                </div>

            </div>

        </div>
        


    </div>



</form>

@endsection

@include('Layout.ValidatorJS')
@section('script')  
     <script src="/calendario/js/bootstrap-datepicker.min.js"></script>
     <script src="/calendario/locales/bootstrap-datepicker.es.min.js"></script>
     <script src="/select2/bootstrap-select.min.js"></script> 
     <script>
         
        const objNombre = document.getElementById('nombre');
        const objCodigoPresupuestal = document.getElementById('codigoPresupuestal');
        const objCodSede = document.getElementById('codSede');
        const objCodEntidadFinanciera = document.getElementById('codEntidadFinanciera');

        const objFechaInicio = document.getElementById('fechaInicio');
        const objCodTipoFinanciamiento = document.getElementById('codTipoFinanciamiento');
        const objCodMoneda = document.getElementById('codMoneda');
        const objNombreLargo = document.getElementById('nombreLargo');

        const objObjetivoGeneral = document.getElementById('objetivoGeneral');
        const objImportePresupuestoTotal = document.getElementById('importePresupuestoTotal');
        const objImporteContrapartidaCedepas = document.getElementById('importeContrapartidaCedepas');
        const objImporteContrapartidaPoblacionBeneficiaria = document.getElementById('importeContrapartidaPoblacionBeneficiaria');

        const objImporteContrapartidaOtros = document.getElementById('importeContrapartidaOtros');
        const objImporteFinanciamiento = document.getElementById('importeFinanciamiento'); 
        const objPEI = document.getElementById('codPEI');


        function clickActualizar(){
            msjError = validarActualizacion();
            if(msjError!="")
            {
                alerta(msjError);
                return;
            }

            confirmarConMensaje("Confirmacion","¿Desea crer el proyecto con la información ingresada?","warning",submitearActualizacionInfoProyecto);
        }

        function submitearActualizacionInfoProyecto(){
            document.frmUpdateInfoProyecto.submit(); // enviamos el formulario	
        }

        function validarActualizacion(){
            msjError ="";
            limpiarEstilos(['fechaInicio','fechaFinalizacion','nombre','codigoPresupuestal','nombreLargo','objetivoGeneral','codSede','codEntidadFinanciera',
                            'codTipoFinanciamiento','codMoneda','codPEI','importeFinanciamiento','importePresupuestoTotal','importeContrapartidaCedepas',
                            'importeContrapartidaPoblacionBeneficiaria','importeContrapartidaOtros']);

            msjError = validarNulidad(msjError,'fechaInicio','Fecha de inicio del proyecto');
            msjError = validarNulidad(msjError,'fechaFinalizacion','Fecha de finalización del proyecto');

            msjError = validarTamañoMaximoYNulidad(msjError,'nombre',{{App\Configuracion::tamañoMaximoNombreP}},'Nombre');
            msjError = validarTamañoMaximoYNulidad(msjError,'codigoPresupuestal',{{App\Configuracion::tamañoMaximoCodigoPresupuestalP}},'Codigo Presupuestal');
            msjError = validarTamañoMaximoYNulidad(msjError,'nombreLargo',{{App\Configuracion::tamañoMaximoNombreLargoP}},'Nombre Largo');
            msjError = validarTamañoMaximoYNulidad(msjError,'objetivoGeneral',{{App\Configuracion::tamañoMaximoObjetivoGeneralP}},'Objetivo General');  

            msjError = validarSelect(msjError,'codSede',-1,'Sede'); 
            msjError = validarSelect(msjError,'codEntidadFinanciera',-1,'Entidad Financiera');  
            msjError = validarSelect(msjError,'codTipoFinanciamiento',-1,'Tipo Financiamiento'); 
            msjError = validarSelect(msjError,'codMoneda',-1,'Moneda');   
            msjError = validarSelect(msjError,'codPEI',-1,'PEI');

            msjError = validarPositividadYNulidad(msjError,'importeFinanciamiento','Importe de Financiamiento');
            msjError = validarPositividadYNulidad(msjError,'importePresupuestoTotal','Presupuesto Total');
            msjError = validarPositividadYNulidad(msjError,'importeContrapartidaCedepas','Importe de contrapartida de CEDEPAS');
            msjError = validarPositividadYNulidad(msjError,'importeContrapartidaPoblacionBeneficiaria','Importe de contrapartida de la Poblacion Beneficiaria');
            msjError = validarPositividadYNulidad(msjError,'importeContrapartidaOtros','Importe de contrapartida otros');
            /*
            if(objNombre.value=="")
                msjError="ingrese el nombre del proyecto";
                
            if(objCodigoPresupuestal.value=="")
                msjError="Ingrese un codigo presupuestal válido.";

            
            if(objCodigoPresupuestal.value.length!=2 )
                msjError="El codigo presupuestal del proyecto solo puede tener 2 caracteres numéricos";
            
            if(objCodSede.value=="-1")
                msjError="Ingrese una sede valida";
            if(objCodEntidadFinanciera.value=="-1")
                msjError="Ingrese una entidad financiera";

            if(objFechaInicio.value=="")
                msjError="Ingrese la fecha de inicio del proyecto.";
            if(objCodTipoFinanciamiento.value=="-1")
                msjError="Ingrese el tipo de financiamiento";
            if(objCodMoneda.value=="-1")
                msjError="Ingrese la moneda";
            if(objNombreLargo.value=="")
                msjError="Ingrese el nombre completo del proyecto";
            
            
            if(objPEI.value == "-1")
                msjError="Seleccione el plan estratégico institucional con el que será registrado el proyecto.";
            if(objObjetivoGeneral.value=="")
                msjError="Ingrese el objetivo general del proyecto";
            
            if(objImporteFinanciamiento.value=="")
                msjError="Ingrese el importe de financiamiento";
            if(objImportePresupuestoTotal.value=="")
                msjError="Ingrese el presupuesto total del proyecto.";
            if(objImporteContrapartidaCedepas.value=="")
                msjError="Ingrese el  importe de contrapartida de cedepas.";
            if(objImporteContrapartidaPoblacionBeneficiaria.value=="")
                msjError="Ingrese el  importe de contrapartida de la poblacion beneficiaria.";
            if(objImporteContrapartidaOtros.value=="")
                msjError="Ingrese el importe de contrapartida otros.";
            */

            return msjError;

        }
        

        function actualizarPresupuestoTotal(){
            total = 
                parseFloat(objImporteContrapartidaCedepas.value) + 
                parseFloat(objImporteContrapartidaPoblacionBeneficiaria.value) + 
                parseFloat(objImporteContrapartidaOtros.value) + 
                parseFloat(objImporteFinanciamiento.value);
        
            total=   Math.round(total * 100) / 100
            objImportePresupuestoTotal.value = total;

        }
    </script>
@endsection

