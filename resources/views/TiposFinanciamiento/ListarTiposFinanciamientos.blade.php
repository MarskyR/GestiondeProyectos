@extends('Layout.Plantilla')

@section('titulo')
    Listar Tipos de Financiamiento
@endsection

@section('contenido')


<div class="card-body">
    
  <div class="well">
    <H3 style="text-align: center;">
    <strong>
      Tipos de Financiamiento

      </strong>
    </H3>
  </div>
  <div class="row">
   


    <div class="col-md-2">
      <a href="{{route('TipoFinanciamiento.crear')}}" style="margin-bottom: 2px" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Registro
      </a>
    </div>
    
  </div>
  @include('Layout.MensajeEmergenteDatos')
  

    <table class="table table-bordered table-hover datatable" id="table-3">
      <thead>                  
        <tr>
          <th>CÓDIGO</th>
          <th>NOMBRE</th>
          <th>OPCIONES</th>
        </tr>
      </thead>
      <tbody>

        @foreach($tiposFinanciamiento as $itemTipoFinanciamiento)
            <tr>
                <td>{{$itemTipoFinanciamiento->codTipoFinanciamiento}}</td>
                <td>{{$itemTipoFinanciamiento->nombre}}</td>
                <td>
                    <a href="{{route('TipoFinanciamiento.editar',$itemTipoFinanciamiento->codTipoFinanciamiento)}}" class="btn btn-info btn-sm btn-icon icon-left"><i class="entypo-pencil"></i>Editar</a>

                    <!--Boton eliminar -->
                    <a href="#" class="btn btn-danger btn-sm btn-icon icon-left" title="Le quita el acceso al sistema." onclick="swal({//sweetalert
                            title:'Confirmación',
                            text: '¿Está seguro de eliminar el tipo de financiamiento?',     //mas texto
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
                            window.location.href='{{route('TipoFinanciamiento.eliminar',$itemTipoFinanciamiento->codTipoFinanciamiento)}}';

                        });"><i class="entypo-cancel"></i>Eliminar</a>

                </td>
            </tr>
        @endforeach
        
      </tbody>
    </table>
    
  

  </div>


@endsection
