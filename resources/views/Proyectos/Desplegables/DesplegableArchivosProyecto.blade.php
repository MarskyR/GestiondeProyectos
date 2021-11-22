
<div class="panel-group card">
  <div class="panel panel-default">
      <a id="giradorArchivos" onclick="girarIcono('iconoGiradorArchivosProyecto')" data-toggle="collapse" href="#collapseArchivosProyecto" style="" > 
          <div class="panel-heading card-header" style="">
              <h4 class="panel-title card-title" style="">
                  Archivos del Proyecto
              </h4>
              <i id="iconoGiradorArchivosProyecto" class="vaAGirar fas fa-plus" style="float:right"></i>
          </div>
      </a>
      <div id="collapseArchivosProyecto" class="panel-collapse collapse card-body p-0">
        <form id="formAñadirArchivos" name="formAñadirArchivos" action="{{route('GestionProyectos.añadirArchivos')}}" method="post"  enctype="multipart/form-data">
          <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codProyecto" value="{{$proyecto->codProyecto}}">
          @csrf
          <div class="row ml-3" style="color:red"> {{-- AVISO DE QUE FALTAN X ARCHIVOS --}}
            <b>
              {{$proyecto->getTiposArchivosFaltantes()}}
            </b>
          </div>
          <div class="row">
            
            <div class="col BordeCircular" id="divEnteroArchivo">    
              <div class="row">
                  <div class="col ">
                     
                      <div class="form-check">
                          <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_añadir" value="1">
                          <label class="form-check-label" for="ar_añadir">
                            Añadir Archivos
                          </label>
                      </div>
                      <div class="form-check">
                          <input class="form-check-input" type="radio" name="tipoIngresoArchivos" id="ar_sobrescribir" value="2">
                          <label class="form-check-label" for="ar_sobrescribir">
                            Sobrescribir archivos
                          </label>
                      </div>
              

                  </div>
                  <div class="w-100"></div>
                  <div class="col">
                      <input type="{{App\Configuracion::getInputTextOHidden()}}" name="nombresArchivos" id="nombresArchivos" value="">
                      <input type="file" multiple class="btn btn-primary" name="filenames[]" id="filenames"        
                              style="{{App\Configuracion::getDisplayNone()}}" onchange="cambio()">  
                                      <input type="hidden" name="nombreImgImagenEnvio" id="nombreImgImagenEnvio">                 
                      <label class="label" for="filenames" style="font-size: 12pt;">       
                              <div id="divFileImagenEnvio" class="hovered">       
                              Seleccionar Archivos
                              <i class="fas fa-upload"></i>        
                          </div>       
                      </label>  

                      <select class="form-control form-control-sm" name="codTipoArchivoProyecto" id="codTipoArchivoProyecto">
                        <option value="-1">- Seleccione el tipo de Archivos -</option>
                        @foreach($listaTiposArchivos as $tipoArchivo)
                          <option value="{{$tipoArchivo->codTipoArchivoProyecto}}">{{$tipoArchivo->nombre}}</option>
                        @endforeach
                      </select>
                  </div>
              </div>
              
                  
            </div>    


            <div class="col">


              <button type="button" class="btn btn-primary float-right" 
                id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                onclick="clickAñadirArchivos()">
                <i class='fas fa-save'></i> 
                Añadir Archivos
              </button> 
 

            </div>


          </div>
        </form>

        {{-- <table class="table table-striped table-bordered table-condensed table-hover" 
              style='background-color:#FFFFFF;'>
            <thead>
              <th>Archivo</th>
              <th>Fecha Subida</th>
              <th>Tipo Archivo</th>
              <th>Opciones</th>
            </thead>
          <tbody>


            @foreach ($proyecto->getListaArchivos() as $itemArchivo)
            <tr>
                <td style = "padding: 0.50rem">
                <a href="{{route('GestionProyectos.descargarArchivo',$itemArchivo->codArchivoProyecto)}}">
                      <i id="" class="far fa-address-card nav-icon"></i>
                       {{$itemArchivo->nombreAparente}}
                   </a>
                </td>
                <td style="color: rgb(71, 122, 231)">
                  {{date("d/m/Y",strtotime($itemArchivo->fechaHoraSubida))}}
                </td>
                <td >
                  {{$itemArchivo->getTipoArchivo()->nombre}}

                </td>
                <td>
                  <a href="#" class="btn btn-danger btn-sm" 
                    onclick="clickEliminarArchivoProyecto({{$itemArchivo->codArchivoProyecto}},'{{$itemArchivo->nombreAparente}}')">
                    Borrar 
                    <i class="fas fa-trash" title="Eliminar Archivo"></i>
                  </a>
              </td>
             
            </tr>
            @endforeach
          </tbody>
        </table> --}}

      </div>
  </div>
</div>

<style>
 
    .hovered:hover{
        background-color:rgb(97, 170, 170);

    }

    .BordeCircular{
        border-radius: 10px;
        background-color:rgb(190, 190, 190);
        margin-left: 2%;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  
<script>


    codArchivoProyAeliminar =0;
    function clickEliminarArchivoProyecto(codArchivo,nombre){
      codArchivoProyAeliminar = codArchivo;
      confirmarConMensaje("Confirmación","¿Desea eliminar el archivo "+nombre+"?","warning",ejecutarEliminacionArchivoProyecto);
    }

    /* FALTA CODIGO AQUI */
    function ejecutarEliminacionArchivoProyecto(){
      location.href = "/GestionProyectos/eliminarArchivo/" + codArchivoProyAeliminar;

    }





  //se ejecuta cada vez que escogewmos un file
      function cambio(){

        msjError = validarPesoArchivos();
        if(msjError!=""){
            alerta(msjError);
            return;
        }
        

        listaArchivos="";
        cantidadArchivos = document.getElementById('filenames').files.length;
        console.log('----- Cant archivos seleccionados:' + cantidadArchivos);
        for (let index = 0; index < cantidadArchivos; index++) {
            nombreAr = document.getElementById('filenames').files[index].name;
            console.log('Archivo ' + index + ': '+ nombreAr + "cantChar:" + nombreAr.length);
            listaArchivos = listaArchivos +', '+  nombreAr; 
        }
        listaArchivos = listaArchivos.slice(2, listaArchivos.length);
        document.getElementById("divFileImagenEnvio").innerHTML= listaArchivos;
        document.getElementById("nombresArchivos").value= listaArchivos;
        

      }


      function validarPesoArchivos(){
        cantidadArchivos = document.getElementById('filenames').files.length;
        
        msj="";
        for (let index = 0; index < cantidadArchivos; index++) {
            var imgsize = document.getElementById('filenames').files[index].size;
            nombre = document.getElementById('filenames').files[index].name;
            if(imgsize > {{App\Configuracion::pesoMaximoArchivoMB}}*1000*1000 ){
                msj=('El archivo '+nombre+' supera los  {{App\Configuracion::pesoMaximoArchivoMB}}Mb, porfavor ingrese uno más liviano o comprima.');
            }
        }

        if(cantidadArchivos == 0)
          msj="No ha ingresado ningun archivo.";
        

        return msj;

      }


      function clickAñadirArchivos(){
        msj = validarFormulario();
        if(msj!=""){
          alerta(msj);
          return;
        }


          document.formAñadirArchivos.submit();

      }

      function validarFormulario(){

          limpiarEstilos(['codTipoArchivoProyecto']);
          msj = "";
          msj = validarPesoArchivos();
          msj = validarSelect(msj,'codTipoArchivoProyecto','-1','Tipo de archivo');
          msj = validarGroupButton(msj,['ar_sobrescribir','ar_añadir'],"tipo de subida");

          

          return msj;
      }

      
</script>