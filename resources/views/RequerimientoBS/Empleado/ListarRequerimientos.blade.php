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
  <h3> Mis Requerimientos de Bienes y Servicios </h3>
  
  <br>
  <div class="row">
    <div class="col-md-2">
      <a href="{{route('RequerimientoBS.Empleado.CrearRequerimientoBS')}}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Registro
      </a>
    </div>
    <div class="col-md-10">
      <form class="form-inline float-right">

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
                <th width="6%"  scope="col" style="text-align: center">F. Revisión</th>
               {{--  <th width="6%"  scope="col" style="text-align: center">F. Atención</th> --}}
                <th width="6%"  scope="col" style="text-align: center">Gerente/Director/a</th>
                 
                <th  scope="col">Origen & Proyecto</th>         
                <th>Justificacion</th>     
                <th width="11%"  scope="col" style="text-align: center">Estado</th>
                <th width="13%"  scope="col">Opciones</th>
                
              </tr>
            </thead>
      <tbody>

        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($requerimientos as $itemRequerimiento)

      
            <tr>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->codigoCedepas  }}</td>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraEmision()}}</td>
              <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraRevisionGerente()}}</td>
          {{--     <td style = "padding: 0.40rem">{{$itemRequerimiento->formatoFechaHoraRevisionAdmin()}}</td> --}}
              <td style = "padding: 0.40rem">{{$itemRequerimiento->getNombreGerente()}}</td>
              
              <td style = "padding: 0.40rem">{{$itemRequerimiento->getProyecto()->getOrigenYNombre()  }}</td>
              
              <td>{{$itemRequerimiento->justificacion}}</td>
        
              <td style="text-align: center; padding: 0.40rem">
                
                <input type="text" value="{{$itemRequerimiento->getNombreEstado()}}" class="form-control" readonly 
                style="background-color: {{$itemRequerimiento->getColorEstado()}};
                        height: 26px;
                        text-align:center;
                        color: {{$itemRequerimiento->getColorLetrasEstado()}} ;
                "  title="{{$itemRequerimiento->getMensajeEstado()}}">
              </td>
              
              <td style = "padding: 0.40rem">       
                <a href="{{route('RequerimientoBS.Empleado.ver',$itemRequerimiento->codRequerimiento)}}" class="btn btn-info btn-sm" title="Ver Requerimiento" >
                    <i class="fas fa-eye"></i>
                </a>
                @if($itemRequerimiento->listaParaEditar())
                <a href="{{route('RequerimientoBS.Empleado.EditarRequerimientoBS',$itemRequerimiento->codRequerimiento)}}"
                  class="btn btn-warning btn-sm" title="Editar Requerimiento"><i class="fas fa-edit"></i></a>
                @endif
                @if($itemRequerimiento->codEstadoRequerimiento<3)
                <a href="#" class="btn btn-sm btn-danger" title="Cancelar Requerimiento" onclick="swal({//sweetalert
                    title:'¿Está seguro de cancelar el requerimiento: {{$itemRequerimiento->codigoCedepas}} ?',
                    //type: 'warning',  
                    type: 'warning',
                    showCancelButton: true,//para que se muestre el boton de cancelar
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText:  'SÍ',
                    cancelButtonText:  'NO',
                    closeOnConfirm:     true,//para mostrar el boton de confirmar
                    html : true
                },
                function(){//se ejecuta cuando damos a aceptar
                  window.location.href='{{route('RequerimientoBS.cancelar',$itemRequerimiento->codRequerimiento)}}';
                });"><i class="fas fa-trash-alt"> </i></a>
                @endif
              </td>

            </tr>
        @endforeach
      </tbody>
    </table>
    {{$requerimientos->appends(
      ['fechaInicio'=>$fechaInicio, 
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
