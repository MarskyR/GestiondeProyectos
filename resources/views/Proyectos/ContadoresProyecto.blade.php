@extends('Layout.Plantilla')
@section('titulo')
    Contadores del proyecto
@endsection
@section('contenido')

<div class="card-body">
    
    <div class="well">
      <H3 style="text-align: center;">
        <strong>
          Contadores del proyecto
        </strong>
        <br>
        {{$proyecto->nombre}}
      </H3>
      
    </div>

    <form id="frmContador" name="frmContador" role="form" action="{{route('GestiónProyectos.agregarContador')}}" method="post">
      @csrf 
      <input type="hidden" id="codProyecto" name="codProyecto" value="{{$proyecto->codProyecto}}">
      <div class="row">
          <div class="col-md-1"><label>Contador:</label></div>
          <div class="col-md-3">
              <select class="form-control"  id="codEmpleadoConta" name="codEmpleadoConta" >
                  <option value="-1">Seleccionar</option>
                  @foreach($contadores as $itemcontador)
                  <option value="{{$itemcontador->codEmpleado}}">{{$itemcontador->getNombreCompleto()}} </option>
                  @endforeach
              </select>
          </div>
          <div class="col-md-1">
            <button class="btn btn-success">Agregar</button>
            </div>
      </div>
    </form>
    <br/> 

    <table class="table table-bordered table-hover datatable" id="table-3">
      <thead>                  
        <tr>
          <th>CÓDIGO</th>
          <th>NOMBRES Y APELLIDOS</th>
          <th>OPCIONES</th>
        </tr>
      </thead>
      <tbody>
        @foreach($contadoresSeleccionados as $itemcontador)
          <tr>
            <td>{{$itemcontador->codEmpleado}}</td>
            <td>{{$itemcontador->getNombreCompleto()}}</td>
            <td>
              <a href="#" class="btn btn-danger btn-sm btn-icon icon-left" title="Eliminar registro" onclick="swal({//sweetalert
                  title:'¿Eliminar el contador del proyecto?',
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
                  window.location.href='{{route('GestiónProyectos.eliminarContador',$itemcontador->codEmpleado)}}';
              });">Eliminar</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <br>
    
    <a href="{{route('GestiónProyectos.AdminSistema.Listar')}}" class="btn btn-primary icon-left" style="margin-left:600px;"><i class="entypo-pencil"></i>Regresar</a>  

  </div>
@endsection
