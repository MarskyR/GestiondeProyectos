@extends('Layout.Plantilla') 

@section('titulo')
    Listado de Proyectos
@endsection

@section('contenido')
<br>

@include('Layout.MensajeEmergenteDatos')
<h1>
  Mis Proyectos

</h1>

<div class="card">
    <div class="card-header" style=" ">
      <h3 class="card-title">PROYECTOS</h3>
      <div class="card-tools">
        <ul class="nav nav-pills ml-auto">
          <li class="nav-item">
              <!--
            <a href="{{route('GestiónProyectos.crear')}}" class="nav-link active"><i class="fas fa-plus"></i> Nuevo Registro</a>
              -->
          </li>
        </ul>
      </div>
      
    </div>


    

    <!-- /.card-header -->
    <div class="card-body p-0">
      <table class="table table-sm">
        <thead>

          <tr>
            
            <th scope="col">Cod Proy</th>
            <th scope="col">NOMBRE PROYECTO</th>
            <th>Fecha Inicio</th>
            <th>Entidad Financiera</th>
            <th>Moneda</th>
            <th>Tipo Financiamiento</th>
            <th>Sede</th>
              
            <th>Opciones</th>
          </tr>

        </thead>
        <tbody>

          @foreach($listaProyectos as $itemProyecto)
            
          
          <tr>
            
            <td>{{$itemProyecto->codigoPresupuestal}}</td>
            
            <td>{{$itemProyecto->nombre}}</td>
            <td>{{$itemProyecto->getFechaInicio()}}</td>
            <td>{{$itemProyecto->getEntidadFinanciera()->nombre}}</td>
            <td>{{$itemProyecto->getMoneda()->nombre}}</td>
            <td>{{$itemProyecto->getTipoFinanciamiento()->nombre}}</td>
            <td>{{$itemProyecto->getSede()->nombre}}</td>
            
             
            </td>
            
            <td class="text-center">
              <a href="{{route('GestionProyectos.Gerente.RegistrarMetasEjecutadas',$itemProyecto->codProyecto)}}" class="btn btn-success btn-sm">
                <i class="fas fa-bullseye"></i>
                Metas
              </a>
              <a href="{{route('GestionProyectos.Gerente.Ver',$itemProyecto->codProyecto)}}" class="btn btn-success btn-sm">
                <i class="fas fa-eye"></i>
                Ver
              </a>
            </td>

          </tr>

          @endforeach

        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
<br>

@endsection

