@extends ('Layout.Plantilla')
@section('titulo')
Mis Gastos
@endsection
@section('estilos')
<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">
@endsection
@section('contenido')

<div style="text-align: center">
  <h3> Resumen de mis gastos en rendiciones </h3>
    @include('Layout.MensajeEmergenteDatos')

    <table class="table" style="font-size: 10pt; margin-top:10px; ">
            <thead class="thead-dark">
              <tr>
                
                <th>Rendición</th>
                <th scope="col" style="text-align: center">Fecha</th>
                
                <th  scope="col">N° Comp</th>
                <th >Concepto</th>
                <th >Monto</th>

                <th  scope="col" style="text-align: center">Tipo</th>
                <th>Estado de la Rendicion</th>
                <th  scope="col" style="text-align: center">Contabilizado</th>
                
                <th scope="col">Opciones</th>
                
              </tr>
            </thead>
      <tbody>

        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($listaGastos as $itemGasto)

      
            <tr>
                <td>{{$itemGasto->getRendicion()->codigoCedepas}}</td>
                <td style = "padding: 0.40rem; text-align: center">{{$itemGasto->fecha}}</td>
              
                <td style = "padding: 0.40rem"> {{$itemGasto->nroComprobante}} </td>
                <td style = "padding: 0.40rem">{{$itemGasto->concepto  }}</td>
                
                <td style = "padding: 0.40rem">{{$itemGasto->getRendicion()->getMoneda()->simbolo}} {{number_format($itemGasto->importe,2)  }}</td>
                <td style = "padding: 0.40rem; text-align: right"> 
                    {{$itemGasto->getNombreTipoCDP()}}
                </td>

                <td style = "padding: 0.40rem; text-align: center">
                    <input type="text" value="{{$itemGasto->getRendicion()->getNombreEstado()}}" class="form-control" readonly 
                    style="background-color: {{$itemGasto->getRendicion()->getColorEstado()}};
                            height: 26px;
                            text-align:center;
                            color: {{$itemGasto->getRendicion()->getColorLetrasEstado()}} ;
                    " title="{{$itemGasto->getRendicion()->getMensajeEstado()}}">
                </td>

                <td style = "padding: 0.40rem; text-align: center {{$itemGasto->getEstadoDeAlerta()}}">
                  {{$itemGasto->getContabilizado()}}
                </td>
                
                
                <td style = "padding: 0.40rem">
                      {{-- Si la tenemos que evaluar --}}  
                    <a href="{{route('RendicionGastos.Empleado.Ver',$itemGasto->codRendicionGastos)}}" 
                        class='btn btn-info btn-sm' title="Ver Rendicion">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>

            </tr>
        @endforeach
      </tbody>
    </table>

  {{$listaGastos->links()}}
 

</div>
@endsection


<?php 
  $fontSize = '14pt';
?>
<style>
/* PARA COD ORDEN CON CIRCULITOS  */

   </style>
@section('script')  
<script src="/select2/bootstrap-select.min.js"></script>     
 <script src="/calendario/js/bootstrap-datepicker.min.js"></script>
 <script src="/calendario/locales/bootstrap-datepicker.es.min.js"></script>
@endsection