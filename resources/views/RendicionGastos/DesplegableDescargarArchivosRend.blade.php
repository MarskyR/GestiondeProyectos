
<div class="panel-group card">
  <div class="panel panel-default">
      <a id="giradorArchivos" onclick="girarIconoDescargaComprobantes()" data-toggle="collapse" href="#collapseArchivos" style="" > 
          <div class="panel-heading card-header" style="">
              <h4 class="panel-title card-title" style="">
                  Descargar Archivos Comprobantes
              </h4>
              <i id="iconoGirador" class="fas fa-plus" style="float:right"></i>
          </div>
      </a>
      <div id="collapseArchivos" class="panel-collapse collapse card-body p-0">
        <table class="table table-striped table-bordered table-condensed table-hover" 
              style='background-color:#FFFFFF;'>
          <tbody>
              @foreach ($rendicion->getListaArchivos() as $itemArchivo)
              <tr>
                <td style = "padding: 0.50rem">
                  <a href="{{route('rendiciones.descargarCDP',$itemArchivo->codArchivoRend)}}">
                        <i id="" class="far fa-address-card nav-icon"></i>
                        {{$itemArchivo->nombreAparente}}
                    </a>
                  </td>
              
              </tr>
              @endforeach
          </tbody>
        </table>

      </div>
  </div>
</div>

<style>
  #iconoGirador {
      -moz-transition: all 0.25s ease-out;
      -ms-transition: all 0.25s ease-out;
      -o-transition: all 0.25s ease-out;
      -webkit-transition: all 0.25s ease-out;
  }

  #iconoGirador.rotado {
      -moz-transform: rotate(90deg);
      -ms-transform: rotate(90deg);
      -o-transform: rotate(90deg);
      -webkit-transform: rotate(90deg);
  }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  
<script>
  let giradoComprobantes = true;
  function girarIconoDescargaComprobantes(){
    const elemento = document.querySelector('#iconoGirador');
    let nombreClase = elemento.className;
    if(giradoComprobantes)
      nombreClase += ' rotado';
    else
      nombreClase =  nombreClase.replace(' rotado','');
    elemento.className = nombreClase;
    giradoComprobantes = !giradoComprobantes;
  }
</script>