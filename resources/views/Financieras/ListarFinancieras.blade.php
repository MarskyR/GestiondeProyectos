@extends('Layout.Plantilla')

@section('titulo')
    Listar Entidades Financieras
@endsection

@section('contenido')


<div class="card-body">
    
  <div class="well">
    <H3 style="text-align: center;">
    <strong>
      Entidades financieras

      </strong>
    </H3>
  </div>
  <div class="row">
   


    <div class="col-md-2">
      <a href="{{route('EntidadFinanciera.crear')}}" class="btn btn-primary">
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

        @foreach($entidadesFinancieras as $itemEntidad)
            <tr>
                <td>{{$itemEntidad->codEntidadFinanciera}}</td>
                <td>{{$itemEntidad->nombre}}</td>
                <td>
                    <a href="{{route('EntidadFinanciera.editar',$itemEntidad->codEntidadFinanciera)}}" class="btn btn-info btn-sm btn-icon icon-left"><i class="entypo-pencil"></i>Editar</a>

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
                            window.location.href='{{route('EntidadFinanciera.eliminar',$itemEntidad->codEntidadFinanciera)}}';

                        });"><i class="entypo-cancel"></i>Eliminar</a>

                </td>
            </tr>
        @endforeach
        
      </tbody>
    </table>
    
  

  </div>


@endsection
