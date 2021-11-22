{{-- EN ESTE VAN LAS SOLICITUDES POR RENDIR Y LAS OBSERVADAS --}}

<li class="nav-item dropdown">
    <?php 
        $reposicionesObservadas = App\Empleado::getEmpleadoLogeado()->getReposicionesObservadas();
        $itemsPendientesDeContabilizar = App\Empleado::getEmpleadoLogeado()->getDetallesPendientesReposicion();
        $cantidadNotificaciones = count($reposicionesObservadas) + count($itemsPendientesDeContabilizar);
    ?>

    {{-- CABECERA DE TODA LA NOTIF  --}}
    <a class="nav-link" data-toggle="dropdown" href="#">
      REP
      @if($cantidadNotificaciones!=0)
        <span class="badge badge-danger navbar-badge">
          {{$cantidadNotificaciones}}
        </span>
      @endif
    </a>



    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      
      
      @if(count($reposicionesObservadas)==0)
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>No tiene Reposiciones Observadas</b> 
        </a>
      @else
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>Reposiciones Observadas</b> 
        </a>
        @foreach($reposicionesObservadas as $detalleRepoObservada)
          <div class="dropdown-divider"></div>
          
          <a href="{{route('ReposicionGastos.Empleado.Listar')}}" class="dropdown-item notificacionObservada">
            <div class="media" >
                <h3 class="dropdown-item-title">
                  {{$detalleRepoObservada->codigoCedepas}}
                  <span class="float-right text-sm text-warning"></span>
                </h3>
                <p class="text-sm">
                  &nbsp; por {{$detalleRepoObservada->getMoneda()->simbolo}}
                    {{number_format($detalleRepoObservada->totalImporte,2)}}
                  
                </p>
            </div>
          </a>
        @endforeach
      @endif  


      @if(count($itemsPendientesDeContabilizar)==0)
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>No tiene gastos observados</b> 
        </a>
      @else
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>Gastos Observados</b> 
        </a>
        @foreach($itemsPendientesDeContabilizar as $detallePendienteRepo)
            <div class="dropdown-divider "></div>
          
            <div class="media notificacionObservada" >
                <a href="{{route('ReposicionGastos.Empleado.ver',$detallePendienteRepo->getReposicion()->codReposicionGastos)}}" class="dropdown-item ">
            
                    <p class="text-sm">
                      Gasto de {{$detallePendienteRepo->getReposicion()->getMoneda()->simbolo }}
                      {{number_format($detallePendienteRepo->importe,2)}}
                      por {{$detallePendienteRepo->concepto}}
                    </p>
                </a>
            

                <a href="{{route('ReposicionGastos.Empleado.marcarComoVisto',$detallePendienteRepo->codDetalleReposicion)}}" class="btn btn-success">
                  <i class="fas fa-check"></i>
                </a>
            </div>
         
        @endforeach
      @endif  

    </div>


  </li> 