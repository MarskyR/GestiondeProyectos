{{-- EN ESTE VAN LAS SOLICITUDES POR RENDIR Y LAS OBSERVADAS --}}

<li class="nav-item dropdown">
    <?php 
        $requerimientosObservados = App\Empleado::getEmpleadoLogeado()->getRequerimientosObservados();
     
    ?>

    {{-- CABECERA DE TODA LA NOTIF  --}}
    <a class="nav-link" data-toggle="dropdown" href="#">
      REQ
      @if(count($requerimientosObservados)!=0)
        <span class="badge badge-danger navbar-badge">
          {{count($requerimientosObservados)}}
        </span>
      @endif
    </a>



    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      
      
      @if(count($requerimientosObservados)==0)
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>No tiene Requerimientos Observados</b> 
        </a>
      @else
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>Requerimientos Observadas</b> 
        </a>
        @foreach($requerimientosObservados as $detalleReqObservado)
          <div class="dropdown-divider"></div>
          
          <a href="{{route('RequerimientoBS.Empleado.Listar')}}" class="dropdown-item notificacionObservada">
            <div class="media" >
                <h3 class="dropdown-item-title">
                  {{$detalleReqObservado->codigoCedepas}}
                  <span class="float-right text-sm text-warning"></span>
                </h3>
                <p class="text-sm">
                  &nbsp; por {{$detalleReqObservado->getObservacionMinimizada()}}
                </p>
            </div>
          </a>
        @endforeach
      @endif  



    </div>


  </li> 