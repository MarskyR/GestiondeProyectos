
@foreach($poblacionesBeneficiarias as $itempoblacion)
  <br>
  <br>
  <table  border="1">
    <thead>
        
        <tr>
          <th  style="{{$fondoPlomo}}" colspan="5">Poblacion:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          <th colspan="5">{{$itempoblacion->descripcion}}</th>   
        </tr>
    </tbody>
  </table>
  <br>
  <!--NATURALES -->
  <table>
    <thead>
        <tr>
          <th style="font-size: small;text-align:left;">Naturales:</th>  
        </tr>
    </thead>
  </table>
  <table class="table table-sm" border="1">
    <thead style="{{$fondoPlomo}}">

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
      @foreach($itempoblacion->getPersonasNaturales() as $i=>$itempersona)
      <tr>
        <th >{{$i+1}}</th>
        <th >{{$itempersona->dni}}</th>
        <th >{{$itempersona->nombres}}</th>
        <th >{{$itempersona->apellidos}}</th>
        <th >{{$itempersona->sexo}}</th>
        <th >{{$itempersona->nroTelefono}}</th>
        <th >{{$itempersona->fechaNacimiento}}</th>
        <th >{{$itempersona->edadMomentanea}}</th>
        <th >{{$itempersona->getResumenActividades()}}</th>
        <th colspan="4" >{{$itempersona->direccion}}</th>


        
      </tr>
      @endforeach
    </tbody>
  </table>
  <!--JURIDICAS -->
  <br>
  <table>
    <thead style="{{$fondoPlomo}}">
        <tr>
          <th colspan="2" style="font-size: small;text-align:left;">JURIDICAS:</th>  
        </tr>
    </thead>
  </table>
  <table class="table table-sm" border="1">
    <thead style="{{$fondoPlomo}}">

      <tr>
        <th  style="{{$fondoPlomo}}">#</th>
        <th  style="{{$fondoPlomo}}">RUC</th>
        <th  style="{{$fondoPlomo}}"># Socios</th>
        <th  style="{{$fondoPlomo}}"># Socias</th>
        <th  style="{{$fondoPlomo}}">Tipo</th>
        <th style="{{$fondoPlomo}}" colspan="3">Razon Social</th>
        <th  style="{{$fondoPlomo}}">Actividades</th>
        <th  style="{{$fondoPlomo}}" colspan="4">Direccion</th>

        
      </tr>

    </thead>
    <tbody>
      @foreach($itempoblacion->getPersonasJuridicas() as $i=>$itempersona)
      <tr>
        <th >{{$i+1}}</th>
        <th >{{$itempersona->ruc}}</th>
        <th >{{$itempersona->numeroSociosHombres}}</th>
        <th >{{$itempersona->numeroSociosMujeres}}</th>
        <td>{{$itempersona->getTipologia()->siglas}}</td>
        <th colspan="3" >{{$itempersona->razonSocial}}</th>
        <th >{{$itempersona->getResumenActividades()}}</th>
        <th colspan="4" >{{$itempersona->direccion}}</th>

        
      </tr>
      @endforeach
    </tbody>
  </table>
@endforeach
