@extends('Layout.Plantilla') 

@section('titulo')
    Listado de Sedes
@endsection

@section('contenido')
<br>

@include('Layout.MensajeEmergenteDatos')

<div class="card">
    <div class="card-header" style=" ">
      <h2 class="card-title">Admin Sistema - Sedes</h2>
      <div class="card-tools">
        <ul class="nav nav-pills ml-auto">
          <li class="nav-item">

            <button href="#" class="btn btn-success" onclick="limpiarModal()"
            data-toggle="modal" data-target="#ModalSede">
                <i class="fas fa-plus"></i> 
                Nuevo Registro
            </button>

          </li>
        </ul>
      </div>
      
    </div>


    

    <!-- /.card-header -->
    <div class="card-body p-0">
      <table class="table table-sm">
        <thead>

          <tr>
            <th scope="col">idBD</th>
            <th scope="col">Sede</th>
            <th scope="col">Administrador</th>
            <th scope="col">Opciones</th>
          </tr>

        </thead>
        <tbody>

          @foreach($listaSedes as $itemSede)
            
          
          <tr>
            <td>{{$itemSede->codSede}}</td>
            <td>{{$itemSede->nombre}}</td>
         
            <td>

              <select class="form-control select2 select2-hidden-accessible selectpicker" 
                  onchange="cambiarAdministrador({{$itemSede->codSede}})" data-select2-id="1" 
                  tabindex="-1" aria-hidden="true" data-live-search="true" id="comboAdministrador{{$itemSede->codSede}}">

                @foreach($listaAdministradores as $administrador)
                  <option value="{{$administrador->codEmpleado}}" {{$itemSede->codEmpleadoAdministrador==$administrador->codEmpleado ? 'selected':''}}>
                    {{$administrador->getNombreCompleto()}}
                  </option>                                 
                @endforeach
              </select> 
            </td>
           
          
            <td>
              <button href="" class="btn btn-info" onclick="clickEditarSede({{$itemSede->codSede}})"
              data-toggle="modal" data-target="#ModalSede">
                <i class="fas fa-pen-square"></i>
                Editar
              </button>

            </td>
          </tr>

          @endforeach

        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
  
  <!-- /.card -->

<div class="modal fade" id="ModalSede" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalSede">Agregar Sede</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmSede" name="frmSede" action="{{route('Sede.GuardarEditar')}}" method="POST">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codSede" id="codSede" value="0">
                        @csrf
                        
                        <div class="row">
                            <div class="col">
                              <label for="">Nombre de la Sede</label>
                              <input type="text" class="form-control" value="" name="nombre" id="nombre" >
                            </div>
                            <div class="w-100"></div>
                            <br>
                            <div class="col">
                                <label for="">Seleccionar Administrador</label>
                                <select class="form-control"  id="codEmpleadoAdministrador" name="codEmpleadoAdministrador" onchange="" >
                                    <option value="-1"> -- Administrador -- </option>
                                    @foreach($listaAdministradores as $administrador)
                                        <option value="{{$administrador->codEmpleado}}">
                                            {{$administrador->getNombreCompleto()}}
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

                    <button type="button" class="m-1 btn btn-primary" onclick="clickGuardarSede()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
        </div>
    </div>
</div>

@endsection



<script>
  
  var listaSedes = [];

  document.addEventListener('DOMContentLoaded', function () {
      cargarSedes();
  }, false);

  function cargarSedes(){
    @foreach($listaSedes as $itemSede)
        listaSedes.push({
          codSede : {{$itemSede->codSede}},
          nombre : '{{$itemSede->nombre}}',
          codEmpleadoAdministrador : {{$itemSede->codEmpleadoAdministrador}}
        });
    @endforeach

  }


  function clickEditarSede(codSede){
    sede = listaSedes.find(element => element.codSede == codSede);

    document.getElementById('codSede').value = sede.codSede;
    document.getElementById('nombre').value = sede.nombre;
    document.getElementById('codEmpleadoAdministrador').value = sede.codEmpleadoAdministrador;
    document.getElementById('TituloModalSede').innerHTML = "Editar Sede";

  }

  function clickGuardarSede(){
    msjError = validarFormularioSede();
    if(msjError!=""){
      alerta(msjError);
      return;
    }
    
    document.frmSede.submit();
  }




  function validarFormularioSede(){
    codSede = document.getElementById('codSede').value;
    nombre = document.getElementById('nombre').value;
    codEmpleado = document.getElementById('codEmpleadoAdministrador').value;
    
    msjError = "";
    if(nombre == ""){
      msjError = "Debe ingresar un nombre";

    }

    tamañoMaximoNombreSede = {{App\Configuracion::tamañoMaximoNombreSede}}
    if(nombre.length > tamañoMaximoNombreSede){
      msjError = "El nombre de la sede debe tener máximo " + tamañoMaximoNombreSede + " caracteres. El tamaño actual es "+nombre.length;
    }
    
    if(codEmpleado == "-1"){
      msjError = "Debe seleccionar un administrador para la sede";
    }

    return msjError;

  }

  function limpiarModal(){
    codSede = document.getElementById('codSede').value = "0";
    nombre = document.getElementById('nombre').value = "";
    codEmpleado = document.getElementById('codEmpleadoAdministrador').value = "-1";
    document.getElementById('TituloModalSede').innerHTML = "Registrar nueva Sede";
    

  }












  function cambiarAdministrador(codSede){
    
    codAdministrador = document.getElementById('comboAdministrador'+codSede).value;
    

    $.get('/Sede/cambiarAdministrador/'+codSede+'*'+codAdministrador, function(data){
      console.log(data);
        
        if(data == 1) 
          alertaMensaje('Enbuenahora','Se actualizó el administrador de la sede','success');
        else{ 
          
          alerta('No se pudo actualizar el administrador de la sede. Revise su conexión a internet.');
        }
      });

  }

</script>
