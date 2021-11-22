@extends('Layout.Plantilla')

@section('titulo')
    Listar Unidades de Medida
@endsection

@section('contenido')


<div class="card-body">
    
  <div class="well"><H3 style="text-align: center;"><strong>UNIDADES DE MEDIDA</strong></H3></div>
  <div class="row">
   


    <div class="col-md-2">
      <a href="{{route('GestiónUnidadMedida.crear')}}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Registro
      </a>
    </div>
    <div class="col-md-10">
      
    </div>
  </div>
  @include('Layout.MensajeEmergenteDatos')
  

    <table class="table table-bordered table-hover datatable" id="table-3">
      <thead>                  
        <tr>
          <th>CODIGO</th>
          <th>NOMBRE</th>
          <th>OPCIONES</th>
        </tr>
      </thead>
      <tbody>

        @foreach($unidades as $itemunidad)
            <tr>
                <td>{{$itemunidad->codUnidadMedida}}</td>
                <td>{{$itemunidad->nombre}}</td>
                <td>
                    <a href="{{route('GestiónUnidadMedida.editar',$itemunidad->codUnidadMedida)}}" class="btn btn-info btn-sm btn-icon icon-left"><i class="entypo-pencil"></i>Editar</a>

                    <!--Boton eliminar -->
                    <a href="#" class="btn btn-danger btn-sm btn-icon icon-left" title="Le quita el acceso al sistema." onclick="swal({//sweetalert
                            title:'¿Está seguro de eliminar la unidad?',
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
                            window.location.href='{{route('GestiónUnidadMedida.eliminar',$itemunidad->codUnidadMedida)}}';

                        });"><i class="entypo-cancel"></i>Eliminar</a>

                </td>
            </tr>
        @endforeach
        
      </tbody>
    </table>
    
    {{$unidades->links()}}

  </div>


@endsection
