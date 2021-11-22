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
  <h3> Aprobar Requerimientos de Bienes y Servicios </h3>
  
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

        <select class="form-control mr-sm-2"  id="codProyectoBuscar" name="codProyectoBuscar" style="margin-left: 10px;width: 300px;">
          <option value="0">--Seleccionar--</option>
          @foreach($proyectos as $itemproyecto)
              <option value="{{$itemproyecto->codProyecto}}" {{$itemproyecto->codProyecto==$codProyectoBuscar ? 'selected':''}}>
                [{{$itemproyecto->codigoPresupuestal}}] {{$itemproyecto->nombre}}
              </option>                                 
          @endforeach 
        </select>


        <button class="btn btn-success " type="submit">Buscar</button>
      </form>
    </div>
  </div>
  
  
    

{{-- AQUI FALTA EL CODIGO SESSION DATOS ENDIF xdd --}}
    @include('Layout.MensajeEmergenteDatos')

    <table class="table table-hover" style="font-size: 10pt; margin-top:10px;">
            <thead class="thead-dark">
              <tr>
                <th width="5%" scope="col">Cod. Requerimiento</th> {{-- COD CEDEPAS --}}
                <th width="6%"  scope="col" style="text-align: center">F. Emisión</th>
                <th width="6%"  scope="col" style="text-align: center">F. Aprobación</th>
                
                <th width="11%" scope="col">Solicitante</th>
                 
                <th width="19%">Origen & Proyecto</th>              
                <th width="20%">Justificacion</th>
                <th width="13%"  scope="col" style="text-align: center">Estado</th>
                <th width="9%"  scope="col">Opciones</th>
                
              </tr>
            </thead>
      <tbody>

        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($requerimientos as $itemRequerimiento)

      
            <tr>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->codigoCedepas  }}</td>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraEmision()}}</td>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraRevisionGerente()}}</td>
              
              <td style = "padding: 0.40rem">{{$itemRequerimiento->getEmpleadoSolicitante()->getNombreCompleto()}}</td>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->getProyecto()->getOrigenYNombre()  }}</td>
              <td style = "padding: 0.40rem"> {{$itemRequerimiento->getJustificacionAbreviada()}} </td>
              <td style="text-align: center; padding: 0.40rem">
                
                <input type="text" value="{{$itemRequerimiento->getNombreEstado()}}" class="form-control" readonly 
                style="background-color: {{$itemRequerimiento->getColorEstado()}};
                        height: 26px;
                        text-align:center;
                        color: {{$itemRequerimiento->getColorLetrasEstado()}} ;
                "  title="{{$itemRequerimiento->getMensajeEstado()}}">
              </td>
              
              <td style = "padding: 0.40rem">       
                @if($itemRequerimiento->verificarEstado('Creada') || $itemRequerimiento->verificarEstado('Subsanada')  )
                <a href="{{route('RequerimientoBS.Gerente.ver',$itemRequerimiento->codRequerimiento)}}" 
                  class="btn btn-warning btn-sm" title="Evaluar Reposición">
                  <i class="fas fa-thumbs-up"></i>
                </a>
                @else
                <a href="{{route('RequerimientoBS.Gerente.ver',$itemRequerimiento->codRequerimiento)}}"
                  class="btn btn-info btn-sm" title="Ver Reposición">
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
      'codProyectoBuscar'=>$codProyectoBuscar]
                      )
      ->links()
    }}
      
</div>
@endsection


<?php 
  $fontSize = '14pt';
?>
