@extends('Layout.Plantilla') 

@section('titulo')
    Listado de Objetivos del Milenio
@endsection

@section('contenido')
<br>

@include('Layout.MensajeEmergenteDatos')

<div class="card">
    <div class="card-header" style=" ">
      <h2 class="card-title">Admin Sistema - Objetivos del Milenio</h2>
      <div class="card-tools">
        <ul class="nav nav-pills ml-auto">
          <li class="nav-item">

            <button href="#" class="btn btn-success" onclick="limpiarModal()"
            data-toggle="modal" data-target="#ModalObjetivoMilenio">
                <i class="fas fa-plus"></i> 
                Nuevo Registro
            </button>

          </li>
        </ul>
      </div>
      
    </div>


    

    <!-- /.card-header -->
    <div class="card-body p-0">
      <table class="table table-sm">
        <thead>

          <tr>
            <th scope="col">idBD</th>
            <th scope="col">Item</th>
            <th scope="col">Descripción</th>
            <th scope="col">Opciones</th>
          </tr>

        </thead>
        <tbody>
            
            @foreach($objetivosMilenio as $obj)
                <tr>
                    <td>
                        {{$obj->codObjetivoMilenio}}
                    </td>
                    <td>
                        {{$obj->item}}
                    </td>
                    <td>
                        {{$obj->descripcion}}
                    </td>
                    <td>

                        <button type="button" class="btn btn-info" onclick="clickEditarObjetivo({{$obj->codObjetivoMilenio}})"
                            data-toggle="modal" data-target="#ModalObjetivoMilenio">
                            <i class="fas fa-pen"></i>
                        </button>

                        <button type="button" class="btn btn-danger" onclick="clickEliminarObjetivo({{$obj->codObjetivoMilenio}})">
                            <i class="fas fa-trash"></i>
                        </button>

                    </td>
                </tr>
            @endforeach

        </tbody>
      </table>

      <div class="row m-1">
          <div class="col">

          </div>
          <div class="col text-right">
               
                <button class="m-2 btn btn-primary" onclick="confirmarGenerarRelaciones()">
                    <i class="fas fa-random"></i>
                    Generar relaciones faltantes Proy-ObjMilenio
                </button>
          </div>
      </div>
    </div>
    <!-- /.card-body -->
  </div>
  
  <!-- /.card -->

<div class="modal fade" id="ModalObjetivoMilenio" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalObjetivoMilenio"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmObjMilenio" name="frmObjMilenio" action="{{route('ObjetivoMilenio.agregarEditarObjetivo')}}" method="POST">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codObjetivoMilenio" id="codObjetivoMilenio" value="0">
                        @csrf
                        
                        <div class="row">
                            <div class="col">
                              <label for="">Item</label>
                              <input type="number" min="0" step="1" class="form-control" value="" name="item" id="item" >
                            </div>
                             
                            <div class="w-100"></div>

                            <div class="col">
                                <label for="">Descripción</label>
                                <textarea class="form-control" name="descripcion" id="descripcion" cols="30" rows="4"></textarea>
                            </div>

                        </div>                            
                        
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="m-1 btn btn-primary" onclick="clickGuardarObjetivo()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
        </div>
    </div>
</div>

@endsection
@section('script')
@include('Layout.ValidatorJS')


<script>
  
    var listaObjetivos = @php echo json_encode($objetivosMilenio) @endphp;
  
    document.addEventListener('DOMContentLoaded', function () {
      
    }, false);
  
  
  
      function clickEditarObjetivo(codObjetivoMilenio){
          obj = listaObjetivos.find(element => element.codObjetivoMilenio == codObjetivoMilenio);
  
  
          document.getElementById('codObjetivoMilenio').value = codObjetivoMilenio;
          document.getElementById('item').value = obj.item;
          document.getElementById('descripcion').value = obj.descripcion;
  
          document.getElementById('TituloModalObjetivoMilenio').innerHTML = "Editar Objetivo Milenio";
      }
      function limpiarModal(){
          
  
          document.getElementById('codObjetivoMilenio').value = "0";
          document.getElementById('item').value = "1";
          document.getElementById('descripcion').value = "";
  
          document.getElementById('TituloModalObjetivoMilenio').innerHTML = "Agregar Objetivo Milenio";
      }   
       
  
         
    function clickGuardarObjetivo(){
      msjError = validarFormObjetivo();
      if(msjError!=""){
        alerta(msjError);
        return;
      }
      
      document.frmObjMilenio.submit();
    }
  
  
  
  
    function validarFormObjetivo(){
    
      msj = "";
  
      msj = validarPositividadYNulidad(msj,'item','Item');
      msj = validarTamañoMaximoYNulidad(msj,'descripcion',400 ,'Descripción');
  
      
      return msj;
  
    }
  
  
  
  
  
    codObjetivoMilenioAEliminar = 0;
    function clickEliminarObjetivo(codObjetivoMilenio){

      codObjetivoMilenioAEliminar = codObjetivoMilenio;
      confirmarConMensaje("Confirmacion",'¿Desea eliminar el objetivo del milenio?' ,'warning',ejecutarEliminacionObjetivo);
    }
  
    function ejecutarEliminacionObjetivo(){
          location.href="/ObjetivosMilenio/eliminar/" + codObjetivoMilenioAEliminar;
    }
  
   


    function confirmarGenerarRelaciones(){
        confirmarConMensaje("Confirmación","¿Desea generar todas las relaciones faltantes entre proyectos y obj del Milenio?",'warning',ejecutarGenerarRelaciones);
    }
    function ejecutarGenerarRelaciones(){
        location.href ="{{route('ObjetivoMilenio.generarRelacionesProyectosYObjMilenio')}}";
    }


  </script>
  

@endsection
