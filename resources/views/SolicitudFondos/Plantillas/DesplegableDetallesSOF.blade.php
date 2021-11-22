{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

@if(isset($rendicion)) {{-- variable sí definida (caso general) --}}
    <?php 
    $detallesSolicitud = $rendicion->getSolicitud()->getDetalles()
    ?>
@else {{-- Variable NO DEFINIDA (crear rendicion) --}}
    
@endif

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIconoItemsSolicitud()" data-toggle="collapse" href="#collapse1" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Ver Items de la Solicitud
                </h4>
                <i id="iconoGiradorItems" class="fas fa-plus" style="float:right"></i>
            </div>
        </a>
        <div id="collapse1" class="panel-collapse collapse card-body p-0">
            {{-- PARA VER LA SOLICITUD ENLAZADA --}}   
            <div class="table-responsive">                           
                <table class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
                    <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                        <th width="10%" class="text-center">Ítem</th>                                        
                        <th width="40%">Concepto</th>                                 
                        <th width="10%"> Importe</th>
                        <th width="15%" class="text-center">Código Presupuestal</th>
                        {{-- <th width="20%" class="text-center">Opciones</th>                                            
                    --}}
                    </thead>
                    <tbody>
                        @foreach($detallesSolicitud as $itemDetalle)
                            <tr class="selected">
                                <td style="text-align:center;">               
                                {{$itemDetalle->nroItem}} 
                                </td>               
                                <td> 
                                {{$itemDetalle->concepto}}
                                </td>               
                                <td  style="text-align:right;">               
                                {{$itemDetalle->importe}}
                                </td>               
                                <td style="text-align:center;">               
                                    {{$itemDetalle->codigoPresupuestal}}
                                </td>                        
                            </tr>                
                        @endforeach    
                    </tbody>
                </table>
            </div> 

        </div>
    </div>
</div>
{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<style>
    #iconoGiradorItems {
        -moz-transition: all 0.25s ease-out;
        -ms-transition: all 0.25s ease-out;
        -o-transition: all 0.25s ease-out;
        -webkit-transition: all 0.25s ease-out;
    }
  
    #iconoGiradorItems.rotado {
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
    }
  
  </style>

<script>
    let giradoItems = true;
    function girarIconoItemsSolicitud(){
      const elemento = document.querySelector('#iconoGiradorItems');
      let nombreClase = elemento.className;
      if(giradoItems)
        nombreClase += ' rotado';
      else
        nombreClase =  nombreClase.replace(' rotado','');
      elemento.className = nombreClase;
      giradoItems = !giradoItems;
    }
</script>





