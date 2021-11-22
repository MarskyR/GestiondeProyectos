{{-- EN ESTE VAN LAS SOLICITUDES POR RENDIR Y LAS OBSERVADAS --}}

<li class="nav-item dropdown">
    <?php 
        $solicitudesPorRendirr = App\Empleado::getEmpleadoLogeado()->getSolicitudesPorRendir();
        $solicitudesObservadas = App\Empleado::getEmpleadoLogeado()->getSolicitudesObservadas();
    ?>

    {{-- CABECERA DE TODA LA NOTIF  --}}
    <a class="nav-link" data-toggle="dropdown" href="#">
      
      SOF
      @if(count($solicitudesPorRendirr) + count($solicitudesObservadas) !=0)
      <span class="badge badge-danger navbar-badge" >
        {{count($solicitudesPorRendirr) +
         count($solicitudesObservadas) }}
      </span>
      @endif
    </a>



    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      
      
      @if(count($solicitudesPorRendirr)==0)
        <a href="#" class="dropdown-item dropdown-footer notificacionXRendir">
          <b>No tiene Solicitudes por rendir.</b> 
        </a>
      @else
        <a href="#" class="dropdown-item dropdown-footer notificacionXRendir">
          <b>Solicitudes por Rendir</b> 
        </a>
        @foreach($solicitudesPorRendirr as $detalleSolXRendir)
          <div class="dropdown-divider"></div>
          
          <a href="{{route('RendicionGastos.Empleado.Listar')}}" class="dropdown-item notificacionXRendir">
            <div class="media" >
                <h3 class="dropdown-item-title">
                  {{$detalleSolXRendir->codigoCedepas}}
                  <span class="float-right text-sm text-warning"></span>
                </h3>
                <p class="text-sm">
                  &nbsp; por {{$detalleSolXRendir->getMoneda()->simbolo}}
                    {{number_format($detalleSolXRendir->totalSolicitado,2)}}
                  
                </p>
            </div>
          </a>
        @endforeach
      @endif  



      @if(count($solicitudesObservadas)==0)
        <a href="" class="dropdown-item dropdown-footer notificacionObservada">
          <b>No tiene solicitudes Observadas</b> 
        </a>
      @else
        <a href="" class="dropdown-item dropdown-footer notificacionObservada">
          <b>Solicitudes Observadas</b> 
        </a>
        @foreach($solicitudesObservadas as $itemSolObserv)
          <div class="dropdown-divider"></div>
          
          <a href="{{route('SolicitudFondos.Empleado.Edit',$itemSolObserv->codSolicitud)}}" class="dropdown-item notificacionObservada">
            <div class="media" >
                <h3 class="dropdown-item-title">
                  {{$itemSolObserv->codigoCedepas}}
                  <span class="float-right text-sm text-warning"></span>
                </h3>
                
                <p class="text-sm">
                  &nbsp; por {{$itemSolObserv->getMoneda()->simbolo}}
                    {{number_format($itemSolObserv->totalSolicitado,2)}}
                  
                </p>
            </div>
          </a>
        @endforeach
      @endif

    </div>


  </li> 