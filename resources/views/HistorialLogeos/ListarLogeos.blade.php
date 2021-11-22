@extends('Layout.Plantilla')

@section('titulo')
    Historial de Logeos
@endsection

@section('contenido')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>

<link rel="stylesheet" href="/libs/morris.css">
<script src="/libs/morris.min.js" charset="utf-8"></script>


<div class="card-body">
    
  <div class="well">
    <H3 style="text-align: center;">
      <strong>
        Historial de Ingresos al Sistema:
        {{$codEmpleadoBuscar}}
      </strong>
    </H3>
  </div>


  <div class="row">
    <div class="col-md-12">
      <form class="form-inline">
        <label for="">Empleado: </label>
        <select class="form-control select2 select2-hidden-accessible selectpicker" data-select2-id="1" tabindex="-1" aria-hidden="true" id="codEmpleadoBuscar" name="codEmpleadoBuscar" data-live-search="true" style="margin-right: 10px">
          <option value="-1">- Seleccione Empleado -</option>          
          @foreach($empleados as $itemempleado)
            <option value="{{$itemempleado->codEmpleado}}" {{$itemempleado->codEmpleado==$codEmpleadoBuscar ? 'selected':''}}>
              {{$itemempleado->getNombreCompleto()}}
            </option>                                 
          @endforeach
        </select> 

        <label style="" for="">
          Fecha:
          
        </label>
        <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker"  style="width: 140px; margin-left: 10px">
          <input type="text"  class="form-control" name="fechaInicio" id="fechaInicio" style="text-align: center"
                 value="{{$fechaInicio==null ? Carbon\Carbon::now()->format('d/m/Y') : $fechaInicio}}" style="text-align:center;font-size: 10pt;">
          <div class="input-group-btn">                                        
              <button class="btn btn-primary date-set" type="button"><i class="fa fa-calendar"></i></button>
          </div>
        </div>
         - 
        <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker"  style="width: 140px">
          <input type="text"  class="form-control" name="fechaFin" id="fechaFin" style="text-align: center"
                 value="{{$fechaFin==null ? Carbon\Carbon::now()->format('d/m/Y') : $fechaFin}}" style="text-align:center;font-size: 10pt;">
          <div class="input-group-btn">                                        
              <button class="btn btn-primary date-set" type="button"><i class="fa fa-calendar"></i></button>
          </div>
        </div>

        <button class="btn btn-success " type="submit">Buscar</button>
      </form>
    </div>
  </div>
  <br>

  <div class="row"> {{-- GRAFICO --}}

    <div class="col-md">

      <div id="table1"></div>  

    </div>


  </div>


  <div class="row"> {{-- TABLA --}}


    <div class="col-md">
      <table class="table table-bordered table-hover datatable table-sm fontsize8" style="" id="table-3">
        <thead>                  
          <tr>
            <th class="text-center">Cod</th>
            <th class="text-center">CodEmpleado</th>
            <th class="text-center">Empleado</th>
            <th class="text-center">Rol</th>
            <th class="text-center">Fecha y Hora</th>
            <th class="text-center">IP</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logeos as $itemLogeo)
              <tr>
                  <td class="text-center">{{$itemLogeo->codLogeoHistorial}}</td>
                  <td class="text-center">{{$itemLogeo->getEmpleado()->codEmpleado}}</td>
                  <td class="text-center">{{$itemLogeo->getNombreEmpleado()}}</td>
                  <td class="text-center">{{$itemLogeo->getEmpleado()->getPuesto()->nombre}}</td>
                  <td class="text-center">{{$itemLogeo->getFechaHora()}}</td>
                  <td class="text-center">{{$itemLogeo->ipLogeo}}</td>
              </tr>
          @endforeach
          
        </tbody>
      </table>
      <!--SOLUCION PARA PAGINACION CON FILTROS -->

      {{$logeos->appends(
        ['codEmpleadoBuscar'=>$codEmpleadoBuscar, 
        'fechaInicio'=>$fechaInicio, 
        'fechaFin'=>$fechaFin]
                        )
        ->links()
      }}



    </div>

  </div>




</div>
@endsection
@section('script')
  <script>
    
    $(document).ready(function(){
            
      new Morris.Line({//META - EJECUTADA
          element: 'table1',
          data: <?php echo json_encode($arr); ?>,
          xkey: 'date',
          ykeys: ['a'],
          labels: ['LOGEOS'],
          resize: true,
          lineColors: ['#C14D9F'],
          lineWidth: 1
      });  
    });
  


  </script>
@endsection