{{-- EN ESTE VAN LAS RENDICIONES OBSERVADAS --}}

<li class="nav-item dropdown">
    <?php 
        $rendicionesObservadas = App\Empleado::getEmpleadoLogeado()->getRendicionesObservadas();
        $itemsPendientesDeContabilizar = App\Empleado::getEmpleadoLogeado()->getDetallesPendientesRendicion();
        $cantidadNotificaciones = count($rendicionesObservadas) + count($itemsPendientesDeContabilizar);
    ?>

    {{-- CABECERA DE TODA LA NOTIF  --}}
    <a class="nav-link" data-toggle="dropdown" href="#">
      REN
      
      @if($cantidadNotificaciones!=0)
        <span class="badge badge-danger navbar-badge">
          {{$cantidadNotificaciones}}
        </span>
      @endif
    </a>

    

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      
      
      @if(count($rendicionesObservadas)==0)
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>No tiene Rendiciones Observadas</b> 
        </a>
      @else
        <a href="#" class="dropdown-item dropdown-footer notificacionObservada">
          <b>Rendiciones Observadas</b> 
        </a>
        @foreach($rendicionesObservadas as $detalleRendObservada)
          <div class="dropdown-divider"></div>
          
          <a href="{{route('RendicionGastos.Empleado.Listar')}}" class="dropdown-item notificacionObservada">
            <div class="media" >
                <h3 class="dropdown-item-title">
                  {{$detalleRendObservada->codigoCedepas}}
                  <span class="float-right text-sm text-warning"></span>
                </h3>
                <p class="text-sm">
                  &nbsp; por gasto de {{$detalleRendObservada->getMoneda()->simbolo}}
                    {{number_format($detalleRendObservada->totalImporteRendido,2)}}
                  
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
        @foreach($itemsPendientesDeContabilizar as $detallePendienteRend)
          <div class="dropdown-divider"></div>
          
          <div class="media notificacionObservada" >
            <a href="" class="dropdown-item notificacionObservada">
           
                <p class="text-sm">
                  Gasto de {{$detallePendienteRend->getRendicion()->getMoneda()->simbolo }}
                  {{number_format($detallePendienteRend->importe,2)}}
                  por {{$detallePendienteRend->concepto}}
                </p>
            </a>
            <a href="{{route('RendicionGastos.Empleado.marcarComoVisto',$detallePendienteRend->codDetalleRendicion)}}" class="btn btn-success">
              <i class="fas fa-check"></i>
            </a>

          </div>
          
        @endforeach
      @endif  

    </div>


  </li> 