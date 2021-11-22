@extends('Layout.Plantilla')

@section('titulo')
    Listar Planes estratégicos de cedepas
@endsection

@section('contenido')


<div class="card-body">
    
  <div class="well">
    <H3 style="text-align: center;">
    <strong>
      Planes estratégicos de CEDEPAS Norte

      </strong>
    </H3>
  </div>
  <div class="row">
   


    <div class="col-md-2">
      <a href="{{route('PlanEstrategico.crear')}}" style="margin-bottom: 2px" class="btn btn-primary">
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
          <th>PERIODO</th>
          <th>OPCIONES</th>
        </tr>
      </thead>
      <tbody>

        @foreach($planesEstrategicos as $itemPlanEstrategico)
            <tr>
                <td>{{$itemPlanEstrategico->codPEI}}</td>
                <td>{{$itemPlanEstrategico->getPeriodo()}}</td>
                
                <td>
                    <a href="{{route('PlanEstrategico.editar',$itemPlanEstrategico->codPEI)}}" class="btn btn-info btn-sm btn-icon icon-left">
                      <i class="fas fa-pen"></i>
                      Editar
                    </a>
                   
                    
                    <!--Boton eliminar -->
                    <a href="#" class="btn btn-danger btn-sm btn-icon icon-left" title="Le quita el acceso al sistema." onclick="swal({//sweetalert
                            title:'Confirmación',
                            text: '¿Está seguro de desactivar el objetivo estratégico del año {{$itemPlanEstrategico->año}}?',     //mas texto
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
                            window.location.href='{{route('PlanEstrategico.eliminar',$itemPlanEstrategico->codPEI)}}';

                        });">
                      <i class="fas fa-arrow-down"></i>
                      Desactivar
                    </a>

                </td>
            </tr>
        @endforeach
        
      </tbody>
    </table>
    
  
    <button class="btn btn-primary" onclick="confirmar()">
      Generar relaciones faltantes Proy-ObjEstr
    </button>
  </div>


@endsection
@section('script')

<script>


  function confirmar(){
    confirmarConMensaje("Confirmación","¿Desea generar todas las relaciones faltantes entre proyectos y obj estrategicos?",'warning',ejecutar);
  }
  function ejecutar(){
    location.href ="{{route('PlanEstrategico.generarRelacionesProyectosYObjetivosEstrategicos')}}";
  }

  
</script>
@endsection