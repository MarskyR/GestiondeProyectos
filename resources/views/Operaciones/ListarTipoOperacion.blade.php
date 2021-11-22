@extends ('Layout.Plantilla')
@section('titulo')
  Listar Tipos de Operacion
@endsection
@section('contenido')
  

<div style="text-align: center">
  <h2> Listar tipos de Operacion</h2>
  <br>
   

    @include('Layout.MensajeEmergenteDatos')
      
    <table class="table table-sm" style="width:40%;">
      <thead class="thead-dark">
        <tr>
            <th>
                Código
            </th>
            <th style="text-align: center">
                Documento
            </th>
            <th>
                Nombre
            </th>
            
        </tr>
      </thead>
      <tbody>
        
        @foreach($lista as $tipo)
            <tr style="background-color: {{$tipo->getTipoDocumento()->getColor()}}">
                <td>
                    {{$tipo->codTipoOperacion}}
                </td>

                <td>
                    {{$tipo->getTipoDocumento()->abreviacion}}
                </td>

                <td>
                    {{$tipo->nombre}}
                </td>

 
            </tr>
        @endforeach
      </tbody>
    </table>
    
</div>
@endsection