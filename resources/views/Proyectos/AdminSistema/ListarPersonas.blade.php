@extends('Layout.Plantilla') 

@section('titulo')
    Listado de Proyectos
@endsection

@section('contenido')
<br>

@include('Layout.MensajeEmergenteDatos')

<div class="card">

    <div class="card-header" style=" ">
      <h3 class="card-title">Personas Naturales</h3>
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
      <table class="table table-sm fontSize9">
        <thead>

          <tr>
            <th width="8%" scope="col">DNI</th>
            <th  scope="col" style="text-align: center">Nombres</th>
            <th >Apellidos</th>
            <th width="6%">Sexo</th>
            <th width="9%">Telefono</th>
            <th >Direccion</th>
            <th width="12%" >Fecha Nacimiento</th>
            <th width="5%" scope="col" style="text-align: center">Edad Registrada</th>
            <th >Actividad Principal</th>
            <th> Poblaciones Beneficiarias y Cod Proy </th>

            
            </tr>
        </thead>
        <tbody>

          @foreach($personasNaturales as $persona)
            <tr>
              <td>
                {{$persona->dni}}
              </td>
              <td>
                {{$persona->nombres}}
              </td>
              <td>
                {{$persona->apellidos}}
              </td>
              <td>
                {{$persona->sexo}}
              </td>
              <td>
                {{$persona->nroTelefono}}
              </td>
              <td>
                {{$persona->direccion}}
              </td>
              <td>
                {{$persona->fechaNacimiento}}
              </td>

              <td>
                {{$persona->edadMomentanea}}
              </td>
              <td>
                {{$persona->actividadPrincipal}}
              </td>
              <td>
                {{$persona->getListaPoblaciones()}}
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



<div class="card">

  <div class="card-header" style=" ">
    <h3 class="card-title">Personas Jurídicas</h3>
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
    <table class="table table-sm fontSize9">
      <thead>

        <tr>
          <th class="text-center" width="8%" scope="col">RUC</th>
          <th class="text-center"  scope="col">Razon Social</th>
          <th class="text-center" >Direccion</th>
          <th class="text-center" width="6%"># Hombres</th>
          <th class="text-center" width="9%"># Mujeres</th>
          <th class="text-center" >Actividad Principal</th>
       
          <th>Pob Beneficiaria y Cod Proy</th>

          
          </tr>
      </thead>
      <tbody>

        @foreach($personasJuridicas as $persona)
          <tr>
            <td class="text-center">
              {{$persona->ruc}}
            </td>
            <td class="text-center">
              {{$persona->razonSocial}}
            </td>
           
            <td class="text-center">
              {{$persona->direccion}}
            </td>
         

            <td class="text-center">
              {{$persona->numeroSociosHombres}}
            </td>
            <td class="text-center">
              {{$persona->numeroSociosMujeres}}
            </td>
            <td class="text-center">
              {{$persona->actividadPrincipal}}
            </td>
            <td>
              {{$persona->getListaPoblaciones()}}
            </td>

          </tr>
        

        @endforeach

      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>


@endsection

