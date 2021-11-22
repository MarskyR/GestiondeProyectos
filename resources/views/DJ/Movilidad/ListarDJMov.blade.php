@extends ('Layout.Plantilla')
@section('titulo')
  DJ MOV
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
  <h2> Listar mis DJ por Movilidad </h2>
  


  <br>
    
    <div class="row">
      <div class="col-md-2">
        <a href="{{route('DJMovilidad.Empleado.Crear')}}" class = "btn btn-primary" style="margin-bottom: 5px;"> 
          <i class="fas fa-plus"> </i> 
            Nueva DJ Movilidad
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
    


{{-- AQUI FALTA EL CODIGO SESSION DATOS ENDIF xdd --}}
    @include('Layout.MensajeEmergenteDatos')
      
    <table class="table" style="font-size: 10pt; margin-top:10px;">
            <thead class="thead-dark">
              <tr>
                <th>Código </th>
                <th style="text-align: center">Fecha Creación</th>
                <th>Importe.</th>
                <th>Resumen</th>
                <th>Opciones</th>
                
              </tr>
            </thead>
      <tbody>
        


        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($listaDJ as $itemDJ)
            <tr>
                <td style = "padding: 0.40rem">{{$itemDJ->codigoCedepas  }}</td>
                <td style = "padding: 0.40rem; text-align: center">{{$itemDJ->getFechaHoraCreacion()}}</td>
                <td style = "padding: 0.40rem">{{$itemDJ->getMontoConMoneda() }}</td>
                
                <td style = "padding: 0.40rem">
                  {{$itemDJ->getDetallesAbreviados()}}

                </td>
                <td>
                  <a href="{{route('DJMovilidad.Empleado.ver',$itemDJ->codDJGastosMovilidad)}}" class='btn btn-info btn-sm' title="Descargar PDF">
                    <i class="fas fa-eye"></i>
                  </a>
                  
                  
                  <a href="{{route('DJMovilidad.Empleado.descargarPDF',$itemDJ->codDJGastosMovilidad)}}" class='btn btn-info btn-sm' title="Descargar PDF">
                    <i class="fas fa-file-download"></i>
                  </a>
                  <a target="pdf_djmov{{$itemDJ->codDJGastosMovilidad}}" href="{{route('DJMovilidad.Empleado.verPDF',$itemDJ->codDJGastosMovilidad)}}" class='btn btn-info btn-sm'  title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                  </a>

                </td>

              

            </tr>
        @endforeach
      </tbody>
    </table>

  {{$listaDJ->appends(
    ['fechaInicio'=>$fechaInicio, 
    'fechaFin'=>$fechaFin]
                    )
    ->links()
  }}

</div>
@endsection




<?php 
  $fontSize = '14pt';
?>


@section('script')
{{-- 
  //Este código es para abrir una ventana emergente al terminar de crear la DJ, Sí funciona pero al activarlo el antivirus/chrome bloquea la ventana emergente
        
@ if($nuevaDJ!="0")
    <script>
        url = "/DJ/GastosMovilidad/{ { $nuevaDJ}}/ver";
        var win = window.open(url, '_blank');
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();


    </script>

@endif
 --}}

@endsection