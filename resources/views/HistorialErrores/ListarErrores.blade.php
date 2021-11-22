@extends('Layout.Plantilla')

@section('titulo')
    Historial de Errores
@endsection
@section('estilos')
<style>
  .grande{
    width: 30px;
    height: 30px;

  }

</style>
@endsection
@section('contenido')

@include('Layout.MensajeEmergenteDatos')
<div class="card-body">
    
  <div class="well"><H3 style="text-align: center;"><strong>HISTORIAL ERRORES</strong></H3></div>
  <div class="row">
    <div class="col-md-12">
      <form class="form-inline">
        <label for="">Empleado: </label>
        <select class="form-control select2 select2-hidden-accessible selectpicker" data-select2-id="1" tabindex="-1" aria-hidden="true" id="codEmpleadoBuscar" name="codEmpleadoBuscar" data-live-search="true">
          <option value="0">- Seleccione Empleado -</option>          
          @foreach($empleados as $itemempleado)
            <option value="{{$itemempleado->codEmpleado}}" {{$itemempleado->codEmpleado==$codEmpleadoBuscar ? 'selected':''}}>{{$itemempleado->getNombreCompleto()}}</option>                                 
          @endforeach
        </select> 
      
        <label style="" for="">
         &nbsp; Controlador:
          
        </label>

        <select class="form-control mr-sm-2"  id="controllerDondeOcurrio" name="controllerDondeOcurrio" style="margin-left: 10px;width: 300px;">
          <option value="0">--Seleccionar Controlador--</option>
          @foreach($controllers as $itemcontroller)
            <option value="{{$itemcontroller->controllerDondeOcurrio}}" {{$itemcontroller->controllerDondeOcurrio==$controllerDondeOcurrio ? 'selected':''}}>{{$itemcontroller->controllerDondeOcurrio}}</option>                                 
          @endforeach
        </select>
        <button class="btn btn-success " type="submit">Buscar</button>
       
      </form>
    </div>
  </div>
  <br>

    <table class="table table-bordered table-hover datatable table-sm" style="font-size: 10pt" id="table-3">
      <thead>                  
        <tr>
      
          <th width="3%">Cod</th>
          <th width="10%">Empleado</th>
          <th>Controlador y Método</th>
           
          <th width="5%">Fecha y Hora</th>
         
          <th>Descripcion</th>

          <th width="8%">Opciones</th>
        </tr>
      </thead>
      <tbody>

        @foreach($errores as $itemerror)
            <tr>
                

                <td>{{$itemerror->codErrorHistorial}}</td>
                <td>{{$itemerror->getNombreEmpleado()}}</td>
                <td>{{$itemerror->controllerDondeOcurrio}} -> {{$itemerror->funcionDondeOcurrio}}</td>

                <td>{{$itemerror->getFechaHora()}}</td>
             
                <td>{{$itemerror->getErrorAbreviado()}}</td>
                
                <td>
                  
                  <a href="{{route('HistorialErrores.ver',$itemerror->codErrorHistorial)}}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i>
                  </a>
            
                  <input class="grande" type="checkbox" id="CB{{$itemerror->codErrorHistorial}}"    {{-- Este es solo pa mostrarlo en el alert --}}
                    onchange="actualizarEstado({{$itemerror->codErrorHistorial}},{{$itemerror->estadoError}})"
                    {{$itemerror->getChecked()}}>
                  
                 
             
                </td>

            </tr>
        @endforeach
        
      </tbody>
    </table>
    
    {{$errores->appends(
      ['codEmpleadoBuscar'=>$codEmpleadoBuscar, 
      'controllerDondeOcurrio'=>$controllerDondeOcurrio]
                      )
      ->links()
    }}
  </div>


@endsection

@section('script')
<script>
  
  listaErrores = [];

  $(document).ready(function(){
    cargarErrores();
  
  });

  function cargarErrores(){

    @foreach ($errores as $itemerror)
      listaErrores.push(
        {
          codErrorHistorial: {{$itemerror->codErrorHistorial}},
          claseDondeOcurrio : "{{$itemerror->controllerDondeOcurrio}}",
          metodo: "{{$itemerror->funcionDondeOcurrio}}",
          empleado : "{{$itemerror->getEmpleado()->getNombreCompleto()}}"


        }
      );  

    @endforeach

  }

  function actualizarEstado(codigo){


    chekBox = document.getElementById('CB'+codigo);

    variacion = "";
   
    if(!chekBox.checked)//si está solucionado, pasará a no solucionado
      variacion = " NO";
      
    //$.get('/asignarGerentesContadores/actualizar/'+codProyecto+'*'+codGerente+'*1', function(data){
    $.get("/HistorialErrores/"+codigo+"/cambiarEstadoError", function(data){
      if(data==1) 
        alertaMensaje('Enbuenahora','Se cambió el estado del error a' + variacion +' RESUELTO.','success');
      else{ 
        alerta('No se pudo cambiar el estado del error, verifique su conexión');
        chekBox.checked = !chekBox.checked;

      }
    });
    
  }
  


  function clickIngresarInfoError($codError){



  }


</script>
@endsection
