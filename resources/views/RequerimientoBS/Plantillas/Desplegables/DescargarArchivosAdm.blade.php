
<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorArchivos" onclick="girarIconoArchivosAdm()" data-toggle="collapse" href="#collapseArchivosAdm" style="" > 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Archivos del Administrador
                </h4>
                <i id="iconoGiradorAdm" class="fas fa-plus" style="float:right"></i>
            </div>
        </a>
        <div id="collapseArchivosAdm" class="panel-collapse collapse card-body p-0">
          <table class="table table-striped table-bordered table-condensed table-hover" 
                style='background-color:#FFFFFF;'>
            <tbody>
              @foreach ($requerimiento->getListaArchivosAdmin() as $itemArchivo)
                <tr>
                  <td style = "padding: 0.50rem">
                    <a href="{{route('RequerimientoBS.descargarArchivoAdm',$itemArchivo->codArchivoReqAdmin)}}">
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
    #iconoGiradorAdm {
        -moz-transition: all 0.25s ease-out;
        -ms-transition: all 0.25s ease-out;
        -o-transition: all 0.25s ease-out;
        -webkit-transition: all 0.25s ease-out;
    }
  
    #iconoGiradorAdm.rotado {
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
    }
  
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
  <script>
    let giradoArchivosAdm = true;
    function girarIconoArchivosAdm(){
      const elemento = document.querySelector('#iconoGiradorAdm');
      let nombreClase = elemento.className;
      if(giradoArchivosAdm)
        nombreClase += ' rotado';
      else
        nombreClase =  nombreClase.replace(' rotado','');
      elemento.className = nombreClase;
      giradoArchivosAdm = !giradoArchivosAdm;
    }
  </script>
  