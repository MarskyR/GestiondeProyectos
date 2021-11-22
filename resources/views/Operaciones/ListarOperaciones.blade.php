@extends ('Layout.Plantilla')
@section('titulo')
  Listar operaciones
@endsection
@section('contenido')
  

<div style="text-align: center">
  <h2> Listar operaciones</h2>
  <br>
   

    @include('Layout.MensajeEmergenteDatos')
      
    <table class="table table-sm" style="">
      <thead class="thead-dark">
        <tr>
            <th>
                Código
            </th>
            <th style="text-align: center">
                Tipo Documento
            </th>
            <th>
                Codigo documento
            </th>
            <th>
               Fecha hora
            </th>

            <th>
              Accion
            </th>
            <th>
              Puesto
            </th>
            <th>
              Empleado
            </th>

        </tr>
      </thead>
      <tbody>
        
        @foreach($listaOperaciones as $operacion)
            <tr style="background-color: {{$operacion->getColorFondo()}}">
              <td>
                {{$operacion->codOperacionDocumento}}
              </td>
              <td>
                {{$operacion->getTipoDocumento()->abreviacion}}
              </td>
              <td>
                {{$operacion->getDocumento()->codigoCedepas}}
              </td>
              <td>
                {{$operacion->getFechaHora()}}
              </td>
              <td>
                {{$operacion->getTipoOperacion()->nombre}}
                @if($operacion->getTipoOperacion()->nombre == "Observar")
                    <button type="button" class="btn btn-primary btn-xs" 
                      onclick="alertaMensaje('Razón de la observación',`{{$operacion->descripcionObservacion}}`,'info')">
                      <i class="fas fa-eye"></i>
                    </button>
                @endif
              </td>
              <td>
                {{$operacion->getPuesto()->nombre}}
              </td>
              <td>
                {{$operacion->getEmpleado()->getNombreCompleto()}}
              </td>

 
            </tr>
        @endforeach
      </tbody>
    </table>
    {{$listaOperaciones->links()}}
</div>
@endsection