@extends('Layout.Plantilla')

@section('titulo')
    Listar Empleados
@endsection

@section('contenido')

@include('Layout.MensajeEmergenteDatos')
<div class="card-body">

  <div class="well">
    <H3 style="text-align: center;">
      <strong>
        EMPLEADOS
      </strong>
    </H3>
  </div>

  <div class="row">
    
    <div class="col-md-2">
      <a href="{{route('GestionUsuarios.create')}}" class="btn btn-primary"><i class="fas fa-plus"></i>Nuevo Registro</a>
    </div>
    <div class="col-md-10">
      <form class="form-inline float-right">
        <input class="form-control mr-sm-2" type="search" placeholder="Buscar por Nombres y Apellidos" aria-label="Search" name="nombreBuscar" id="nombreBuscar" value="{{$nombreBuscar}}">
        <input class="form-control mr-sm-2" type="search" placeholder="Buscar por DNI" aria-label="Search" name="dniBuscar" id="dniBuscar" value="{{$dniBuscar}}">
        <button class="btn btn-success " type="submit">Buscar</button>
      </form>
    </div>
  </div>

  <br>

  <table class="table table-bordered table-hover datatable table-sm fontSize8" id="table-3">
    <thead>                  
      <tr>
        <th>idBD</th>
        <th>DNI</th>
        <th>USUARIO</th>
        <th>NOMBRES Y APELLIDOS</th>
        <th>Fecha Registro</th>
        <th>PUESTO</th>
        
        <th>OPCIONES</th>
      </tr>
    </thead>
    <tbody>

      @foreach($empleados as $itemempleado)
          <tr>
              <td>{{$itemempleado->codEmpleado}}</td>
              <td>{{$itemempleado->dni}}</td>
              
              <td>{{$itemempleado->usuario()->usuario}}</td>
              <td>{{$itemempleado->getNombreCompleto()}}</td>
              <td>{{$itemempleado->fechaRegistro}}</td>
              <td>
                
                {{$itemempleado->getPuestoActual()->nombre}}

                @if($itemempleado->esContador())
                  
                <select class="form-control-xs" name="" id="codSede{{$itemempleado->codEmpleado}}" onchange="cambiarSedeContador({{$itemempleado->codEmpleado}})">
                  <option value="0">- Sede -</option>
                  
                  @foreach ($listaSedes as $sede)
                  <option value="{{$sede->codSede}}"
                    @if($itemempleado->codSedeContador == $sede->codSede)
                      selected
                    @endif
                    >
                    {{$sede->nombre}}
                  </option>
                    
                  @endforeach

                </select>

                @endif

              </td>
              <td>
                  <a href="{{route('GestionUsuarios.editUsuario',$itemempleado->codEmpleado)}}" class="btn btn-warning btn-xs btn-icon icon-left">
                    <i class="entypo-pencil"></i>
                    Editar Usuario
                  </a>
                  <a href="{{route('GestionUsuarios.editEmpleado',$itemempleado->codEmpleado)}}" class="btn btn-warning btn-xs btn-icon icon-left">
                    <i class="entypo-pencil"></i>
                    Editar Empleado
                  </a>

                  <!--Boton eliminar -->
                  <a href="#" class="btn btn-danger btn-xs btn-icon icon-left" title="Le quita el acceso al sistema." onclick="swal({//sweetalert
                          title:'<h3>¿Está seguro de cesar el usuario?',
                          text: '',     //mas texto
                          //type: 'warning',  
                          type: '',
                          showCancelButton: true,//para que se muestre el boton de cancelar
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText:  'SÍ',
                          cancelButtonText:  'NO',
                          closeOnConfirm:     true,//para mostrar el boton de confirmar
                          html : true
                      },
                      function(){//se ejecuta cuando damos a aceptar
                          window.location.href='{{route('GestionUsuarios.cesar',$itemempleado->codEmpleado)}}';

                      });">
                      
                      <i class="entypo-cancel"></i>
                      Cesar
                  </a>
                  @if($itemempleado->esContador())
                    <a href="{{route('GestionUsuarios.verProyectosContador',$itemempleado->codEmpleado)}}" class="btn btn-success btn-xs btn-icon icon-left" >
                      Asignar Proyectos

                    </a>


                  @endif
                 
              </td>
          </tr>
      @endforeach
      
    </tbody>
  </table>
    
    {{$empleados->appends(
      ['nombreBuscar'=>$nombreBuscar, 
      'dniBuscar'=>$dniBuscar]
                      )
      ->links()
    }}
  </div>

  <script>
    function cambiarSedeContador(codEmpleado){
        codSede = document.getElementById('codSede'+codEmpleado).value;
          
      $.get('/GestiónUsuarios/ActualizarSedeContador/'+codEmpleado+'*'+codSede, function(data){
          console.log(data);
          if(data == true) 
            alertaMensaje('Enbuenahora','Se actualizó la sede del contador','success');
          else{
            alerta('No se pudo actualizar la sede del contador. Hubo un error interno. Contacte con el administrador');
          }
        }
        );

    }

  </script>


@endsection
