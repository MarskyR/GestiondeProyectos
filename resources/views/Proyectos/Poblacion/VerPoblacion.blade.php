@extends ('Layout.Plantilla')
@section('titulo')
    Detalle Poblacion Beneficiaria
@endsection

@section('tiempoEspera')

<div class="loader" id="pantallaCarga"></div>

@endsection

@section('contenido')
@php
    $empLogeado=App\Empleado::getEmpleadoLogeado();
    /* ESTA VISTA SIRVE PARA EL GERENTE COMO PARA LA UGE
    SOLO EL GERENTE PUEDE GESTIONAR PERSONAS, LA UGE SOLO VE
    */
@endphp

<div class="card mt-1">
    <div class="card-header" style=" ">
        <div class="row">
            <div class="col">
                
                <h3>Proyecto:</h3>
                {{$poblacion->getProyecto()->nombre}}
            </div>

            <div class="col">
                <h3>Descripción de la población: </h3>
                
                {{$poblacion->descripcion}}
            </div>
             
            
            
        </div>
    </div>
</div>

<div style="text-align: center">
  

    

    @include('Layout.MensajeEmergenteDatos')

    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPoblacion" id="codPoblacion" value="{{$poblacion->codPoblacionBeneficiaria}}">
    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="cantElementosNaturales" id="cantElementosNaturales">
    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="cantElementosJuridicas" id="cantElementosJuridicas">
    
    {{-- CARD SUPERIOR NATURALES --}}
    <div class="card">
        <h3 style="text-align: left; margin-left: 10px; margin-top: 10px">NATURALES:</h3>
        <div class="card-body p-1">

            @if($empLogeado->esGerente())
               
                <form id="frmAgregarNaturalYaExistente" name="frmAgregarNaturalYaExistente" method="POST" 
                    action="{{route('GestionProyectos.agregarNaturalExistenteAPoblacion')}}">
                    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPoblacionBeneficiaria" value="{{$poblacion->codPoblacionBeneficiaria}}">
                    @csrf
                    <div class="row m-2">
                        <div class="col">

                            {{-- Al clickear aquí se abre el modal con el formulario 
                            <button href="" class="btn btn-sm btn-success">
                                Registrar nueva Persona Natural
                            </button>--}}
                            <button type="button" id="" class="btn btn-success" onclick="clickAgregarPersonaNatural()"
                                data-toggle="modal" data-target="#ModalRegistrarNatural">
                                Registrar nueva Persona Natural
                                <i class="fas fa-plus"></i>
                            </button>

                        </div>


                        <div class="col"></div>
                    
                        <div class="col">
                            <select class="form-control select2 select2-hidden-accessible selectpicker" data-select2-id="1" tabindex="-1" 
                                    aria-hidden="true" id="codEmpleadoBuscar" name="codEmpleadoBuscar" data-live-search="true">
                                <option value="0" selected>- Buscar por Nombre o DNI -</option>          
                                @foreach($listaPersonasNaturalesAjenas as $itemPersonaAjena)
                                    <option value="{{$itemPersonaAjena->codPersonaNatural}}">
                                        {{$itemPersonaAjena->getNombreCompletoYDNI()}}
                                    </option>                                 
                                @endforeach
                            </select> 

                        </div>
                        
                        <div class="col">
                            <button type="button" onclick="clickAgregarNaturalAjena()" class="btn btn-success">
                                Agregar a la lista
                            </button>
                        </div>
                        


                    </div>
                </form>
    
            @endif
            
            <table id="detalles" class="table table-sm">
                <thead class="thead-dark">
                    

                    <tr>
                    <th  class="text-center" width="8%" scope="col">DNI</th>
                    <th  scope="col" style="text-align: center">Nombres</th>
                    <th >Apellidos</th>
                    <th width="6%">Sexo</th>
                    <th width="9%" class="text-center">
                        <i class="fas fa-phone"></i> 
                    </th>
                    <th >Dirección</th>
                    <th width="12%" >Fecha Nacimiento</th>
                    <th width="5%" scope="col" style="text-align: center">Edad R.</th>
                    <th class="text-center" >Actividades</th>
                    <th >Lugar</th>
                    
                    <th>Opciones</th>

                    
                    </tr>
                </thead>
                <tbody>
                    @foreach($listaPersonasNaturales as $persona)           
                        <tr>
                            <td>
                                {{$persona->dni}}
                            </td>
                            <td>
                                {{$persona->nombres}}
                            </td>
                            <td>
                                {{$persona->apellidos}}
                            </td>
                            <td>
                                {{$persona->sexo}}
                            </td>
                            <td>
                                {{$persona->nroTelefono}}
                            </td>
                            <td>
                                {{$persona->direccion}}
                            </td>
                            <td>
                                {{$persona->getFechaNacimiento()}}
                            </td>
                            <td>
                                {{$persona->edadMomentanea}}
                            </td>
                            <td class="fontSize10">
                                {{$persona->getResumenActividades()}}
                            </td>
                            <td>
                                {{$persona->getLugarEjecucion()->getZonaDistrito()}}
                            </td>
                            
                            <td style="text-align:center;">              
                                <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#ModalActividades"
                                    onclick="clickEditarActividades({{$persona->codPersonaNatural}},'natural');">
                                    Actividades            
                                </button>

                                @if($empLogeado->esGerente())
                
                                    <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#ModalRegistrarNatural"
                                        onclick="editarPersonaNatural({{$persona->codPersonaNatural}});">
                                        <i class="fas fa-pen"></i>            
                                    </button>
                                    
                                    <button type="button" class="btn btn-danger btn-xs" 
                                        onclick="clickEliminarNaturalDeLaPoblacion({{$persona->codPersonaNatural}});">
                                        <i class="fa fa-times" ></i>               
                                    </button>
                                @endif
                                       
                            </td>
                        </tr>
                    @endforeach
                    
    
                </tbody>
            </table>
        </div><!-- /.card-body -->
    </div>


    {{-- CARD INFERIOR JURIDICAS --}}
    <div class="card">
        <h3 style="text-align: left; margin-left: 10px; margin-top: 10px">JURIDICAS:</h3>
        <div class="card-body p-1">
            @if($empLogeado->esGerente())
             
            <div class="row m-2">
                <div class="col">
                    <button type="button" id="" class="btn btn-success" onclick="clickAgregarPersonaJuridica()"
                        data-toggle="modal" data-target="#ModalRegistrarJuridica">
                        Registrar nueva Persona Juridica
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                 
                <div class="col">
                    
                </div>

                <div class="col">
                    <form id="frmAgregarJuridicaAjena" name="frmAgregarJuridicaAjena" action="{{route('GestionProyectos.agregarJuridicaExistenteAPoblacion')}}" method="post">
                        
                        @csrf     
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPoblacionBeneficiaria" value="{{$poblacion->codPoblacionBeneficiaria}}">
                        
                        <select class="form-control select2 select2-hidden-accessible selectpicker" data-select2-id="1" tabindex="-1" 
                            aria-hidden="true" id="codJuridicaBuscar" name="codJuridicaBuscar" data-live-search="true">
                            <option value="0" selected>- Buscar por Razón Social O Ruc -</option>          
                            @foreach($listaPersonasJuridicasAjenas as $itemPersonaAjena)
                                <option value="{{$itemPersonaAjena->codPersonaJuridica}}">
                                    {{$itemPersonaAjena->getRazonYRuc()}}
                                </option>                                 
                            @endforeach
                        </select> 

                        
                    </form>

                </div>
                <div class="col">
                    <button type="button" onclick="clickAgregarJuridicaAjena()" class="btn btn-success">
                        Agregar a la lista
                    </button>
                </div>

            </div>
            @endif
            <table id="detallesJuricas" class="table table-sm">
                                                
                
                <thead class="thead-dark">
                    

                    <tr>
                        <th  class="text-center"  width="10%" scope="col">RUC</th>
                        <th>Tipología</th>
                        <th  scope="col" style="text-align: center">Razon Social</th>
                        <th  class="text-center" >Actividades</th>
                        <th  class="text-center" >Direccion</th>
                        <th width="7%" scope="col" style="text-align: center">#Socios (H)</th>
                        <th width="7%" scope="col" style="text-align: center">#Socias (M)</th>
                        <th width="7%" scope="col" style="text-align: center">Representante</th>
                        
                        <th>Opciones</th>

                    
                    </tr>
                </thead>
                <tbody>
                                    
                    @foreach($listaPersonasJuridicas as $persona)
                        <tr>
                            <td class="text-center">
                                {{$persona->ruc}}
                            </td>
                            <td>
                                {{$persona->getTipologia()->siglas}}
                            </td>
                            <td class="text-center">
                                {{$persona->razonSocial}}
                            </td>
                            <td class=" fontSize10">
                                {{$persona->getResumenActividades()}}
                            </td>
                            <td class="text-center">
                                {{$persona->direccion}}
                            </td>
                            <td class="text-center">
                                {{$persona->numeroSociosHombres}}
                            </td>
                            <td class="text-center">
                                {{$persona->numeroSociosMujeres}}
                            </td>
                            <td class="text-center">
                                {{$persona->representante}}
                            </td>
                            
                            <td>
                                <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#ModalActividades"
                                    onclick="clickEditarActividades({{$persona->codPersonaJuridica}},'juridica');">
                                    Actividades            
                                </button>
                                @if($empLogeado->esGerente())
                    
                                    <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#ModalRegistrarJuridica"
                                        onclick="clickEditarPersonaJuridica({{$persona->codPersonaJuridica}});">
                                        <i class="fas fa-pen"></i>            
                                    </button>

                                    <button type="button" class="btn btn-danger btn-xs" 
                                        onclick="clickEliminarJuridicaDeLaPoblacion({{$persona->codPersonaJuridica}});">
                                        <i class="fa fa-times" ></i>               
                                    </button>    
                                @endif

                                
                            </td>
                        </tr>
                    @endforeach
                                
    
                </tbody>
            </table>
        </div><!-- /.card-body -->
    </div>
    

    {{-- VOLVER AL PROYECTO --}}
    <div class="row">
      <div class="col">

        @php
            if($empLogeado->esUGE())
                $ruta="GestiónProyectos.editar";
            else 
                $ruta = "GestionProyectos.Gerente.Ver";
        @endphp
        <a href="{{route($ruta,$poblacion->codProyecto)}}" class="btn btn-info">
            <i class="fas fa-arrow-left"></i> 
          Volver al Proyecto
        </a>

      </div>
      <div class="col"></div>
      <div class="col">
       
      
      </div>
      
    </div>

    {{-- MODALES EMERGENTES --}}
    
    <!-- MODAL PARA NATURAL-->
    <div class="modal fade" id="ModalRegistrarNatural" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalPersonaNatural">Agregar Persona Natural</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-left">
                        <form id="formPersonaNatural" name="formPersonaNatural" action="{{route('GestionProyectos.agregarEditarPersonaNatural')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codPoblacionBeneficiaria" value="{{$poblacion->codPoblacionBeneficiaria}}">
                            {{-- Si se creará uno nuevo, está en 0, si se va a editar tiene el codigo del indicador a editar --}}
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPersonaNatural" id="codPersonaNatural" value="0">
                            
                            <div class="row">
                                <div class="col">
                                    <label>Nombres:</label>
                                </div>
                                
                                <div class="col">
                                    <label>Apellidos:</label>
                                </div>
                                <div class="w-100"></div>
                                <div class="col">
                                    <input type="text" class="form-control" name="nombresNatural" id="nombresNatural" value="">    
                                </div>

                                <div class="col">
                                    <input type="text" class="form-control" name="apellidosNatural" id="apellidosNatural" value="">    
                                </div>
                            </div>
                            <div class="row">
                               
                                <div class="col">
                                    <label>DNI:</label>
                                </div>
                                <div class="col">
                                    <label class="">Sexo:</label>
                                </div>
                                <div class="col">
                                    <label class="">Telefono:</label>
                                </div>
                            </div>
                            <div class="row">
                               
                                 
                              
                                <div class="col" style="">
                                    <input type="text" class="form-control" name="dniNatural" id="dniNatural" value="">    
                                </div>
                               
                                <div class="col">
                                    <select class="form-control" name="sexoNatural" id="sexoNatural">
                                        <option value="-1" selected>- Sexo -</option>
                                        
                                        <option value="M">M</option>
                                        <option value="H">H</option>
                                    </select>     
                                </div>

                                <div class="col">
                                    <input type="text" class="form-control" name="telefonoNatural" id="telefonoNatural" value="">    
                                </div>
                            </div>
                            <div class="row">
                               

                                <div class="col-6">
                                    <label>Dirección:</label>
                                </div>

                                <div class="col">
                                    <label>Fecha Nac.</label>
                                </div>

                                <div class="col">
                                    <label class="">Edad:</label>
                                </div>
                            </div>
                            <div class="row">
                               
                                <div class="col-6">
                                    <input type="text" class="form-control" name="direccionNatural" id="direccionNatural" value="">    
                                </div>

                                <div class="col">
                                    <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker">
                                    {{-- INPUT PARA EL CBTE DE LA FECHA --}}
                                        <input type="text" style="text-align: center" class="form-control" name="fechaNacimientoNatural" id="fechaNacimientoNatural"
                                                value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" style="font-size: 10pt;" onchange="calcularEdad()"> 
                                        
                                        <div class="input-group-btn">                                        
                                            <button class="btn btn-primary date-set" type="button"  style="display: none" onclick="">
                                                <i class="fas fa-calendar fa-xs"></i>
                                            </button>
                                        </div>
                                    </div>   
                                </div>


                                <div class="col">
                                    <input type="number" class="form-control" name="edadNatural" id="edadNatural" value="" readonly>    
                                </div>
                            </div>
                            <div class="row">

                               

 
                                <div class="col">
                                    <label>Lugar de Procedencia:</label>
                                </div>

                            </div>
                            <div class="row">
                               
                               
                               
                                <div class="col">
                                    <select class="form-control" name="codLugarEjecucion" id="codLugarEjecucion">
                                        <option value="-1" selected>- Lugar Procedencia -</option>
                                        @foreach($poblacion->getProyecto()->getLugaresEjecucion() as $lugar)
                                            <option value="{{$lugar->codLugarEjecucion}}">
                                                {{$lugar->getZonaDistrito()}}
                                            </option>
                                            
                                        @endforeach
                                    </select>     

                                </div>
                                
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>

                        <button type="button" onclick="clickGuardarPersonaNatural()" class="btn btn-primary">
                           Agregar <i class="fas fa-save"></i>
                        </button>
                    </div>
                
            </div>
        </div>
    </div>

    <!-- MODAL PARA juridica-->
    <div class="modal fade" id="ModalRegistrarJuridica" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalPersonaJuridica">Agregar Persona Jurídica</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="modal-body text-left">
                        <form id="formPersonaJuridica" name="formPersonaJuridica" action="{{route('GestionProyectos.agregarEditarPersonaJuridica')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codPoblacionBeneficiaria" value="{{$poblacion->codPoblacionBeneficiaria}}">
                            {{-- Si se creará uno nuevo, está en 0, si se va a editar tiene el codigo del indicador a editar --}}
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPersonaJuridica" id="codPersonaJuridica" value="0">
                            
                            <div class="row">
                                
                                <div class="col">
                                    <label for="">RUC</label>
                                    <div class="row">
                                        
                                        <input type="text" class="form-control w-50 m-1" name="ruc" id="ruc">     
                                        <button type="button" onclick="consultarPorRuc()" class="m-1 w-25 btn btn-success">
                                            <i class="fas fa-search fa-1x"></i>
                                        </button>

                                    </div>
                                   
                                </div>      
                                     
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="">Tipología</label>
                                    <select class="form-control" name="codTipoPersonaJuridica" id="codTipoPersonaJuridica">
                                        <option value="0">- Seleccionar -</option>

                                        @foreach($tiposPersonaJuridica as $tipoPersona)
                                            <option value="{{$tipoPersona->codTipoPersonaJuridica}}">
                                                {{$tipoPersona->getDescripcion()}}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                </div>

                            </div>

                            <div class="row">                         
                                <div class="col" > 
                                    <label for="">Razón Social</label>
                                    <input type="text" class="form-control" name="razonSocial" id="razonSocial">     
                                 
                                </div>

                            </div>
 

                            <div class="row">
                                <div class="col">
                                    <label for="">Representante</label>
                                    <input type="text" class="form-control" name="representante" id="representante">     
                                 
                                </div>
                            
                            </div>

                            <div class="row">                             
                                <div class="col">
                                    <label for="">Direccion</label>
                                    <textarea class="form-control" name="direccionInputJuridico" id="direccionInputJuridico">     
                                    </textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div  class="col" >
                                    <label for="">Socios Hombres</label>
                                    <input type="number" class="form-control" min="0" name="numeroSociosHombres" id="numeroSociosHombres" value="0">     
                                  
                                </div>
                                <div  class="col">
                                    <label for="">Socios mujeres</label>
                                    <input type="number" class="form-control" min="0" name="numeroSociosMujeres" id="numeroSociosMujeres" value="0">     
                                </div>
                                       

                            </div>

                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>
                        
                        <button type="button" onclick="clickGuardarPersonaJuridica()" class="btn btn-primary">
                           Guardar <i class="fas fa-save"></i>
                        </button>
                    </div>
                
            </div>
        </div>
    </div>

    
    <!-- MODAL PARA SELECCIONAR LAS ACTIVIDADES DE UNA PERSONA juridica o NATURAL-->
    <div class="modal fade" id="ModalActividades" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalActividad">Agregar Actividades</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="modal-body text-left">
                        <form id="formActividad" name="formActividad" action="{{route('GestionProyectos.guardarActividadesDePersona')}}" method="POST">
                            @csrf
                            <input type="hidden" id="naturalOJuridica" name="naturalOJuridica" value="">
                            {{-- el value puede ser "natural" o "juridica", para diferenciar ambos casos--}}
                            
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPoblacionBeneficiaria" value="{{$poblacion->codPoblacionBeneficiaria}}">
                            
                            <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPersona" id="codPersona" value="0">
                            
                            <div class="row">
                                <div class="col">
                                    <label for="" id="ACT_labelNombre"></label>
                                </div>
                                <div class="w-100"></div>
                                <div class="col">
                                    <input type="text" class="form-control" id="ACT_NombrePersona" readonly>
                                </div>
                            </div>
                            <label for="">Seleccione las actividades:</label>
                            <div class="row ml-1">
                                @foreach ($listaActividades as $actividad)
                                    <div  class="col" >
                                        <input class="" type="checkbox" id="Actividad{{$actividad->codActividadPrincipal}}"    {{-- Este es solo pa mostrarlo en el alert --}}
                                        name="Actividad{{$actividad->codActividadPrincipal}}"
                                            @if($empLogeado->esUGE()){{-- Si es uge, no debe poder editar --}}
                                                onclick="return false;"
                                            @endif
                                        >

                                        <label for="Actividad{{$actividad->codActividadPrincipal}}">
                                            {{$actividad->descripcion}}
                                        </label>
                                    </div>
                                    <div class="w-100"></div>
                                @endforeach
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>
                        @if($empLogeado->esGerente())
                        
                            <button type="button" onclick="clickGuardarActividades()" class="btn btn-primary">
                            Guardar <i class="fas fa-save"></i>
                            </button>
                        @endif
                    </div>
                
            </div>
        </div>
    </div>

    
    

</div>
@endsection

@include('Layout.ValidatorJS')
   
@section('script')
    <script>
        var personasNaturales=[];
        var personasJuridicas=[];
        var listaActividades = [];
        
        tamañoMaximoRazonSocial = {{App\Configuracion::tamañoMaxRazonSocial}};
        tamañoMaximoActividadPrincipal = {{App\Configuracion::tamañoMaxActividadPrincipal}};
        tamañoMaximoDireccion =  {{App\Configuracion::tamañoMaximoDireccion}};

        $(window).load(function(){
            //cuando apenas carga la pagina, se debe copiar el contenido de la tabla a personasNaturales
            cargarPersonasNaturales();
            cargarPersonasJuridicas();
            cargarActividades();
            $(".loader").fadeOut("slow");
        });


        function cargarPersonasNaturales(){

            @foreach($listaPersonasNaturales as $persona)           
                personasNaturales.push({
                    codPersonaNatural: {{$persona->codPersonaNatural}},
                    dni: "{{$persona->dni}}",
                    nombres: "{{$persona->nombres}}",
                    apellidos: "{{$persona->apellidos}}",
                    fechaNacimiento: "{{$persona->getFechaNacimiento()}}",
                    edadMomentanea: {{$persona->edadMomentanea}},
                    sexo: "{{$persona->sexo}}",
                    direccion: "{{$persona->direccion}}",
                    nroTelefono: "{{$persona->nroTelefono}}",
                    actividadPrincipal: "{{$persona->actividadPrincipal}}",
                    codLugarEjecucion : {{$persona->codLugarEjecucion}},
                    listaActividades: {{$persona->getVectorActividades()}}
                    
                });

            @endforeach

        }
        
        
        function cargarPersonasJuridicas(){

            @foreach($listaPersonasJuridicas as $persona)           
                personasJuridicas.push({
                    codPersonaJuridica: {{$persona->codPersonaJuridica}},
                    ruc: "{{$persona->ruc}}",
                    codTipoPersonaJuridica : {{$persona->codTipoPersonaJuridica}},
                    razonSocial: "{{$persona->razonSocial}}",
                    direccion: "{{$persona->direccion}}",
                    numeroSociosHombres: {{$persona->numeroSociosHombres}},
                    numeroSociosMujeres: {{$persona->numeroSociosMujeres}},
                    actividadPrincipal: "{{$persona->actividadPrincipal}}",
                    representante: "{{$persona->representante}}",
                    listaActividades: {{$persona->getVectorActividades()}}

                });

            @endforeach

        }

        function cargarActividades(){
            @foreach($listaActividades as $actividad)
            listaActividades.push({
                    codActividad: {{$actividad->codActividadPrincipal}},
                    descripcion : `{{$actividad->descripcion}}`
                });
            @endforeach 


        }


        /* ------------------------- PERSONAS NATURALES ---------------------------- */

        function editarPersonaNatural(codPersonaNatural){

            /* Es lo mismo que poner    findOrFail(codPersonaNatural)                 */
            var persona = personasNaturales.find(element => element.codPersonaNatural == codPersonaNatural);
            console.log(persona);
            
            document.getElementById('codPersonaNatural').value = persona.codPersonaNatural;
            document.getElementById('nombresNatural').value = persona.nombres;
            document.getElementById('apellidosNatural').value = persona.apellidos;
            document.getElementById('dniNatural').value = persona.dni;
            document.getElementById('sexoNatural').value  = persona.sexo;
            document.getElementById('direccionNatural').value = persona.direccion;
            document.getElementById('telefonoNatural').value = persona.nroTelefono;
            document.getElementById('fechaNacimientoNatural').value = persona.fechaNacimiento;
            document.getElementById('edadNatural').value = persona.edadMomentanea;
            //document.getElementById('actividadPrincipalNatural').value = persona.actividadPrincipal;
            document.getElementById('codLugarEjecucion').value = persona.codLugarEjecucion;

            document.getElementById('TituloModalPersonaNatural').innerHTML = "Editar Persona Natural";

        }

        function clickAgregarPersonaNatural(){
            //limpiamos
            document.getElementById('codPersonaNatural').value = "0";
            document.getElementById('nombresNatural').value = "";
            document.getElementById('apellidosNatural').value = "";
            document.getElementById('dniNatural').value = "";
            document.getElementById('sexoNatural').value = "-1";
            document.getElementById('direccionNatural').value = "";
            document.getElementById('telefonoNatural').value = "";
            document.getElementById('fechaNacimientoNatural').value = "";
            document.getElementById('edadNatural').value = "";
            
            //document.getElementById('actividadPrincipalNatural').value = "";
            document.getElementById('codLugarEjecucion').value = "-1";
            

            document.getElementById('TituloModalPersonaNatural').innerHTML = "Agregar Persona Natural";

        }

        
        function clickAgregarNaturalAjena(){
                 
            codPersonaNatural = document.getElementById('codEmpleadoBuscar').value;
            if(codPersonaNatural == "0"){
                alerta('Debe seleccionar una persona con el buscador');
                return;
            }

            confirmarConMensaje("Confirmación","¿Desea agregar esta persona a la lista de personas naturales?",
                "warning",ejecutarAgregarNaturalAjena);

        }


        function ejecutarAgregarNaturalAjena(){

            document.frmAgregarNaturalYaExistente.submit();
        }


        function validarFrmPersonaNatural(){
            msjError="";
            // VALIDAMOS 
            limpiarEstilos(['dniNatural','nombresNatural','apellidosNatural','telefonoNatural','sexoNatural'
                ,'fechaNacimientoNatural','direccionNatural','codLugarEjecucion']);
            
            msjError =  validarTamañoExacto(msjError,'dniNatural',8,'DNI');
            msjError = validarNulidad(msjError,'dniNatural','DNI');
            msjError = validarTamañoMaximoYNulidad(msjError,'nombresNatural',{{App\Configuracion::tamañoMaximoNombreApellidoNA}},'Nombres');
            msjError = validarTamañoMaximoYNulidad(msjError,'apellidosNatural',{{App\Configuracion::tamañoMaximoNombreApellidoNA}},'Apellidos');  
            msjError = validarTamañoMaximoYNulidad(msjError,'telefonoNatural',{{App\Configuracion::tamañoMaximoTelefonoNA}},'Número de Telefono');
            
            
            msjError = validarSelect(msjError,'sexoNatural',"-1",'Sexo');

            //msjError = validarTamañoMaximoYNulidad(msjError,'actividadPrincipalNatural',{{App\Configuracion::tamañoMaxActividadPrincipal}},'Actividad Principal');   
            msjError = validarNulidad(msjError,'fechaNacimientoNatural','Fecha de Nacimiento');
            msjError = validarTamañoMaximoYNulidad(msjError,'direccionNatural',{{App\Configuracion::tamañoMaximoDireccion}},'Dirección');
            msjError = validarSelect(msjError,'codLugarEjecucion',"-1",'Lugar de ejecución');

            fechaNacimiento = document.getElementById('fechaNacimientoNatural').value;
            

            edadMinimaPermitida = {{App\Configuracion::edadMinimaPermitida}};
            edadMomentanea=getEdadPorDiferencia(fechaNacimiento);
            if(edadMomentanea< edadMinimaPermitida){ 
                ponerEnRojo('fechaNacimientoNatural');
        
                msjError=("La edad mínima permitida es de " + edadMinimaPermitida + " años");  
            }


            return msjError;
        }   

        function clickGuardarPersonaNatural(){
            
            msjError = validarFrmPersonaNatural();
            if(msjError!="")
            {
                alerta(msjError);
                return ;
            }
           

            document.formPersonaNatural.submit();

        }

        //solo para la edad
        var f = new Date();
        console.log(f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear());

        function getEdadPorDiferencia(fecha){//1 si la fecha ingresada es menor (dd-mm-yyyy)
            edad=0;
            fechaActual = new Date();

            diaActual=fechaActual.getDate();
            mesActual=fechaActual.getMonth()+1;
            anioActual=fechaActual.getFullYear();
            dia=fecha.substring(0,2);
            mes=fecha.substring(3,5);
            anio=fecha.substring(6,10);

            edad=parseInt(anioActual,10)-parseInt(anio,10);

            if(parseInt(anio,10)<parseInt(anioActual,10)){
                edad=parseInt(anioActual,10)-parseInt(anio,10)-1;
                if(parseInt(mes,10)<parseInt(mesActual,10)){
                    edad++;
                }
                if(parseInt(mes,10)==parseInt(mesActual,10)){
                    if(parseInt(dia,10)<=parseInt(diaActual,10)){
                        edad++;
                    }
                }
            }else edad=0;
            
            return edad;

        }

        function calcularEdad(){
            fechaNacimiento=$("#fechaNacimientoNatural").val(); 
            $('#edadNatural').val(getEdadPorDiferencia(fechaNacimiento));
        }
    
        
        codPersonaNaturalAEliminar = 0;
        function clickEliminarNaturalDeLaPoblacion(codPersonaNatural){
            var persona = personasNaturales.find(element => element.codPersonaNatural == codPersonaNatural);
            nombrePersona = persona.nombres + " " + persona.apellidos;


            codPersonaNaturalAEliminar = codPersonaNatural;
            confirmarConMensaje("Confirmación",'¿Desea eliminar a "'+nombrePersona+'" ? Solo será eliminada de esta población.','warning',ejecutarEliminacionNatural);    
        
        }



        function ejecutarEliminacionNatural(){

            cadena =  "{{$poblacion->codPoblacionBeneficiaria}}*" + codPersonaNaturalAEliminar;  
            location.href = "/GestionProyectos/PoblacionBeneficiaria/quitarNaturalDeLaPoblacion/" + cadena;
        }


        /* ----------------------------------- PERSONA JURIDICA --------------------------------------------- */

        function clickGuardarPersonaJuridica(){
            msjError = validarFrmNuevaPersonaJuridica();
            if(msjError!=""){
                alerta(msjError);
                return;
            }

            document.formPersonaJuridica.submit();

        }


        function validarFrmNuevaPersonaJuridica(){
            msjError="";
            // VALIDAMOS 
            limpiarEstilos(['ruc','codTipoPersonaJuridica','razonSocial','sexoNatural',
                'direccionInputJuridico','representante']);

            document.getElementById('ruc').classList.add('m-1');
            document.getElementById('ruc').classList.add('w-50');
            

            msjError =  validarTamañoExacto(msjError,'ruc',11,'RUC');
            msjError = validarNulidad(msjError,'ruc','RUC');
            msjError= validarSelect(msjError,'codTipoPersonaJuridica','0','Tipología de Persona Jurídica');
            msjError = validarTamañoMaximoYNulidad(msjError,'razonSocial',tamañoMaximoRazonSocial,'Razón Social');
            msjError = validarNulidad(msjError,'sexoNatural','Sexo');
            //msjError = validarTamañoMaximoYNulidad(msjError,'actividadPrincipal',tamañoMaximoActividadPrincipal,'Actividad Principal');
            
            msjError = validarTamañoMaximoYNulidad(msjError,'direccionInputJuridico',tamañoMaximoDireccion,'Dirección');
            
            msjError = validarTamañoMaximoYNulidad(msjError,'representante',{{App\Configuracion::tamañoMaximoRepresentante}},'Representante');
            

            
            return msjError;
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
                

                    document.getElementById('razonSocial').value = personaJuridicaEncontrada.razonSocial;

                    
                    //document.getElementById('actividadPrincipal').value = personaJuridicaEncontrada.actEconomicas;
                    document.getElementById('direccionInputJuridico').value = personaJuridicaEncontrada.direccion;

                }
             
                $(".loader").fadeOut("slow");
            }
            );
        }


        
        function clickAgregarPersonaJuridica(){
            /* LIMPIAMOS LOS CAMPOS ANTES DE QUE SE MUESTRE EL MODAL */

            document.getElementById('TituloModalPersonaJuridica').innerHTML = "Agregar Persona Jurídica";
            
            document.getElementById('codPersonaJuridica').value = "0";
            document.getElementById('ruc').value = "";
            document.getElementById('razonSocial').value = "";
            //document.getElementById('actividadPrincipal').value = "";
            document.getElementById('direccionInputJuridico').value = "";
            document.getElementById('numeroSociosHombres').value = "0";
            document.getElementById('numeroSociosMujeres').value = "0";
            
        }

        function clickEditarPersonaJuridica(codPersonaJuridica){
            
            /* LLENAMOS LOS CAMPOS ANTES DE QUE SE MUESTRE EL MODAL */
            
            /* Es lo mismo que poner    findOrFail(codPersonaJuridica)                 */
            var persona = personasJuridicas.find(element => element.codPersonaJuridica == codPersonaJuridica);
            console.log(persona);
            
            
            document.getElementById('TituloModalPersonaJuridica').innerHTML = "Editar Persona Jurídica";


            document.getElementById('codPersonaJuridica').value = persona.codPersonaJuridica;
            document.getElementById('ruc').value = persona.ruc;
            document.getElementById('codTipoPersonaJuridica').value = persona.codTipoPersonaJuridica;
            
            document.getElementById('razonSocial').value = persona.razonSocial;
            //document.getElementById('actividadPrincipal').value = persona.actividadPrincipal;
            document.getElementById('direccionInputJuridico').value = persona.direccion;
            document.getElementById('numeroSociosHombres').value = persona.numeroSociosHombres;
            document.getElementById('numeroSociosMujeres').value = persona.numeroSociosMujeres;
            document.getElementById('representante').value = persona.representante;
            
        }


        function clickAgregarJuridicaAjena(){
            
            codPersonaJuridica = document.getElementById('codJuridicaBuscar').value;
            if(codPersonaJuridica == "0"){
                alerta('Debe seleccionar una persona jurídica con el buscador');
                return;
            }

            //empresa =  document.getElementById('codJuridicaBuscar');
            indice = document.getElementById('codJuridicaBuscar').options.selectedIndex;
            razonSocialYDni = document.getElementById('codJuridicaBuscar').options[indice].text 

            confirmarConMensaje("Confirmación","¿Desea agregar a "+ razonSocialYDni +" a la lista de personas naturales?",
                "warning",ejecutarAgregarJuridicaAjena);
            
        }
        
        function ejecutarAgregarJuridicaAjena(){

            document.frmAgregarJuridicaAjena.submit();
        }
            

        
        
        codPersonaJuridicaAEliminar = 0;
        function clickEliminarJuridicaDeLaPoblacion(codPersonaJuridica){
            var persona = personasJuridicas.find(element => element.codPersonaJuridica == codPersonaJuridica);
         

            codPersonaJuridicaAEliminar = codPersonaJuridica;
            confirmarConMensaje("Confirmación",'¿Desea eliminar a "'+persona.razonSocial+'" ? Solo será eliminada de esta población.','warning',ejecutarEliminacionJuridica);    
        
        }
        function ejecutarEliminacionJuridica(){

            cadena =  "{{$poblacion->codPoblacionBeneficiaria}}*" + codPersonaJuridicaAEliminar;  
            location.href = "/GestionProyectos/PoblacionBeneficiaria/quitarJuridicaDeLaPoblacion/" + cadena;
        }


        /* --------------------------------------- ACTIVIDADES ------------------------------------------------------- */

        function limpiarModalActividad(){
            document.getElementById('TituloModalActividad').innerHTML = ""; 
            document.getElementById('ACT_NombrePersona').value = "";

            listaActividades.forEach(function (actividad) {
                //console.log(actividad);
                document.getElementById('Actividad'+actividad.codActividad).checked = false;
            });

        }

        function clickEditarActividades(codPersona,tipo){
            limpiarModalActividad();
            document.getElementById('naturalOJuridica').value = tipo;
            document.getElementById('codPersona').value =codPersona;
            
            if(tipo=='natural'){
                persona = personasNaturales.find(element => element.codPersonaNatural == codPersona);
                console.log(persona);

                document.getElementById('TituloModalActividad').innerHTML = "Editar Actividades de P. Natural"; 
                document.getElementById('ACT_NombrePersona').value = persona.nombres + " "+ persona.apellidos;
                document.getElementById('ACT_labelNombre').innerHTML = "Nombres y apellidos:";
                

            }else{//juridicas
                persona = personasJuridicas.find(element => element.codPersonaJuridica == codPersona);
                //console.log(persona);

                document.getElementById('TituloModalActividad').innerHTML = "Editar Actividades de P. Jurídica"; 
                document.getElementById('ACT_NombrePersona').value = persona.razonSocial;
                document.getElementById('ACT_labelNombre').innerHTML = "Razón Social:";

                
            }

            vectorActividadesDePersona = (persona.listaActividades);
            
            vectorActividadesDePersona.forEach(function (element) {
                console.log('yaa:'+element);
                document.getElementById('Actividad'+element).checked = true;
            });

        }

        function clickGuardarActividades(){

            
            document.formActividad.submit();
        }

    </script>
     
@endsection

