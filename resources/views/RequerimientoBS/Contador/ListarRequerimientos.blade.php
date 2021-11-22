@extends ('Layout.Plantilla')

@section('titulo')
  Listar Requerimientos
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
  <h3> Requerimientos de Bienes y Servicios </h3>
  
  <br>
  <div class="row">
    <div class="col-md-12">
      <form class="form-inline float-left">
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
          &nbsp; Proyecto:
          
        </label>

        <select class="form-control form-control-sm mr-sm-2"  id="codProyectoBuscar" name="codProyectoBuscar" style="margin-left: 10px;width: 300px;">
          <option value="0">--Seleccionar--</option>
          @foreach($proyectos as $itemproyecto)
              <option value="{{$itemproyecto->codProyecto}}" {{$itemproyecto->codProyecto==$codProyectoBuscar ? 'selected':''}}>
                [{{$itemproyecto->codigoPresupuestal}}] {{$itemproyecto->nombre}}
              </option>                                 
          @endforeach 
        </select>
        <select class="form-control form-control-sm mr-sm-2"  id="filtroTieneFactura" name="filtroTieneFactura" style="">
          <option value="-1">- Tiene Factura -</option> {{-- No tomar en cuenta el filtro --}}
          {{--  <option value="NoRev">No Revisado</option> PARA filtrar las que están como no revisadas  AQUI EN CONTABILIDAD SIEMPRE LLEGAN SI O NO--}}
          <option value="0">No Tiene</option> {{-- Para filtrar las que NO tienen  factura (tieneFactura=0) --}}
          <option value="1">Sí Tiene</option> {{-- Para filtrar las que SÍ tienen factura (tieneFactura=1) --}}
        </select>

          


        <select class="form-control form-control-sm mr-sm-2"  id="filtroFacturaContabilizada" name="filtroFacturaContabilizada" style="">
          <option value="-1">- Estado de factura -</option> {{-- No tomar en cuenta el filtro --}}
          {{--  <option value="NoRev">No Revisado</option> PARA filtrar las que están como no revisadas  AQUI EN CONTABILIDAD SIEMPRE LLEGAN SI O NO--}}
          <option value="1">Factura Contabilizada</option> {{-- Para filtrar las que NO tienen  factura (tieneFactura=0) --}}
          <option value="0">Factura No Contabilizada</option> {{-- Para filtrar las que SÍ tienen factura (tieneFactura=1) --}}
        </select>

      

        

        <button class="btn btn-success btn-sm" type="submit">
          Buscar
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>
  </div>
  
  
    

{{-- AQUI FALTA EL CODIGO SESSION DATOS ENDIF xdd --}}
    @include('Layout.MensajeEmergenteDatos')

    <table class="table table-hover" style="font-size: 10pt; margin-top:10px;">
            <thead class="thead-dark">
              <tr>
                <th width="11%" scope="col">Cod. Requerimiento</th> {{-- COD CEDEPAS --}}
                <th width="11%"  scope="col" style="text-align: center">F. Emisión</th>
                <th width="11%"  scope="col" style="text-align: center">F. Atención</th>
                
                <th width="11%" scope="col">Solicitante</th>
                
                <th width="19%">Origen & Proyecto</th>              
                <th width="20%">Justificacion</th>              
                
                <th width="11%"  scope="col" style="text-align: center">Estado</th>
                <th width="3%" class="text-center"  scope="col"  >Factura / Contabilizada</th>
                
                <th width="9%"  scope="col">Opciones</th>
                
              </tr>
            </thead>
      <tbody>

        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($requerimientos as $itemRequerimiento)

      
            <tr>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->codigoCedepas  }}</td>
               
              <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraEmision()}}</td>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraRevisionAdmin()}}</td>
              

              <td style = "padding: 0.40rem">{{$itemRequerimiento->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
              
              <td style = "padding: 0.40rem">[{{$itemRequerimiento->getProyecto()->codigoPresupuestal}}] {{$itemRequerimiento->getProyecto()->nombre  }}</td>
              <td style="padding:0.40rem">{{$itemRequerimiento->getJustificacionAbreviada()}}</td>
              
              <td style="text-align: center; padding: 0.40rem">
                
                <input type="text" value="{{$itemRequerimiento->getNombreEstado()}}" class="form-control" readonly 
                style="background-color: {{$itemRequerimiento->getColorEstado()}};
                        height: 26px;
                        text-align:center;
                        color: {{$itemRequerimiento->getColorLetrasEstado()}} ;
                "  title="{{$itemRequerimiento->getMensajeEstado()}}">
              </td>
              <td style="padding:0.40rem" class="text-center">
                <b style="color: {{$itemRequerimiento->getColorSiTieneFactura()}}">
                  {{$itemRequerimiento->getSiTieneFactura()}}
                </b>
                /
                <b style="color: {{$itemRequerimiento->getColorFacturaContabilizada()}}">
                  {{$itemRequerimiento->getFacturaContabilizada()}}
                </b>
                
              </td>
              
                <td style = "padding: 0.40rem">

                    @if($itemRequerimiento->codEstadoRequerimiento==3)
                      <a href="{{route('RequerimientoBS.Contador.ver',$itemRequerimiento->codRequerimiento)}}" class="btn btn-warning btn-sm" title="Contabilizar Reposición">
                        <i class="fas fa-hand-holding-usd"></i>
                      </a>
                    @else
                      <a href="{{route('RequerimientoBS.Contador.ver',$itemRequerimiento->codRequerimiento)}}" class="btn btn-info btn-sm" title="Ver Reposición">
                        <i class="fas fa-eye"></i>
                      </a>
                    @endif
                      <a  href="{{route('RequerimientoBS.exportarPDF',$itemRequerimiento->codRequerimiento)}}" 
                        class="btn btn-primary btn-sm" title="Descargar PDF">
                        <i class="fas fa-file-download"></i>
                      </a>
    
                    <a target="pdf_reposicion_{{$itemRequerimiento->codRequerimiento}}" href="{{route('RequerimientoBS.verPDF',$itemRequerimiento->codRequerimiento)}}" 
                      class="btn btn-primary btn-sm" title="Ver PDF">
                      <i class="fas fa-file-pdf"></i>
                    </a>
                    
                </td>

            </tr>
        @endforeach
      </tbody>
    </table>
    {{$requerimientos->appends(
      ['codEmpleadoBuscar'=>$codEmpleadoBuscar, 
      'fechaInicio'=>$fechaInicio, 
      'fechaFin'=>$fechaFin,
      'codProyectoBuscar'=>$codProyectoBuscar,
      'filtroTieneFactura'=>$filtroTieneFactura,
      'filtroFacturaContabilizada' => $filtroFacturaContabilizada]
                      )
      ->links()
    }}
      
</div>
@endsection
@section('script')
  
  <script>
    $(document).ready(function(){
      cargarFiltros();

    });

    function cargarFiltros(){

        /* Cargar en el filtro Select de factura el valor de la busqueda  */
        selectFactura = document.getElementById('filtroTieneFactura');
        selectFContabilizada = document.getElementById('filtroFacturaContabilizada');

        var tieneFactura = "{{$filtroTieneFactura}}";
        var facturaContabilizada = "{{$filtroFacturaContabilizada}}";
        //console.log(tieneFactura);
        if(tieneFactura==""){//no usé el filtro
          tieneFactura = "-1";
        }

        //console.log(tieneFactura);

        selectFContabilizada.value= facturaContabilizada;
        selectFactura.value = tieneFactura;
          


    }
    </script>


@endsection

<?php 
  $fontSize = '14pt';
?>
