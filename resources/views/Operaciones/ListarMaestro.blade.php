@extends ('Layout.Plantilla')
@section('titulo')
  Buscador Maestro
@endsection
@section('contenido')

<style>
    .fondoTitulos{
        background-color: rgb(218, 218, 218);
        font-weight: bold;
    }

</style>

<div style="text-align: center">
  <h2> Buscador Maestro de documentos</h2>
  <br>
    
    

    @include('Layout.MensajeEmergenteDatos')
    <div class="row m-2">

        <div style="width: 30%">
            <input type="text" class="form-control" id="buscar_codigoCedepas" name="buscar_codigoCedepas" 
                value="" placeholder="Código del documento">
        
        </div>
        <div style="width: 30%">
            <select type="text" class="form-control" id="buscar_tipoDocumento" name="buscar_tipoDocumento" 
                value="" placeholder="Código del documento">
            
                <option value="TODOS" selected>-Todos-</option>
                <option value="SOF">Solicitudes</option>
                <option value="REN">Rendiciones</option>
                <option value="REP">Reposiciones</option>
                <option value="REQ">Requerimientos</option>
                
            </select>
        </div>
        <div style="width: 30%">
            <select type="text" class="form-control" id="buscar_codEmpleadoEmisor" name="buscar_codEmpleadoEmisor" 
                value="" placeholder="Código del documento">

                <option value="-1" selected>- Emisor -</option>
                @foreach($listaEmpleados as $empleado)
                    <option value="{{$empleado->codEmpleado}}">
                        {{$empleado->getNombreCompleto()}}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button class="btn btn-primary" onclick="actualizarBusqueda()">
                Buscar
            </button>
        </div>

    </div>
        

    <div id="contenedorTabla">


    </div>
    
</div>
@endsection
@section('tiempoEspera')

<div class="loader" id="pantallaCarga"></div>

@endsection
@section('script')
<script>

    filtroABuscar = "";


    $(document).ready(function(){
        actualizarBusqueda();
        
        

    });

    function cambiarEstado(codNuevoEstado,tipoDoc,idDocumento){
        console.log('ejecutando...');
        ruta = "{{route('CambiarEstadoDocumento')}}";
        datos = {
            codNuevoEstado : codNuevoEstado,
            tipoDoc : tipoDoc,
            idDocumento : idDocumento
        }


        $.get(ruta, datos,function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);

        });

    }

    function actualizarBusqueda(){

        $(".loader").fadeIn("slow");
        console.log("Actualizando busqueda");
        buscar_codigoCedepas = document.getElementById('buscar_codigoCedepas').value;
        buscar_tipoDocumento = document.getElementById('buscar_tipoDocumento').value;
        buscar_codEmpleadoEmisor = document.getElementById('buscar_codEmpleadoEmisor').value;
        
        
        ruta = "{{route('GetListadoBusqueda')}}"

        datos = {
            buscar_codigoCedepas : buscar_codigoCedepas,
            buscar_tipoDocumento : buscar_tipoDocumento,
            buscar_codEmpleadoEmisor : buscar_codEmpleadoEmisor
        }
        $.get(ruta, datos,function(data){                       
             
            objeto = document.getElementById('contenedorTabla');
            objeto.innerHTML = data;
            $(".loader").fadeOut("slow");
        
        });
         
    }

</script>









@endsection