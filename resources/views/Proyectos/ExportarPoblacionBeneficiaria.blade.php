<?php 

  $descargarExcel = true;
  $fondoPlomo = "background-color: #D0CECE;";

  $filename = "Reporte de población beneficiaria - Proyecto ".$proyecto->nombre.".xls";
  
  if($descargarExcel){
    header("Pragma: public");
    header("Expires: 0");
    
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    
  }
?>
<meta charset="utf-8">

<table>
  <thead>
      <tr>
          <th colspan="15" style="font-size: large">Proyecto:</th>
      </tr>
  </thead>
  <tbody>
      <tr>
          <th colspan="15" style="font-size: medium;font-weight: normal">{{$proyecto->nombre}}</th>   
      </tr>
  </tbody>
</table>


@php
  $listaPersonasNaturalesSinRepetidas = $proyecto->getPersonasNaturalesSinRepetidas();
  $listaPersonasJuridicasSinRepetidas = $proyecto->getPersonasJuridicasSinRepetidas();
  $listaMujeres = $proyecto->getMujeresSinRepetir();
  $listaHombres = $proyecto->getHombresSinRepetir();
  
@endphp
{{-- RESUMEN ESTADÍSTICO --}}
<table border="1">
  <thead>
      <tr>
          <th colspan="4" style="font-size: large">
            Resumen Estadístico:
          </th>
      </tr>
      <tr>
        <th>
          #Mujeres
        </th>
        <th>
          #Hombres
        </th>
        <th>
          # P. Naturales:
        </th>
        <th>
          # P. Jurídicas
        </th>
      </tr>
  </thead>
  <tbody>
      
      <tr>
        <td>
          {{count($listaMujeres)}}
        </td>
        <td>
          {{count($listaHombres)}}
        </td>
        <td>
          {{count($listaPersonasNaturalesSinRepetidas)}}
        </td>
        <td>
          {{count($listaPersonasJuridicasSinRepetidas)}}
        </td>
        
      </tr>

  </tbody>
</table>

<table>
  <thead>
      <tr>
          <th colspan="13" style="font-size: large">
             Listado neto de personas (sin repetidas):
          </th>
      </tr>
  </thead>
</table>
<br>

{{-- LISTADO DE PERSONAS SIN REPETIR --}}
<table border="1">
  <thead>
      <tr>
          <th colspan="13"  style="font-size:small">
            Personas naturales sin repetir
          </th>
      </tr>
      <tr>
        <th  style="{{$fondoPlomo}}">#</th>
        <th  style="{{$fondoPlomo}}">DNI</th>
        <th  style="{{$fondoPlomo}}">Nombres</th>
        <th  style="{{$fondoPlomo}}">Apellidos</th>
        <th  style="{{$fondoPlomo}}">Sexo</th>
        <th  style="{{$fondoPlomo}}">Telefono</th>
        <th  style="{{$fondoPlomo}}">Fecha de Nacimiento</th>
        <th  style="{{$fondoPlomo}}">Edad Registrada</th>
        <th  style="{{$fondoPlomo}}">Actividades</th>

        <th  style="{{$fondoPlomo}}" colspan="4">Direccion</th>


      </tr>
  </thead>
  <tbody>
      @foreach ($listaPersonasNaturalesSinRepetidas as $i=>$pnatural)
          
        <tr>
          <th >{{$i+1}}</th>
          <td >{{$pnatural->dni}}</td>
          <td >{{$pnatural->nombres}}</td>
          <td >{{$pnatural->apellidos}}</td>
          <td >{{$pnatural->sexo}}</td>
          <td >{{$pnatural->nroTelefono}}</td>
          <td >{{$pnatural->fechaNacimiento}}</td>
          <td >{{$pnatural->edadMomentanea}}</td>
          <td >{{$pnatural->getResumenActividades()}}</td>
          <td colspan="4" >{{$pnatural->direccion}}</td>
        </tr>

      @endforeach
  </tbody>
</table>
<br>
<br>
{{-- LISTADO DE PERSONAS JURIDICAS SIN REPETIR --}}
<table border="1">
  <thead>
      <tr>
          <th colspan="13" style="font-size:small">
            Personas Jurídicas sin repetir
          </th>
      </tr>
      <tr>
        <th  style="{{$fondoPlomo}}">#</th>
        <th  style="{{$fondoPlomo}}">RUC</th>
        <th  style="{{$fondoPlomo}}"># Socios</th>
        <th  style="{{$fondoPlomo}}"># Socias</th>
        <th  style="{{$fondoPlomo}}">Tipo</th>
        <th  style="{{$fondoPlomo}}" colspan="3">Razon Social</th>
        <th  style="{{$fondoPlomo}}">Actividades</th>
        <th  style="{{$fondoPlomo}}" colspan="4">Direccion</th>


      </tr>
  </thead>
  <tbody>
      @foreach ($listaPersonasJuridicasSinRepetidas as $i=>$itempersona)
          
        <tr>
          <td >{{$i+1}}</td>
          <td >{{$itempersona->ruc}}</td>
          <td >{{$itempersona->numeroSociosHombres}}</td>
          <td >{{$itempersona->numeroSociosMujeres}}</td>
          <td>{{$itempersona->getTipologia()->siglas}}</td>
          <td colspan="3" >{{$itempersona->razonSocial}}</td>
          <td >{{$itempersona->getResumenActividades()}}</td>
          <td colspan="4" >{{$itempersona->direccion}}</td>
  
        </tr>

      @endforeach
  </tbody>
</table>

<br>
<br>

<table>
  <thead>
      <tr>
          <th colspan="13" style="font-size: large">
             Listado por poblaciones beneficiarias:
          </th>
      </tr>
  </thead>
</table>
 


@include('Proyectos.ExportarPobBeneficiaria-Tablas')


