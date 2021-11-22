@extends ('Layout.Plantilla')
@section('titulo')
  Listar Ordenes de Compra
@endsection
@section('contenido')
<style>

.col{
  margin-top: 15px;

  }

.colLabel{
  width: 13%;
  margin-top: 18px;


}


</style>



<div style="text-align: center">
  <h2> Listar mis Ordenes de Compra </h2>
  


  <br>
    
    <div class="row">
      <div class="col-md-2">
        <a href="{{route('OrdenCompra.Empleado.Crear')}}" class = "btn btn-primary" style="margin-bottom: 5px;"> 
          <i class="fas fa-plus"> </i> 
            Nueva Orden de Compra
        </a>
      </div>
      <div class="col-md-10">
        <form class="form-inline float-right">

          <label style="" for="">
            Fecha:
            
          </label>

          <div class="input-group date form_date " data-date-format="dd/mm/yyyy" data-provide="datepicker"  style="width: 140px">
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
    


    @include('Layout.MensajeEmergenteDatos')
      
    <table class="table table-sm" style="font-size: 10pt; margin-top:10px;">
      <thead class="thead-dark">
        <tr>
          <th>Código</th>
          <th style="text-align: center">
            Fecha Creación
          </th>
          <th>
            Emisor
          </th>
          <th>Proyecto</th>
          <th>Proveedor</th>
          <th>Detalles</th>
          <th>Importe Total</th>
         
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($ordenes as $itemorden)
            <tr>
                <td style = "padding: 0.40rem">
                  {{$itemorden->codigoCedepas}}
                </td>

                <td style = "padding: 0.40rem; text-align: center">
                  {{$itemorden->getFechaHoraCreacion()}}
                </td>
                <td style = "padding: 0.40rem; text-align: center">
                  {{$itemorden->getEmpleadoCreador()->getNombreCompleto()}}
                </td>
               
 

                <td style = "padding: 0.40rem">
                  [{{$itemorden->getProyecto()->codigoPresupuestal}}] {{$itemorden->getProyecto()->nombre}}
                </td>

                <td style = "padding: 0.40rem">
                  {{$itemorden->señores}}
                </td>

                <td style = "padding: 0.40rem">
                  {{$itemorden->getResumenDetalles()}}
                </td>

                <td style = "padding: 0.40rem">
                  {{$itemorden->getMoneda()->simbolo}} {{number_format($itemorden->total,2)}}
                </td>


                <td>
                  
                  @if($itemorden->sePuedeEditar() && $itemorden->codEmpleadoCreador == App\Empleado::getEmpleadoLogeado()->codEmpleado)
                    <a href="{{route('OrdenCompra.Empleado.Editar',$itemorden->codOrdenCompra)}}"
                      class="btn btn-warning btn-xs" title="Editar Requerimiento">
                      <i class="fas fa-edit"></i>
                    </a>
                  @endif

                  <a href="{{route('OrdenCompra.Empleado.Ver',$itemorden->codOrdenCompra)}}"
                    class="btn btn-info btn-xs" title="Ver Orden Compra">
                    <i class="fas fa-eye"></i>
                  </a>

                  <a href="{{route('OrdenCompra.descargarPDF',$itemorden->codOrdenCompra)}}" 
                    class='btn btn-info btn-xs' title="Descargar PDF">
                    <i class="fas fa-file-download"></i>
                  </a>
                  
                  <a target="pdf_ordenCompra_{{$itemorden->codOrdenCompra}}" href="{{route('OrdenCompra.verPDF',$itemorden->codOrdenCompra)}}" 
                    class='btn btn-info btn-xs'  title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                  </a>
                
                </td>
            </tr>
        @endforeach
      </tbody>
    </table>
    {{$ordenes->appends(
      ['fechaInicio'=>$fechaInicio, 
      'fechaFin'=>$fechaFin]
                      )
      ->links()
    }}
</div>
@endsection