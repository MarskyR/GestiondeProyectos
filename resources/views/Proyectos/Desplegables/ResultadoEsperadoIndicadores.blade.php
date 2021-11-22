{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIcono('iconoGiradorResEsp')" data-toggle="collapse" href="#collapseResEsp" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Resultados Esperados e Indicadores
                </h4>
                <i id="iconoGiradorResEsp" class="vaAGirar fas fa-plus" style="float:right"></i>
            </div>

        </a>
        <div id="collapseResEsp" class="panel-collapse collapse card-body">
            
        </div>
    </div>    
</div>
 
<script>
    tamañoMax = {{App\Configuracion::tamañoMaximoResultadoEsperado}};
    tamañoMaxIndicadorResultado = {{App\Configuracion::tamañoMaximoIndicadorResultado}};
    tamañoMaxMedioVerificacion =  {{App\Configuracion::tamañoMaximoMedioVerificacion}};
      
    

    document.addEventListener('DOMContentLoaded', function () {
       // cargarResultadosEsperados();
    }, false);

    /* ------------------------------------------------ RESULTADO ESPERADO ------------------------------------------------ */
    
    function clickGuardarNuevoResEsp(){
        
        msjError = validarFrmGuardarNuevoResEsp();
        if(msjError!="")
        {
            alerta(msjError);
            return ;
        }
 
        descripcionNuevoResultado = document.getElementById('descripcionNuevoResultado').value;
        codResultadoEsperado = document.getElementById('codResultadoEsperado').value;

         
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            descripcionNuevoResultado: descripcionNuevoResultado,
            codProyecto : {{$proyecto->codProyecto}},
            codResultadoEsperado : codResultadoEsperado
        }; 
        ruta = "{{route('GestionProyectos.agregarEditarResultadoEsperado',$proyecto->codProyecto)}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalAgregarResultadoEsperado');
    }
    

    function validarFrmGuardarNuevoResEsp(){
        
        msjError ="";
        descripcion = document.getElementById('descripcionNuevoResultado').value;
        if(descripcion == ""){
            msjError = ("Debe ingresar una descripcion.");
            
        }
        if(descripcion.length > tamañoMax )
        {
            msjError ="La descripcion debe tener máximo " + 
                        tamañoMax +
                         " caracteres. Actualmente tiene" +
                         descripcion.length 
                          +".";
    
        }

        return msjError;

    }

    
    function clickEditarResultadoEsperado(codResultadoEsperado){
        resultadoEsperado = resultadosEsperados.find(element => element.codResultadoEsperado == codResultadoEsperado);
        
        document.getElementById('descripcionNuevoResultado').value = resultadoEsperado.descripcion;
        document.getElementById('codResultadoEsperado').value = resultadoEsperado.codResultadoEsperado;
        
    }

    
    function limpiarModalResultadoEsperado(){

        
        document.getElementById('descripcionNuevoResultado').value ="";
        document.getElementById('codResultadoEsperado').value = "0";
        
        
    }


    codResultadoEsperadoAEliminar = "0";
    function clickEliminarResultadoEsperado(codResultadoEsperado){

        console.log('Se eliminará el ' + codResultadoEsperado);
        res = resultadosEsperados.find(element => element.codResultadoEsperado == codResultadoEsperado);
        if(res.cantidadIndicadoresResultados!=0)
        {
            alerta("Para eliminar el resultado esperado, debe eliminar todos sus indicadores de resultado.");
            return;
        }

        if(res.cantidadActividades!=0)
        {
            alerta("Para eliminar el resultado esperado, debe eliminar todas sus actividades.");
            return;
        }

        codResultadoEsperadoAEliminar = codResultadoEsperado;
        

        confirmarConMensaje('Confirmación','¿Seguro de eliminar el resultado esperado "'+res.descripcion+'" ? Se eliminarán también sus indicadores','warning',ejecutarEliminacionResultadoEsperado);

    }

    function ejecutarEliminacionResultadoEsperado(){

        ruta = "/GestionProyectos/eliminarResultadoEsperado/" + codResultadoEsperadoAEliminar;

        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            recargarTodosLosInvocables();
        });
    }





    /*------------------------------------- INDICADORES DE RESULTADOS ------------------------------------- */


    function clickAgregarIndicadorResultado(){
        msjError = validarFrmAgregarIndicadorResultado();
        if(msjError!="")
        {
            alerta(msjError);
            return ;
        }

        descripcionNuevoIndicadorResultado = document.getElementById('descripcionNuevoIndicadorResultado').value;
        ComboBoxResultadoEsperadoX = document.getElementById('ComboBoxResultadoEsperadoX').value;
        codIndicadorResultado = document.getElementById('codIndicadorResultado').value; 
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            codProyecto : {{$proyecto->codProyecto}},

            descripcionNuevoIndicadorResultado: descripcionNuevoIndicadorResultado,
            codIndicadorResultado : codIndicadorResultado,
            ComboBoxResultadoEsperadoX : ComboBoxResultadoEsperadoX
        }; 

        ruta = "{{route('GestionProyectos.agregarEditarIndicadorResultado')}}";
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalAgregarIndicadorResultado');
        
    }


    function validarFrmAgregarIndicadorResultado(){

        codResultadoEsperado = document.getElementById('ComboBoxResultadoEsperadoX').value;
        descripcionNuevoIndicadorResultado = document.getElementById('descripcionNuevoIndicadorResultado').value;
        

        msjError = "";
        if(codResultadoEsperado=="-1")
        {
            msjError="Debe seleccionar el resultado esperado correspondiente al indicador.";
        }

        if(descripcionNuevoIndicadorResultado=="")
        {
            msjError="Debe ingresar la descripción.";            
        }

        console.log('llega aca' + descripcionNuevoIndicadorResultado.length);
        

        if(descripcionNuevoIndicadorResultado.length >= tamañoMaxIndicadorResultado)
            msjError ="La descripcion debe tener máximo " + tamañoMaxIndicadorResultado + " caracteres. Actualmente tiene" + descripcionNuevoIndicadorResultado.length +".";
        


        
        return msjError;


    }

    function clickEditarIndicadorResultado(codIndicadorResultado){
        var indicador = indicadoresResultados.find(element => element.codIndicadorResultado == codIndicadorResultado)
        document.getElementById('codIndicadorResultado').value = indicador.codIndicadorResultado;
        document.getElementById('ComboBoxResultadoEsperadoX').value = indicador.codResultadoEsperado;
        document.getElementById('descripcionNuevoIndicadorResultado').value = indicador.descripcion;
    }
 

    function limpiarModalIndicadorResultado(){
        document.getElementById('ComboBoxResultadoEsperadoX').value = "-1";
        document.getElementById('codIndicadorResultado').value = "0";
        document.getElementById('descripcionNuevoIndicadorResultado').value = "";
    }


    codIndicadorResultadoAEliminar = "0";
    function clickEliminarIndicadorResultado(codIndicadorResultado){
        
        console.log('Se eliminará el  codIndicadorResultado' + codIndicadorResultado);

        ind = indicadoresResultados.find(element => element.codIndicadorResultado == codIndicadorResultado);
        descripcion = ind.descripcion;
        
        if(ind.cantidadMediosVerificacion!=0){
            alerta('Para eliminar un indicador, primero debe eliminar sus medios de verificación.')
            return;
        }

        codIndicadorResultadoAEliminar = codIndicadorResultado;
        confirmarConMensaje('Confirmación','¿Seguro de eliminar el indicador de resultado  "'+descripcion+'" ? ','warning',ejecutarEliminacionIndicadorResultado);

    }

    function ejecutarEliminacionIndicadorResultado(){
        ruta = "/GestionProyectos/eliminarIndicadorResultadoEsperado/" + codIndicadorResultadoAEliminar;

        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            recargarTodosLosInvocables();
        });


    }

    /* ------------------------------------------------ MEDIO VERIFICACION ------------------------------------------------ */


    function clickGuardarNuevoMedioVerificacion(){
        msjError = validarfrmMedioVerificacion();
        if(msjError!=""){
            alerta(msjError);
            return ;
        }

        nombreArchivoMedioVerificacion = document.getElementById('nombreArchivoMedioVerificacion').value;
        if(nombreArchivoMedioVerificacion!=""){ //si ha ingresado un archivo, ahí sí necesitamos recargar la página pq no podemos mandarlo coon JS
            document.frmMedioVerificacion.submit();
            return;
        }
        


        descripcionNuevoMedio = document.getElementById('descripcionNuevoMedio').value;
        ComboBoxIndicadoresResultados = document.getElementById('ComboBoxIndicadoresResultados').value;
        codMedioVerificacion= document.getElementById('codMedioVerificacion').value;
         
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            codProyecto : {{$proyecto->codProyecto}},

            descripcionNuevoMedio: descripcionNuevoMedio,
            ComboBoxIndicadoresResultados : ComboBoxIndicadoresResultados,
            codMedioVerificacion : codMedioVerificacion
        }; 
        ruta = "{{route('GestionProyectos.agregarMedioVerificacion',$proyecto->codProyecto)}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalMedioVerificacion');
    }


    /* El editar se aplica tanto como para subir el archivo por primera vez, como para sobreescribirlo */
    function clickEditarMedioVerificacion(codMedioVerificacion){
        medio = mediosVerificacion.find(element => element.codMedioVerificacion == codMedioVerificacion);


        document.getElementById('codMedioVerificacion').value = medio.codMedioVerificacion;
        document.getElementById('ComboBoxIndicadoresResultados').value = medio.codIndicadorResultado;
        document.getElementById('descripcionNuevoMedio').value = medio.descripcion;
        
        document.getElementById('TituloModalMedioVerificacion').innerHTML = "Editar Medio Verificación";
        if(medio.tieneArchivo=='1')
            variableMsj = 'Remplazar';
        else 
            variableMsj = 'Subir';

        
        
        text = /* html */
        variableMsj+` Archivo
          <i class="fas fa-upload"></i>
        `;
        document.getElementById('divFileImagenEnvio2').innerHTML= text;


    }

    function limpiarMedioVerificacion(){

        document.getElementById('codMedioVerificacion').value = "0";
        document.getElementById('ComboBoxIndicadoresResultados').value ="-1";
        document.getElementById('descripcionNuevoMedio').value = "";
        
        document.getElementById('TituloModalMedioVerificacion').innerHTML= "Registrar Medio Verificación";

        text = /* html */
        `  
        `;
        document.getElementById('divFileImagenEnvio2').innerHTML= text;


    }

    function validarfrmMedioVerificacion(){
        
         
        msjError = "";
        msjError = validarSelect(msjError,'ComboBoxIndicadoresResultados','-1','Indicador de Resultado')
        msjError = validarTamañoMaximoYNulidad(msjError,'descripcionNuevoMedio',tamañoMaxMedioVerificacion,'Descripción del medio de verificación')

        return msjError;
    }


    var listaArchivos = '';
    //se ejecuta cada vez que escogewmos un file
    function cambioArchivoMedioVer(){
        msjError = validarPesoArchivosMedios();
        if(msjError!=""){
            alerta(msjError);
            return;
        }

        listaArchivos="";
        cantidadArchivos = 1
        
        index = 0;
        nombre = document.getElementById('filenamesMedio').files[index].name;
        document.getElementById("divFileImagenEnvio2").innerHTML= nombre;

        $('#nombreArchivoMedioVerificacion').val(nombre);
    }
            
    function validarPesoArchivosMedios(){        
        msj="";
        index = 0;
        
        var imgsize = document.getElementById('filenamesMedio').files[index].size;
        nombre = document.getElementById('filenamesMedio').files[index].name;
        if(imgsize > {{App\Configuracion::pesoMaximoArchivoMB}}*1000*1000 ){
            msj=('El archivo '+nombre+' supera los  {{App\Configuracion::pesoMaximoArchivoMB}}Mb, porfavor ingrese uno más liviano o comprima.');
        }

        return msj;

    }


    codMedioVerificacionAEliminar=0;
    function clickEliminarMedioVerificacion(codMedioVerificacion){

        codMedioVerificacionAEliminar = codMedioVerificacion;
        medio = mediosVerificacion.find(element => element.codMedioVerificacion == codMedioVerificacion);
        desc = medio.descripcion
        confirmarConMensaje('Confirmación',
            '¿Seguro de eliminar el medio de verificación "'+desc+'" ?','warning',
            ejecutarEliminacionMedioVerificacion);

    }

    function ejecutarEliminacionMedioVerificacion(){
        ruta = "/GestionProyectos/MedioVerificacionResultado/"+codMedioVerificacionAEliminar+"/eliminar"
        
        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            recargarTodosLosInvocables();
        });


    }


</script>
