{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIcono('iconoGiradorResEspAc')" data-toggle="collapse" href="#collapseResAct" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Resultados Esperados y Actividades
                </h4>
                <i id="iconoGiradorResEspAc" class="vaAGirar fas fa-plus" style="float:right"></i>
            </div>

        </a>
        <div id="collapseResAct" class="panel-collapse collapse card-body">
 
            
            

        </div>

       

    


    </div>    
</div>
  
{{-- <script>

            
    tamañoMaximoActividad = {{App\Configuracion::tamañoMaximoActividadResultado}};
    tamañoMaximoIndicadorActividad = {{App\Configuracion::tamañoMaximoIndicadorActividad}};


    actividadAEliminar="";
    function clickEliminarActividad(codActividad){
        
        actividad = actividadesResultados.find(element => element.codActividad == codActividad);
        nombreActividad = actividad.descripcion;
        if(actividad.cantidadIndicadores!=0){
            alerta("Para eliminar la actividad, debe eliminar todos sus indicadores.");
            return;
        }
        
        actividadAEliminar=codActividad;
        
        confirmarConMensaje('Confirmacion','¿Seguro que desea eliminar la actividad "'+nombreActividad+'" ?','warning',ejecutarEliminacionActividadRes);


    }   
    function ejecutarEliminacionActividadRes(){

        ruta = "/GestionProyectos/eliminarActividad/" + actividadAEliminar;

        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });
    
    }

    




    function clickAgregarActividad(){

        msjError = validarfrmActividad();
        if(msjError!="")
        {
            alerta(msjError);
            return ;
        }

 
        descripcionNuevaActividad = document.getElementById('descripcionNuevaActividad').value;
        ComboBoxResultadoEsperado = document.getElementById('ComboBoxResultadoEsperado').value;
        codActividad =  document.getElementById('codActividad').value;
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            codProyecto : {{$proyecto->codProyecto}},
            
            descripcionNuevaActividad: descripcionNuevaActividad,
            ComboBoxResultadoEsperado : ComboBoxResultadoEsperado,
            codActividad : codActividad
        }; 

        ruta = "{{route('GestionProyectos.agregarEditarActividad')}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalActividad');
        


    }

    function validarfrmActividad(){

        codResultadoEsperado = document.getElementById('ComboBoxResultadoEsperado').value;
        descripcionNuevaActividad = document.getElementById('descripcionNuevaActividad').value;
        

        /* FALTAN VALIDACIONES DE LONGITUD */
        msjError = "";
        if(codResultadoEsperado=="-1")
        {
            msjError="Debe seleccionar el resultado esperado.";
        }


        if(descripcionNuevaActividad.length > tamañoMaximoActividad){
            msjError = "La descripción debe tener máximo " + tamañoMaximoActividad +" caracteres ,actualmente tiene "+  descripcionNuevaActividad.length ;
        }


        if(descripcionNuevaActividad=="")
        {
            msjError="Debe ingresar la descripción.";            
        }


        return msjError;
    }


    function clickEditarActividad(codActividad){
        actividad = actividadesResultados.find(element => element.codActividad == codActividad);
        console.log('actividad encontrada');
        console.log(actividad);
        document.getElementById('codActividad').value = actividad.codActividad;
        document.getElementById('ComboBoxResultadoEsperado').value = actividad.codResultadoEsperado;
        document.getElementById('descripcionNuevaActividad').value = actividad.descripcion;
        document.getElementById('TituloModalActividad').innerHTML = "Editar Actividad";

        
    }
    function limpiarModalActividad(){

        document.getElementById('codActividad').value = "0";
        document.getElementById('ComboBoxResultadoEsperado').value ="-1";
        document.getElementById('descripcionNuevaActividad').value = "";
        document.getElementById('TituloModalActividad').innerHTML = "Agregar Actividad";

    }

 

 





    /* INDICADORES DE ACTIVIDADES */

    function clickAgregarIndicador(){
        
        msjError = validarFrmAgregarIndicador();
        if(msjError!="")
        {
            alerta(msjError);
            return ;
        }

 
        unidadNuevoIndicador = document.getElementById('unidadNuevoIndicador').value;
        ComboBoxActividad = document.getElementById('ComboBoxActividad').value;
        codIndicadorActividad =  document.getElementById('codIndicadorActividad').value;
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            codProyecto : {{$proyecto->codProyecto}},
            
            unidadNuevoIndicador: unidadNuevoIndicador,
            ComboBoxActividad : ComboBoxActividad,
            codIndicadorActividad : codIndicadorActividad
        }; 

        ruta = "{{route('GestionProyectos.agregarEditarIndicadorActividad')}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalIndicadorActividad');

    } 

    function validarFrmAgregarIndicador(){

        codActividad = document.getElementById('ComboBoxActividad').value;
         
        unidadNuevoIndicador= document.getElementById('unidadNuevoIndicador').value;


        msjError = "";
        if(codActividad=="-1")
        {
            msjError="Debe seleccionar la actividad que corresponde al indicador.";
        }
         
        if(unidadNuevoIndicador=="")
        {
            msjError="Debe ingresar la unidad de medida.";            
        }

        if(unidadNuevoIndicador.length > tamañoMaximoIndicadorActividad){
            msjError = "La unidad de medida debe tener máximo " + tamañoMaximoIndicadorActividad +" caracteres ,actualmente tiene "+  unidadNuevoIndicador.length ;
        }




        return msjError;


    }

    function clickEditarIndicadorActividad(codIndicadorActividad){

        indicadorActividad = indicadoresActividades.find(element => element.codIndicadorActividad == codIndicadorActividad);

        console.log("IMPRIMIENDO INDICADOR");
        console.log(indicadorActividad);
        
        document.getElementById('codIndicadorActividad').value = indicadorActividad.codIndicadorActividad;
        document.getElementById('ComboBoxActividad').value = indicadorActividad.codActividad;
        
        document.getElementById('unidadNuevoIndicador').value = indicadorActividad.unidadMedida;
        document.getElementById('metaIndicador').value = indicadorActividad.meta;
        
        document.getElementById('TituloModalIndicadorActividad').innerHTML = "Editar Indicador de Actividad";
        

    }

    function limpiarModalIndicador(){

        document.getElementById('codIndicadorActividad').value = "0";
        document.getElementById('ComboBoxActividad').value = "-1";
        
        document.getElementById('unidadNuevoIndicador').value = "";
        document.getElementById('metaIndicador').value = "";
        document.getElementById('TituloModalIndicadorActividad').innerHTML = "Agregar Indicador de Actividad";
        
        
        

    }

    codIndicadorActividadAEliminar = 0;
    function clickEliminarIndicadorActividad(codIndicadorActividad){
        
        indicadorActividad = indicadoresActividades.find(element => element.codIndicadorActividad == codIndicadorActividad);
        codIndicadorActividadAEliminar  = codIndicadorActividad; 

        confirmarConMensaje('Confirmación','¿Desea eliminar el indicador "' + indicadorActividad.unidadMedida+'" ? Serán eliminadas todas sus metas programadas y ejecutadas.','warning',ejecutarEliminacionEliminarIndicadorActividad);
    }

    function ejecutarEliminacionEliminarIndicadorActividad(){
        ruta = "/GestionProyectos/EliminarIndicadorActividad/" + codIndicadorActividadAEliminar ;
        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });

    }

</script> --}}
