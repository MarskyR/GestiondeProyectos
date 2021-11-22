
<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorArchivos" onclick="girarIconoArchivosEmp()" data-toggle="collapse" href="#collapseArchivosEmp" style="" > 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Archivos del Colaborador
                </h4>
                <i id="iconoGiradorEmp" class="fas fa-plus" style="float:right"></i>
            </div>
        </a>
        <div id="collapseArchivosEmp" class="panel-collapse collapse card-body p-0">
          <table class="table table-striped table-bordered table-condensed table-hover" 
                style='background-color:#FFFFFF;'>
            <tbody>
              @foreach ($requerimiento->getListaArchivosEmp() as $itemArchivo)
                <tr>
                    <td style = "padding: 0.50rem">
                        <a href="{{route('RequerimientoBS.descargarArchivoEmp',$itemArchivo->codArchivoReqEmp)}}">
                              <i id="" class="far fa-address-card nav-icon"></i>
                              {{$itemArchivo->nombreAparente}}
                        </a>
                    </td>
                    <td>
                        <a href="#" onclick="clickEliminarArchivo({{$itemArchivo->codArchivoReqEmp}},'{{$itemArchivo->nombreAparente}}')">
                          <i class="fas fa-trash" title="Eliminar Archivo"></i>
                        </a>
                    </td>


                </tr>
              @endforeach

              @if($requerimiento->getCantidadArchivosEmp()==0)
                  <tr>
                    <td>
                      No hay archivos para mostrar.
                    </td>
                  </tr>
              @endif

            </tbody>
          </table>
  
        </div>
    </div>
  </div>
  
  <style>
    #iconoGiradorEmp {
        -moz-transition: all 0.25s ease-out;
        -ms-transition: all 0.25s ease-out;
        -o-transition: all 0.25s ease-out;
        -webkit-transition: all 0.25s ease-out;
    }
  
    #iconoGiradorEmp.rotado {
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
    }
  
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
  <script>

    codArchivoAEliminar =0;
    function clickEliminarArchivo(codArchivoReq,nombre){
      codArchivoAEliminar = codArchivoReq;
      confirmarConMensaje("Confirmación","¿Desea eliminar el archivo "+nombre+"?","warning",ejecutarEliminacionArchivo);

    }


    function ejecutarEliminacionArchivo(){
      location.href = "/RequerimientoBS/Contador/eliminarArchivoDelEmpleado/" + codArchivoAEliminar;
      
    }









    let giradoArchivosEmp = true;
    function girarIconoArchivosEmp(){
      const elemento = document.querySelector('#iconoGiradorEmp');
      let nombreClase = elemento.className;
      if(giradoArchivosEmp)
        nombreClase += ' rotado';
      else
        nombreClase =  nombreClase.replace(' rotado','');
      elemento.className = nombreClase;
      giradoArchivosEmp = !giradoArchivosEmp;
    }
  </script>
  