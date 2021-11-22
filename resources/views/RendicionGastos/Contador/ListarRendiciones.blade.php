@extends ('Layout.Plantilla')
@section('titulo')
Listar Rendiciones
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
  
<div>
  <h3> Rendiciones de Gastos a Contabilizar </h3>

  <br>
  <div class="row">
    <div class="col-md-12">
      <form class="form-inline">
        <label for="">Colaborador: </label>
        <select class="form-control select2 select2-hidden-accessible selectpicker" data-select2-id="1" tabindex="-1" aria-hidden="true" id="codEmpleadoBuscar" name="codEmpleadoBuscar" data-live-search="true">
          <option value="0">- Seleccione Colaborador -</option>          
          @foreach($empleados as $itemempleado)
            <option value="{{$itemempleado->codEmpleado}}" {{$itemempleado->codEmpleado==$codEmpleadoBuscar ? 'selected':''}}>{{$itemempleado->getNombreCompleto()}}</option>                                 
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
        <label style="" for="">
         &nbsp; Proyectos:
          
        </label>
        <select class="form-control mr-sm-2"  id="codProyectoBuscar" name="codProyectoBuscar" style="margin-left: 10px;width: 300px;">
          <option value="0">--Seleccionar Proyecto--</option>
          @foreach($proyectos as $itemproyecto)
              <option value="{{$itemproyecto->codProyecto}}" {{$itemproyecto->codProyecto==$codProyectoBuscar ? 'selected':''}}>
               [{{$itemproyecto->codigoPresupuestal}}]  {{$itemproyecto->nombre}}
              </option>                                 
          @endforeach 
        </select>
        <button class="btn btn-success " type="submit">Buscar</button>
       


      </form>
    </div>
  </div>
  

{{-- AQUI FALTA EL CODIGO SESSION DATOS ENDIF xdd --}}
    @include('Layout.MensajeEmergenteDatos')

    <table class="table table-hover" style="font-size: 10pt; margin-top:10px; ">
            <thead class="thead-dark">
              <tr>
                <th width="9%" scope="col">Cod. Rendición</th> {{-- COD CEDEPAS --}}
                <th width="9%"  scope="col" style="text-align: center">F. Rendición</th>
                
                <th width="13%"  scope="col">Colaborador </th>
                
                <th scope="col">Origen & Proyecto</th>              
                <th width="9%"  scope="col" style="text-align: center">Total Recibido</th>
                <th width="9%"  scope="col" style="text-align: center">Total Gastado</th>
                <th width="9%"  scope="col" style="text-align: center">Saldo</th>
                <th width="11%"  scope="col" style="text-align: center">Estado</th>
                <th width="5%"  scope="col">Opciones</th>
                
              </tr>
            </thead>
      <tbody>

        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($listaRendiciones as $itemRendicion)

      
            <tr>
              <td style = "padding: 0.40rem">{{$itemRendicion->codigoCedepas  }}</td>
              <td style = "padding: 0.40rem; text-align: center">{{$itemRendicion->formatoFechaHoraRendicion()}}</td>
         
              <td style = "padding: 0.40rem">{{$itemRendicion->getNombreSolicitante()  }}</td>
              <td style = "padding: 0.40rem">{{$itemRendicion->getProyecto()->getOrigenYNombre()  }}</td>
 
              <td style = "padding: 0.40rem; text-align: right">{{$itemRendicion->getMoneda()->simbolo}} {{number_format($itemRendicion->totalImporteRecibido,2)  }}</td>
              <td style = "padding: 0.40rem; text-align: right">{{$itemRendicion->getMoneda()->simbolo}} {{number_format($itemRendicion->totalImporteRendido,2)  }}</td>
              <td style = "padding: 0.40rem; text-align: right;  color: {{$itemRendicion->getColorSaldo()}}">
                {{$itemRendicion->getMoneda()->simbolo}} {{number_format($itemRendicion->getSaldoFavorCedepas(),2)  }}
              </td>
        
              <td style = "padding: 0.40rem; text-align: center">
                <input type="text" value="{{$itemRendicion->getNombreEstado()}}" class="form-control" readonly 
                style="background-color: {{$itemRendicion->getColorEstado()}};
                        height: 26px;
                        text-align:center;
                        color: {{$itemRendicion->getColorLetrasEstado()}} ;
                " title="{{$itemRendicion->getMensajeEstado()}}">
              </td>
              <td style = "padding: 0.40rem">        
                    
                      @if($itemRendicion->verificarEstado('Aprobada') )
                        <a href="{{route('RendicionGastos.Contador.verContabilizar',$itemRendicion->codRendicionGastos)}}" 
                          class='btn btn-warning btn-sm' title="Contabilizar Rendición"><i class="fas fa-hand-holding-usd"></i>
                        </a>   
                      @else {{-- Ya está contabilizada --}}
                        <a href="{{route('RendicionGastos.Contador.verContabilizar',$itemRendicion->codRendicionGastos)}}" 
                          class='btn btn-info btn-sm' title="Ver Rendición"><i class="fas fa-eye"></i>
                        </a>   
                      @endif
                      <a class='btn btn-info btn-sm'  href="{{route('rendicionGastos.descargarPDF',$itemRendicion->codRendicionGastos)}}" title="Descargar PDF">
                        <i class="fas fa-file-download"></i>
                      </a>
                      <a target="pdf_rendicion_{{$itemRendicion->codRendicionGastos}}" class='btn btn-info btn-sm'  
                        href="{{route('rendicionGastos.verPDF',$itemRendicion->codRendicionGastos)}}" title="Ver PDF">
                        <i class="fas fa-file-pdf"></i>
                      </a>

                  
              </td>

            </tr>
        @endforeach
      </tbody>
    </table>

    {{$listaRendiciones->appends(
      ['codEmpleadoBuscar'=>$codEmpleadoBuscar,
      'fechaInicio'=>$fechaInicio,
      'fechaFin'=>$fechaFin,
      'codProyectoBuscar'=>$codProyectoBuscar]
                      )
      ->links()
    }}


</div>
@endsection


<?php 
  $fontSize = '14pt';
?>
<style>
/* PARA COD ORDEN CON CIRCULITOS  */

  span.grey {
    background: #000000;
    border-radius: 0.8em;
    -moz-border-radius: 0.8em;
    -webkit-border-radius: 0.8em;
    color: #fff;
    display: inline-block;
    font-weight: bold;
    line-height: 1.6em;
    margin-right: 15px;
    text-align: center;
    width: 1.6em; 
    font-size : {{$fontSize}};
  }
  


  span.red {
  background:#932425;
   border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ffffff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 15px;
  text-align: center;
  width: 1.6em; 
  font-size : {{$fontSize}};
}


span.green {
  background: #5EA226;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ffffff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 15px;
  text-align: center;
  width: 1.6em; 
  font-size : {{$fontSize}};
}

span.blue {
  background: #5178D0;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ffffff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 15px;
  text-align: center;
  width: 1.6em; 
  font-size : {{$fontSize}};
}

span.pink {
  background: #EF0BD8;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ffffff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 15px;
  text-align: center;
  width: 1.6em; 
  font-size : {{$fontSize}};
}
   </style>
