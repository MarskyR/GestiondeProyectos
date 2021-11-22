@extends('Layout.Plantilla') 

@section('titulo')
    Programar Metas del Proyecto
@endsection



@section('contenido')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>

<link rel="stylesheet" href="/libs/morris.css">
<script src="/libs/morris.min.js" charset="utf-8"></script>

  <h1>Programar Metas</h1>
  

  <div class="card">
    <div class="card-header" style=" ">
      <div class="row">
        <div class="col">
          
          <h3>Proyecto:</h3>
          {{$indicador->getProyecto()->nombre}}
          <br>
          
          <b>
            Inicio:
          </b>
          {{$indicador->getProyecto()->getFechaInicio()}}
          <br>
          <b>Fin:</b>
           {{$indicador->getProyecto()->getFechaFinalizacion()}}
        </div>
        <div class="col">
          <h3>Res. Esperado: </h3>
          
          {{$indicador->getResultadoEsperado()->descripcion}}
        </div>
        <div class="col">
          <h3>Actividad: </h3>
          <p class="fontSize9">
            {{$indicador->getActividad()->descripcion}}
          </p>
          
        </div>
       

        <div class="col">
          <h3>Indicador:</h3>
          
          {{ "[".$indicador->meta."] ".$indicador->unidadMedida}}
        </div>

        
      </div>
    </div>
  </div>
  @include('Layout.MensajeEmergenteDatos')

  <div class="card">
    <div class="card-header" style=" ">
      <h3 class="card-title">Metas</h3>

      <div class="card-tools">
        <ul class="nav nav-pills ml-auto">
        
        </ul>
      </div> 

    </div>
    <!-- /.card-header -->


    <div class="card-body p-1">
      <div class="">
        <div class="row">
          <div class="col-4">
            <div class="table-responsive">                           
              <table id="detalles" class="table table-bordered table-sm" style='background-color:#FFFFFF;font-size: 0.9em'> 
                  
                  <form id="formProgramarMeta" name="formProgramarMeta" action="{{route('IndicadorActividad.registrarMetaProgramada')}}"  >
                    <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codIndicadorActividad" value="{{$indicador->codIndicadorActividad}}">
                    
                    <thead >
                        <th class="text-center"> 
                            <div> {{-- INPUT PARA tipo--}}
                                <select class="form-control"  id="mesNuevaMeta" name="mesNuevaMeta">
                                  <option value="-1">--Mes--</option>
                                   @foreach($listaMeses as $itemMes)
                                    <option value="{{$itemMes->codDosDig}}">
                                    {{$itemMes->nombre}}
                                    </option>
                                   @endforeach
                                </select>        
                            </div>
                            <div>
                              <input type="number" class="form-control" id="añoNuevaMeta" name="añoNuevaMeta" value="{{ Carbon\Carbon::now()->format('Y') }}" placeholder="Año...">
                            </div>


                        </th>                                 
                        
                        
                        <th class="text-center">


                            <div > {{-- INPUT PARA codigo presup--}}
                                <input class="" type="checkbox" name="esReprogramada" id="esReprogramada" title="Implica que esta meta surge de la reprogramación de metas anteriores.">
                                <label for="esReprogramada">Reprogramada</label>

                                <input type="number" class="form-control" min="0" 
                                name="cantidadProgramada" id="cantidadProgramada">     
                            </div>
      
                        </th>
                        <th></th>
                        <th  class="text-center">
                            <button type="button" id="btnadddet" name="btnadddet" 
                                class="btn btn-success btn-sm" onclick="agregar()" >
                                    Agregar
                            </button>    
                        </th>                                            
                        
                    </thead>
                  </form>
                  
                  
                  <thead class="thead-default" style="background-color:#3c8dbc;color: #fff; font-size:9pt">     
                      <th class="text-center">Mes</th>                                              
                      <th class="text-center">Prog</th>
                      <th class="text-center">Ejec</th>
                      <th width="20%" class="text-center">Opciones</th>                                            
                      
                  </thead>
                  <tfoot>
    
                                                                                      
                  </tfoot>
                  <tbody>
                    @foreach($listaMetas as $meta)
                      <tr>
                        
                          <td class="text-center">
                            {{$meta->getMesYAñoEscrito()}}
                          </td>
                          <td class="text-center">
                            @if($meta->esReprogramada())
                              (Rep) 
                            @endif
                            {{$meta->cantidadProgramada}}
                          </td>
                          <td class="text-center">
                            {{$meta->getCantidadSiEjecutada()}}
                          </td>
                          
                          <td class="text-center">
                            <a href="#" onclick="clickEliminarMeta({{$meta->codMetaEjecutada}},'{{$meta->getMesYAñoEscrito()}}')" class="btn btn-danger btn-sm">
                                
                              <i class="fas fa-trash fa-sm"></i>
                            </a>
                            <a href="#" onclick="clickEditarMeta(this,{{$meta->codMetaEjecutada}})" class="btn btn-warning btn-sm">
                                
                              <i class="fas fa-edit"></i></a>
                            </a>
                          </td>
                          
                      </tr>
                    @endforeach
                 
                    
                  </tbody>
              </table>
               
          
            </div> 
          </div>
          <div class="col-8">
            <h2>Grafico</h2>
            <hr>
            <div id="table1"></div>
          </div>
        </div>
        
      </div>
    </div>
    <!-- /.card-body -->
    
  </div>
  <!-- /.card -->
  <br>

  <div class="row">
    <div class="col">
        <a href="{{route('GestiónProyectos.editar',$indicador->getProyecto()->codProyecto)}}"class="btn btn-success">
          <i class="fas fa-arrow-left"></i> 
          Regresar al proyecto
        </a>
    </div>
    <div class="col"></div>
    <div class="col"></div>
    <div class="col"></div>
    <div class="col"></div>
    
  </div>

<style>
  .circulo{
 
     -moz-border-radius: 50%;
     -webkit-border-radius: 50%;
     border-radius: 50%;
     
  }

  .BordeCircular{
        border-radius: 10px;
        background-color:rgb(190, 190, 190);
        margin-left: 2%;
    }
</style>
@endsection

@include('Layout.ValidatorJS')
@section('script')

<script>
  var codMetaEditar=0;
  let matriz = 
    @php 
      echo json_encode($listaMetas); 
    @endphp;
    
  console.log(matriz);

  

  //var datosActuales=<?php echo json_encode($arr); ?>;
  new Morris.Line({//META - EJECUTADA
      element: 'table1',
      data: <?php echo json_encode($arr); ?>,
      xkey: 'y',
      ykeys: ['a'],
      labels: ['META'],
      resize: true,
      lineColors: ['#C14D9F'],
      lineWidth: 1,
      gridTextSize: 10,
      //parseTime: false,//hace que los parametros de las funciones ya no traten a el ejex como tipo date
      xLabelFormat: function (x) {//para como se vera en el ejex
        var IndexToMonth = [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dic" ];
        var month = IndexToMonth[ x.getMonth() ];
        var year = x.getFullYear();
        return month+'-'+year;
      },
      dateFormat: function (x) {//para ver como se mostrara en los puntos de del plano
        var IndexToMonth = [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dic" ];
        var month = IndexToMonth[ new Date(x).getMonth() ];
        var year = new Date(x).getFullYear();
        return month+'-'+year;
      }
  });

  function clickEditarMeta(btn,codMeta) {
    var row = btn.parentNode.parentNode;
    row.parentNode.removeChild(row);

    //datos
    codMetaEditar=codMeta;//DEFINE QUE SE VA A EDITAR
    fila=buscarEnMatriz(codMeta);
    fechaArray=fila.mesAñoObjetivo.split('-');

    document.getElementById('cantidadProgramada').value =fila.cantidadProgramada;
    document.getElementById('añoNuevaMeta').value =fechaArray[0];
    document.getElementById('mesNuevaMeta').value =fechaArray[1];
  }

  function buscarEnMatriz(codMeta){
    for (let index = 0; index < matriz.length; index++) {
      const element = matriz[index];
      if(element.codMetaEjecutada==parseInt(codMeta)){
        return element;
      }
    }
  }

  function agregar() {

    msj='';
    limpiarEstilos(['mesNuevaMeta','añoNuevaMeta','cantidadProgramada']);
    
    msj = validarSelect(msj,'mesNuevaMeta',-1,'Mes de Meta');
    msj = validarPositividadYNulidad(msj,'añoNuevaMeta','Año de Meta');
    msj = validarPositividadYNulidad(msj,'cantidadProgramada','Cantidad Programada');
    año = parseInt($('#añoNuevaMeta').val() );
    if(año < 2000 || año > 9999){
      msj='Debe ingresar un año válido (4 digitos).';
    }

    /*
    if($('#mesNuevaMeta').val()==-1){
      msj='Debe seleccionar un mes para la meta';
    }
    if($('#añoNuevaMeta').val()==''){
      msj='Debe ingresar un año.';
    }
    */
    //console.log('leng = ' + $('#añoNuevaMeta').val().lenght);


    if(msj=='')
      if(codMetaEditar!=0)
        confirmarConMensaje('¿Esta seguro de editar el registro?','','warning',ejecutarEditar);
      else 
        confirmarConMensaje('¿Esta seguro de agregar el registro?','','warning',ejecutarAgregar);
    else 
        alerta(msj);

  }


  function ejecutarAgregar() {

      document.formProgramarMeta.submit();

  }
  function ejecutarEditar() {
      año = parseInt($('#añoNuevaMeta').val() );
      mes = $('#mesNuevaMeta').val();
      cantidad = $('#cantidadProgramada').val();

      //console.log("/GestionProyectos/IndicadorActividad/editarMeta/" + codMetaEditar+"*"+año+"*"+mes+"*"+cantidad);
      location.href = "/GestionProyectos/IndicadorActividad/editarMeta/" + codMetaEditar+"*"+año+"*"+mes+"*"+cantidad;
  }


  var codMetaAEliminar = "0";
  function clickEliminarMeta(codMeta,nombreMes){

    codMetaAEliminar = codMeta;
    confirmarConMensaje("Confirmación","¿Desea eliminar la meta del mes "+nombreMes +"?","warning",ejecutarEliminacionMeta);
  }

  function ejecutarEliminacionMeta(){


    location.href = "/GestionProyectos/IndicadorActividad/eliminarMeta/" + codMetaAEliminar;

  }
</script>



@endsection
