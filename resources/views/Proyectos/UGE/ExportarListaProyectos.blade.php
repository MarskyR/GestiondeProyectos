@php

  $descargarExcel = true;

  if($descargarExcel){
    $filename = "Listado de proyectos - Cedepas.xls";
    header("Pragma: public");
    header("Expires: 0");
   
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
  
  }
@endphp

{{-- para que las tildes no se bugeen --}}
<meta charset="utf-8">

 
<table>
    <thead>
        <tr>
          <th colspan="9" style="font-size: large; color:red">FILTROS</th>
        </tr>
        <tr>
            <th colspan="2" style="font-size: medium">Nombre de Proyecto:</th>
            <th colspan="1" style="font-size: medium">Sede:</th>    
            <th colspan="1" style="font-size: medium">Entidad Financiera:</th>   
            <th colspan="4" style="font-size: medium">Tipo de Financiamiento:</th>
            <th colspan="2" style="font-size: medium">Años:</th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2" style="font-size: small;font-weight: normal">{{$nombreProyectoBuscar}}</th>
            <th colspan="1" style="font-size: small;font-weight: normal">{{$codSedeBuscar}}</th>    
            <th colspan="1" style="font-size: small;font-weight: normal">{{$codEntidadFinancieraBuscar}}</th>   
            <th colspan="4" style="font-size: small;font-weight: normal">{{$codTipoFinanciamientoBuscar}}</th>
            <th colspan="2" style="font-size: small;font-weight: normal">{{$años}}</th>    
        </tr>
        <tr></tr>
    </tbody>
</table>

<table class="table table-sm" border="1">
    <thead>
      <tr>
        <th scope="col">Cod Proy</th>

        <th scope="col">NOMBRE PROYECTO</th>
        <th scope="col">NOMBRE LARGO</th>
        <th scope="col">OBJETIVO</th>
        <th>Fecha Inicio</th>
        <th>Fecha Fin</th>
        <th>Dias</th>
        <th>Meses</th>
        <th>Años</th>
     
        <th>Moneda</th>

        <th>I. Contrapartida Cedepas</th>
        <th>I. Contrapartida PB</th>
        <th>I. Contrapartida Otros</th>
        <th>I. Financiamiento</th>

        <th>I. Presupuesto Total</th>

        <th>Tipo Financiamiento</th>
        <th>Entidad Financiera</th>
        <th>Nombre Contacto</th>
        <th>Teléfono Contacto</th>
        <th>Correo contacto</th>
        


        <th>Sede</th>
        <th>PEI</th>
        <th>Objetivos PEI</th>
        <th>Estado</th>
        <th>Gerente</th>
        <th>Descargar reporte (copiar link)</th>
        
      </tr>
    </thead>
    <tbody>

      @foreach($listaProyectos as $itemProyecto)
        
      
      <tr>
        <td>{{$itemProyecto->codigoPresupuestal}}</td>
        
        <td>{{$itemProyecto->nombre}}</td>
        <td>{{$itemProyecto->nombreLargo}}</td>
        <td>{{$itemProyecto->objetivoGeneral}}</td>
        <td>{{$itemProyecto->getFechaInicio()}}</td>
        <td>{{$itemProyecto->getFechaFinalizacion()}}</td>
        <td>{{$itemProyecto->getDuracion('dias',false)}}</td>
        <td>{{$itemProyecto->getDuracion('meses',false)}}</td>
        <td  >{{$itemProyecto->getDuracion('años',false)}}</td>
        

        <td>{{$itemProyecto->getMoneda()->nombre}}</td>

        <td>{{number_format($itemProyecto->importeContrapartidaCedepas)}}</td>
        <td>{{number_format($itemProyecto->importeContrapartidaPoblacionBeneficiaria)}}</td>
        <td>{{number_format($itemProyecto->importeContrapartidaOtros)}}</td>
        <td>{{number_format($itemProyecto->importeFinanciamiento)}}</td>

        <td>{{number_format($itemProyecto->importePresupuestoTotal)}}</td>

        <td>{{$itemProyecto->getTipoFinanciamiento()->nombre}}</td>
        <td>{{$itemProyecto->getEntidadFinanciera()->nombre}}</td>
        <td>{{$itemProyecto->contacto_nombres}}</td>
        <td>{{$itemProyecto->contacto_telefono}}</td>
        <td>{{$itemProyecto->contacto_correo}}</td>
        

        <td>{{$itemProyecto->getSede()->nombre}}</td>
        <td>{{$itemProyecto->getPEI()->getPeriodo()}}</td>
        <td>{{$itemProyecto->getObjPEIs()}}</td>
        
        <td>{{$itemProyecto->getEstado()->nombre}}</td>

        <td>{{$itemProyecto->getNombreCompletoGerente()}}</td>
        <td>
          <a style="color:green" href="">
            {{route('GestionProyectos.ExportarModeloMarcoLogico',$itemProyecto->codProyecto)}}
          </a>

        </td>
      </tr>

      @endforeach

    </tbody>
</table>

<table>
  <tbody>
      <tr>
          <th colspan="9" style="font-style: oblique;font-weight: normal;text-align: left">
            * Reporte generado el {{date("d/m/Y")}} 
            por el usuario 
            {{$empleado->getNombreCompleto()}}
             mediante el 
            <b>
              Sistema Web de Gestión de Cedepas Norte

            </b>
          </th> 
      </tr>
  </tbody>
</table>