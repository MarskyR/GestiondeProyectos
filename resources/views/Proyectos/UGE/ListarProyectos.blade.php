@extends('Layout.Plantilla') 

@section('titulo')
    UGE Proyectos
@endsection

@section('contenido')



@include('Layout.MensajeEmergenteDatos')

<br>
<div class="card">
    <div class="card-header" style=" ">
      <div class="row">
        <div class="col-md">
          <h3 class="">Buscar proyectos por filtros</h3>
        </div>
        <div class="col-md">
          
          <a href="{{route('GestiónProyectos.crear')}}" class="btn btn-primary float-right">
            <i class="fas fa-plus"></i> 
            Nuevo Proyecto
          </a>

        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <form id="formFiltrosProyectos" action="" method="get">
            <div class="row m-2">
              <div class="col">

                <select class="form-control mr-sm-2"  id="codTipoFinanciamientoBuscar" name="codTipoFinanciamientoBuscar" >
                  <option value="0">--Tipo Financiamiento--</option>
                  @for($i = 0; $i < count($tiposFinanciamiento); $i++)
                    <option value="{{$tiposFinanciamiento[$i][0]}}" {{$tiposFinanciamiento[$i][0]==$codTipoFinanciamientoBuscar ? 'selected':''}}>
                      {{$tiposFinanciamiento[$i][1]}}
                    </option>        
                  @endfor
                </select>
              </div>
              <div class="col">

                <select class="form-control mr-sm-2"  id="codEntidadFinancieraBuscar" name="codEntidadFinancieraBuscar" >
                  <option value="0">--Entidad Financiera--</option>
                  @foreach($entidades as $itementidad)
                      <option value="{{$itementidad->codEntidadFinanciera}}" {{$itementidad->codEntidadFinanciera==$codEntidadFinancieraBuscar ? 'selected':''}}>
                       {{$itementidad->nombre}}
                      </option>                                 
                  @endforeach 
                </select>
              </div>
              <div class="col">

                <select class="form-control mr-sm-2"  id="codSedeBuscar" name="codSedeBuscar">
                  <option value="0">--Sede Principal--</option>
                  @foreach($sedes as $itemsede)
                      <option value="{{$itemsede->codSede}}" {{$itemsede->codSede==$codSedeBuscar ? 'selected':''}}>
                       {{$itemsede->nombre}}
                      </option>                                 
                  @endforeach 
                </select>

              </div>
              <div class="col">

                    
                <input class="form-control mr-sm-2" type="search" 
                aria-label="Search" name="nombreProyectoBuscar" id="nombreProyectoBuscar" value="{{$nombreProyectoBuscar}}" placeholder="Nombre del proyecto">

              </div>

            </div>
            <div class="row m-2">
            
                  <div class="col">
                    <label style="" for="">Año:</label>
                  </div>
                  <div class="col">

                    <input class="form-control mr-sm-2 w-75" type="number" aria-label="Search" min="1990" max="2100"
                    name="añoInicioBuscar" id="añoInicioBuscar" value="{{!is_null($añoInicioBuscar)?$añoInicioBuscar: ""}}" placeholder="Inicio">
                  
                  </div>
                  
                
                  <div class="col">
                    <input class="form-control mr-sm-2 w-75"  type="number" aria-label="Search"  min="1990" max="2100"
                    name="añoFinBuscar" id="añoFinBuscar" value="{{!is_null($añoFinBuscar)?$añoFinBuscar: "" }}" placeholder="Fin">
      
                  </div>
                  
                  <div class="col" id="colObjsPEI">
                    <select class="form-control" name="codPEI" id="codPEI" onchange="cambioPEI()">
                        <option value="-1">- PEI -</option>
                        @foreach($listaPEIs as $pei) 
                            <option value="{{$pei->codPEI}}" @php
                             if($codPEI==$pei->codPEI) echo "selected"; 
                            @endphp>
                              {{$pei->añoInicio}}
                            </option>
                        @endforeach
                    </select>
                  </div>

                  <div class="col">
                    <select class="form-control" name="objetivosPEI" id="objetivosPEI" multiple onchange="cambioObjPEI()">
                      
                    </select>

                  </div>
                  
                  <div class="col">
                    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="vectorObjetivosPEISeleccionados" 
                      id="vectorObjetivosPEISeleccionados" value="{{$vectorObjetivosPEISeleccionados}}">

                  </div>
                  <div class="col"></div>
                  <div class="col">

                    <button type="button" class="btn btn-success float-right" type="button" onclick="clickBuscar()">
                      <i class="fas fa-search"></i> 
                      Buscar
                    </button>

                  </div>
                  
                  <div class="col">
                    
                    <button type="button" onclick="clickExportar()" class="btn btn-info float-left">
                      <i class="fas fa-file-excel"></i> Exportar
                    </button>
                  </div>
                  

            </div>
            




           
          </form>
        </div>
      </div>
    </div>
   

    {{-- AQUI LOS FILTROS --}}

    

    <!-- /.card-header -->
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm fontSize10" >
          <thead>
  
            <tr>
              
              <th width="5%">Cod</th>
              <th  width="8%"> NOMBRE PROYECTO</th>
              <th>Fecha Inicio</th>
              <th>Entidad Financiera</th>
              <th>
                Obj PEI

              </th>
              <th  width="10%">Tipo Financiamiento</th>
              <!-- <th>Sede</th>-->
              
              <th>Estado</th>
              <th>Gerente</th>
              <th scope="col">Opciones</th>
              
            </tr>
  
          </thead>
          <tbody>
  
            @foreach($listaProyectos as $itemProyecto)
              
            
            <tr>
              <td>{{$itemProyecto->codigoPresupuestal}}</td>
              
              <td>{{$itemProyecto->nombre}}</td>
              <td>{{$itemProyecto->getFechaInicio()}}</td>
              <td>{{$itemProyecto->getEntidadFinanciera()->nombre}}</td>
              <td>
                <b>
                  {{$itemProyecto->getPEI()->añoInicio}}:
                </b>
                {{$itemProyecto->getObjPEIs()}}
              
              </td>
              <td>{{$itemProyecto->getTipoFinanciamiento()->nombre}}</td>
              <!--<td>{{$itemProyecto->getSede()->nombre}}</td>-->
                
              <td>
                
              <select class="form-control " 
                  onchange="actualizarEstado({{$itemProyecto->codProyecto}},'{{$itemProyecto->nombre}}')" style="width: 100%;" data-select2-id="1" 
                  tabindex="-1" aria-hidden="true" data-live-search="true" id="selectEstado">
                @foreach($listaEstados as $estado)
                  <option value="{{$estado->codEstadoProyecto}}" {{$itemProyecto->codEstadoProyecto==$estado->codEstadoProyecto ? 'selected':''}}>
                    {{$estado->nombre}}
                  </option>                                 
                @endforeach
            
          
              </select> 

              
              </td>



              </td>
              <td>  {{-- BUSCADOR DINAMICO POR NOMBRES --}}
                <select class="form-control" onchange="guardar({{$itemProyecto->codProyecto}})" style="width: 100%;" 
                  data-select2-id="1" tabindex="-1" aria-hidden="true" id="Proyecto{{$itemProyecto->codProyecto}}" 
                  name="Proyecto{{$itemProyecto->codProyecto}}" data-live-search="true">
                  
                  <option value="-1" {{$itemProyecto->codEmpleadoDirector!=null ? 'hidden':'selected'}}>
                    - Seleccione Gerente -
                  </option>          
                  
                  @foreach($listaGerentes as $gerente)
                    <option value="{{$gerente->codEmpleado}}" {{$itemProyecto->codEmpleadoDirector==$gerente->codEmpleado ? 'selected':''}}>
                      {{$gerente->getNombreCompleto()}}
                    </option>                                 
                  @endforeach
                  
                
                </select> 
              </td>
              
              <td class="text-center">
                <a href="{{route('GestiónProyectos.editar',$itemProyecto->codProyecto)}}" class="btn btn-info btn-sm">
                  <i class="fas fa-pen-square"></i>
                  Editar
                </a>
                <a href="{{route('GestionProyectos.UGE.RegistrarMetasEjecutadas',$itemProyecto->codProyecto)}}" class="btn btn-primary  btn-sm">
                  <i class="fas fa-bullseye"></i>
                  Metas
                </a>
                
              </td>
            </tr>
  
            @endforeach
  
          </tbody>
        </table>
      </div>
      
    </div>
    <!-- /.card-body -->
  </div>
  
  <!-- /.card -->
<br>

@endsection

@section('script')


<script>

  //lista de PEIs y sus obj estr
  listaPEI = [];


  
  $(window).load(function(){
    cargarPEIs();
     
  });

  function cargarPEIs(){

    @foreach ($listaPEIs as $pei)
      listaPEI.push({
          codPEI: {{$pei->codPEI}},
          añoInicio: "{{$pei->añoInicio}}",
          añoFin: "{{$pei->añoFin}}",
          listaObj: JSON.parse(`
              @php
                echo json_encode($pei->getListaObj())      
              @endphp
            `)
      });
    @endforeach
    
    @if($codPEI!="")
      cambioPEI();


    @endif

  }
  
  

  function guardar(codProyecto){
    var codGerente=$('#Proyecto'+codProyecto).val();
    if(codGerente!="-1"){
      //$.get('/asignarGerentesContadores/actualizar/'+codProyecto+'*'+codGerente+'*1', function(data){
      $.get('/GestiónProyectos/'+codProyecto+'*'+codGerente+'*1'+'/asignarGerente', function(data){
        if(data) alertaMensaje('Enbuenahora','Se actualizó el gerente','success');
        else alerta('No se pudo actualizar el gerente');
      });
    }else{
      alerta('seleccione un Gerente');
    }
    
  }
  

  function actualizarEstado(codProyecto, nombreProyecto){
    codEstado = document.getElementById('selectEstado').value;
   
    $.get('/GestiónProyectos/ActualizarEstado/'+codProyecto+'*'+codEstado, function(data){
      console.log(data);
        if(data == true) 
          alertaMensaje('Enbuenahora','Se actualizó el estado del proyecto','success');
        else{ 
         
          alerta('No se pudo actualizar el estado del proyecto. Hubo un error interno. Contacte con el administrador');
        }
      });

  }

  /* actualiza la lista de obj estr segun el PEI seleccionado */
  function cambioPEI(){

    selectObj = document.getElementById('objetivosPEI');
    selectObj.innerHTML = "";//limpiamos el select


    codPEI = document.getElementById('codPEI').value;
    

    PEI = listaPEI.find(element => element.codPEI == codPEI);
    PEI.listaObj.forEach(objetivo => { //metemos en la lista 

        
        selectObj.innerHTML += 
          /* */
          `
          <option   value="`+objetivo.codObjetivoEstrategico+`" >
                `+objetivo.item + `. ` + objetivo.nombre+`
          </option>
          `;

        

        //console.log(objetivo);
    });
  }

  function cambioObjPEI(){
    var objetivos = document.getElementById('objetivosPEI').selectedOptions;

    var vectorObjetivos = Array.from(objetivos).map(({ value }) => value);

    console.log(vectorObjetivos);

    //metemos en el input hidden para que se mande 
    document.getElementById('vectorObjetivosPEISeleccionados').value = vectorObjetivos;
  }
 
  
  function clickBuscar(){
      formu = document.getElementById('formFiltrosProyectos');
  
      formu.action = "{{route('GestiónProyectos.UGE.Listar')}}";
      formu.submit();
  }


  function clickExportar(){
    formu = document.getElementById('formFiltrosProyectos');
  
      formu.action = "{{route('GestiónProyectos.UGE.Exportar')}}";
      formu.submit();
      
  }



</script>

@endsection