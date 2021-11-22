{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIcono('iconoGiradorItemsLugEjec')" data-toggle="collapse" href="#collapseLugEjec" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Lugares de Ejecución del Proyecto
                </h4>
                <i id="iconoGiradorItemsLugEjec" class="fas fa-plus vaAGirar" style="float:right"></i>
            </div>
        </a>
        <div id="collapseLugEjec" class="panel-collapse collapse card-body p-0">

            @if($proyecto->sePuedeEditar())
                <button type="button" id="botonModalAgregarLugar" class="btn btn-success m-2" 
                    data-toggle="modal" data-target="#ModalAgregarLugar">
                    Nuevo Lugar de Ejecución <i class="fas fa-plus"></i>
                </button>
            @endif

            {{-- Aqui se cargará mediante JS el invocable lugares ejecucion, tambien el modal --}}
            <div class="table-responsive m-1" id="contenedorTablaLugares">     

                
            </div> 

            

        </div>


    


    </div>    
</div>
 
<script>
    
 
    function clickSelectDepartamento(){
        departamento = document.getElementById('ComboBoxDepartamento');
        ComboBoxProvincia =  document.getElementById('ComboBoxProvincia');
        ComboBoxDistrito =  document.getElementById('ComboBoxDistrito'); 
        console.log('el codigo del dep seleccionado es ='+departamento.value);

        $.get('/listarProvinciasDeDepartamento/'+departamento.value, 
            function(data)
            {   
                
                cadenaHTML = `
                    <option value="-1" selected>
                        - Provincia -
                    </option> 
                `;
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                     
                    cadenaHTML = cadenaHTML + 
                    `
                    <option value="`+element.codProvincia+`">
                        `+ element.nombre +`
                    </option>   
                    `;
                }
                ComboBoxProvincia.innerHTML = cadenaHTML;    
                ComboBoxDistrito.innerHTML =
                `
                    <option value="-1" selected>
                        - Distrito -
                    </option> 
                `;
            }
        );

    }

    function clickSelectProvincia(){
        ComboBoxProvincia = document.getElementById('ComboBoxProvincia');
        ComboBoxDistrito =  document.getElementById('ComboBoxDistrito'); 
        console.log('el codigo de provincia seleccionada es ='+ComboBoxProvincia.value);
        
        $.get('/listarDistritosDeProvincia/'+ComboBoxProvincia.value, 
            function(data)
            {   
               
                cadenaHTML = `
                    <option value="-1" selected>
                        - Distrito -
                    </option> 
                `;
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                     
                    cadenaHTML = cadenaHTML + 
                    `
                    <option value="`+element.codDistrito+`">
                        `+ element.nombre +`
                    </option>   
                    `;
                }
                ComboBoxDistrito.innerHTML = cadenaHTML;                
            }
        );

    }

    codLugarEjecucionAEliminar = "0";
    function confirmarEliminarLugarEjecucion(codLugarEjecucion){
        console.log('Se eliminará el ' + codLugarEjecucion);
        codLugarEjecucionAEliminar = codLugarEjecucion;
        confirmarConMensaje("Confirmación","¿Seguro de eliminar este lugar de ejecución?","warning",ejecutarEliminacionLugarEjecucion);

    }

    function ejecutarEliminacionLugarEjecucion(){

        //document.getElementById('contenedorTablaLugares').innerHTML=""; //aqui quitamos el modal tambien
        
        ruta = "/GestionProyectos/eliminarLugarEjecucion/" + codLugarEjecucionAEliminar;
         
        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });
         

    }

    
    function clickGuardarNuevoLugar(){
        msjError = validarFormNuevoLugar();
        if(msjError !=""){
            alerta(msjError);
            return;
        }

        //console.log('PASO LA VALIDACION');
        //document.frmAgregarLugar.submit();
         
        ComboBoxDistrito = document.getElementById('ComboBoxDistrito').value;
        codProyecto = document.getElementById('codProyecto').value;
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            cantidadZonas : cantidadZonas,
            ComboBoxDistrito,
            codProyecto
        }; 
        for (let index = 1; index <= cantidadZonas; index++) {
            datosAEnviar['zonaLugarEjecucion'+index] = zonaLugarEjecucion = document.getElementById('zonaLugarEjecucion'+index).value;
        }

        ruta = "{{route('GestionProyectos.agregarLugarEjecucion')}}";

        cerrarModal('ModalAgregarLugar');
        document.getElementById('contenedorTablaLugares').innerHTML=""; //aqui quitamos el modal tambien
        
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            recargarLugaresEjecucion();
            recargarDatosProyecto();
            cantidadZonas = 1;
        });

        

    }

    function validarFormNuevoLugar(){

        paraLimpiar = ['ComboBoxDepartamento','ComboBoxProvincia','ComboBoxDistrito']; 
        for (let index = 1; index <= cantidadZonas; index++)
            paraLimpiar.push('zonaLugarEjecucion'+index);
        console.log("PARA LIMPIAR;");
        console.log(paraLimpiar);
        
        limpiarEstilos(paraLimpiar);
        
        msj="";

        msj = validarSelect(msj,'ComboBoxDepartamento',-1,'Departamento');
        msj = validarSelect(msj,'ComboBoxProvincia',-1,'Provincia');
        msj = validarSelect(msj,'ComboBoxDistrito',-1,'Distrito');
        
        
        for (let index = 1; index <= cantidadZonas; index++)
            msj = validarTamañoMaximoYNulidad(msj,'zonaLugarEjecucion'+index,{{App\Configuracion::tamañoZona}},'Zona de ejecución N°' + index);

        

        return msj;

    }




    var cantidadZonas = 1;
    //esta funcion agrega un nuevo input al modal para agregar zonas
    function agregarCampoLugarEjecucion(){
        console.log('se hizo click');

        item = cantidadZonas + 1;
        cadena = /* html */
        `
        <div class="row" id="fila`+item+`">
            <div class="col-8 mb-1">
                <input type="text" class="form-control" name="zonaLugarEjecucion`+item+`" id="zonaLugarEjecucion`+item+`"
                placeholder="Zona N°`+item+`">
            </div>
            
        </div>
        `;

        
        lista = document.getElementById('listaDeZonas').innerHTML;
         
        $('#listaDeZonas').append(cadena);


        cantidadZonas = cantidadZonas+1;
        
        document.getElementById('cantidadZonas').value = cantidadZonas;

    }


    function eliminarUltimaFila(){

        if(cantidadZonas==1){
            alerta("No se puede eliminar la última zona");
            return;
        }

        eliminarCampoLugarEjecucion(cantidadZonas);

    }
    /* Elimina un input  */
    function eliminarCampoLugarEjecucion(index){
        document.getElementById('fila'+index).remove();
        cantidadZonas = cantidadZonas-1;
        document.getElementById('cantidadZonas').value = cantidadZonas;

    }


</script>
