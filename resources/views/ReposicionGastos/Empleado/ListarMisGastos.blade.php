@extends ('Layout.Plantilla')
@section('titulo')
Mis Gastos
@endsection
@section('estilos')
<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">

<style>
.pad04{
  padding: 0.40rem;
  background-color: rebeccapurple
}

</style>

@endsection
@section('contenido')

<div style="text-align: center">
  <h3> Resumen de mis gastos en reposiciones </h3>
    @include('Layout.MensajeEmergenteDatos')

    <table class="table table-sm table-hover fontSize10" style="">
            <thead class="thead-dark">
              <tr >
                
                <th class="pad04" >Reposicion</th>
                <th class="pad04" scope="col" style="text-align: center">Fecha</th>
                
                <th class="pad04"  scope="col">N° Comp</th>
                <th class="pad04" >Concepto</th>
                <th class="pad04" >Monto</th>

                <th class="pad04"  scope="col" style="text-align: center">Tipo</th>
                <th class="pad04">Estado de la Rendicion</th>
                <th class="pad04"  scope="col" style="text-align: center">Contabilizado</th>
                
                <th class="pad04" scope="col">Opciones</th>
                
              </tr>
            </thead>
      <tbody>

        {{--     varQuePasamos  nuevoNombre                        --}}
        @foreach($listaGastos as $itemGasto)

        
            <tr >
                <td>{{$itemGasto->getReposicion()->codigoCedepas}}</td>
                <td style = "text-align: center">{{$itemGasto->getFechaComprobante()}}</td>
              
                <td style = ""> {{$itemGasto->nroComprobante}} </td>
                <td style = "">{{$itemGasto->concepto  }}</td>
                
                <td style = "">{{$itemGasto->getReposicion()->getMoneda()->simbolo}} {{number_format($itemGasto->importe,2)  }}</td>
                <td style = "text-align: right"> 
                    {{$itemGasto->getNombreTipoCDP()}}
                </td>

                <td style = "text-align: center">
                    <input type="text" value="{{$itemGasto->getReposicion()->getNombreEstado()}}" class="form-control" readonly 
                    style="background-color: {{$itemGasto->getReposicion()->getColorEstado()}};
                            height: 26px;
                            text-align:center;
                            color: {{$itemGasto->getReposicion()->getColorLetrasEstado()}} ;
                    " title="{{$itemGasto->getReposicion()->getMensajeEstado()}}">
                </td>

                <td style = "text-align: center; {{$itemGasto->getEstadoDeAlerta()}}">
                  {{$itemGasto->getContabilizado()}}
                </td>
                
                
                <td style = "">
                      {{-- Si la tenemos que evaluar --}}  
                    <a href="{{route('ReposicionGastos.Empleado.ver',$itemGasto->codReposicionGastos)}}" 
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