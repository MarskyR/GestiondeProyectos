@extends('Layout.Plantilla') 

@section('titulo')
    Listado de Actividades de Personas
@endsection

@section('contenido')
<br>

@include('Layout.MensajeEmergenteDatos')

<div class="card">
    <div class="card-header" style=" ">
      <h2 class="card-title">Admin Sistema - Actividades de Personas</h2>
      <div class="card-tools">
        <ul class="nav nav-pills ml-auto">
          <li class="nav-item">

            <button href="#" class="btn btn-success" onclick="limpiarModal()"
            data-toggle="modal" data-target="#ModalActividadPrincipal">
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
            <th scope="col">Actividad</th>
            <th scope="col">Opciones</th>
          </tr>

        </thead>
        <tbody>

          @foreach($listaActividades as $actividad)
          <tr>
            <td>{{$actividad->codActividadPrincipal}}</td>
            <td>{{$actividad->descripcion}}</td>
            
            <td>
              <button href="" class="btn btn-info" onclick="clickEditarActividad({{$actividad->codActividadPrincipal}})"
              data-toggle="modal" data-target="#ModalActividadPrincipal">
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

<div class="modal fade" id="ModalActividadPrincipal" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalActividadPrincipal">Agregar Actividad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmActividadPrincipal" name="frmActividadPrincipal" action="{{route('ActividadPrincipal.guardarEditarActividad')}}" method="POST">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codActividadPrincipal" id="codActividadPrincipal" value="0">
                        @csrf
                        
                        <div class="row">
                            <div class="col">
                              <label for="">Descripción de la actividad</label>
                              <input type="text" class="form-control" value="" name="descripcion" id="descripcion" >
                            </div>
                        </div>                            
                        
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="m-1 btn btn-primary" onclick="clickGuardarActividad()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
        </div>
    </div>
</div>

@endsection


@include('Layout.ValidatorJS')
<script>
  
  var listaActividades = [];

  document.addEventListener('DOMContentLoaded', function () {
      cargarActividades();
  }, false);

  function cargarActividades(){
    @foreach($listaActividades as $actividad)
    listaActividades.push({
          codActividadPrincipal: {{$actividad->codActividadPrincipal}},
          descripcion : `{{$actividad->descripcion}}`
        });
    @endforeach

  }


  function clickEditarActividad(codActividadPrincipal){
    actividad = listaActividades.find(element => element.codActividadPrincipal == codActividadPrincipal);

    document.getElementById('codActividadPrincipal').value = actividad.codActividadPrincipal;
    document.getElementById('descripcion').value = actividad.descripcion;
    document.getElementById('TituloModalActividadPrincipal').innerHTML = "Editar Actividad";

  }

  function clickGuardarActividad(){
    msjError = validarFormularioActividad();
    if(msjError!=""){
      alerta(msjError);
      return;
    }
    
    document.frmActividadPrincipal.submit();
  }

  function validarFormularioActividad(){
    codActividadPrincipal = document.getElementById('codActividadPrincipal').value;
    descripcion = document.getElementById('descripcion').value;
    msjError = "";
    msjError = validarTamañoMaximoYNulidad(msjError,'descripcion',{{App\Configuracion::tamañoMaximoDescripcionAP }},'Descripción');

    return msjError;

  }

  function limpiarModal(){
    document.getElementById('codActividadPrincipal').value = "0";
    document.getElementById('descripcion').value = "";
    document.getElementById('TituloModalActividadPrincipal').innerHTML = "Registrar nueva Actividad";
    

  }

</script>
