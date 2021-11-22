{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIcono('iconoGiradorObjEspecif')" data-toggle="collapse" href="#collapseObjEspec" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Objetivos Especificos e Indicadores
                </h4>
                <i id="iconoGiradorObjEspecif" class="vaAGirar fas fa-plus" style="float:right"></i>
            </div>

        </a>
        <div id="collapseObjEspec" class="panel-collapse collapse card-body">

            

        </div>

    </div>    
</div>
 

<script>

 
    

     

    /* --------------------- OBJETIVO ESPECIFICO --------------------------- */
    function clickEditarObjEspecifico(codObj){
        objetivo = objetivosEspecificos.find(element => element.codObjEspecifico == codObj);

        document.getElementById('codObjetivoEspecifico').value = codObj;
        document.getElementById('descripcionObjetivo').value = objetivo.descripcion;
        document.getElementById('tituloModalObjetivoEspecifico').innerHTML = "Editar Objetivo Específico";
    }

    function clickAgregarObjEspecifico(){
        //limpiamos
        document.getElementById('codObjetivoEspecifico').value = "0";
        document.getElementById('descripcionObjetivo').value = "";
        document.getElementById('tituloModalObjetivoEspecifico').innerHTML = "Agregar Objetivo Específico";

    }


    function agregarObjetivoEspecifico(){
        msjError = validarObjEspecifico();
        if(msjError != ""){
            alerta(msjError);
            return;
        }

        //document.frmAgregarObjEspec.submit();
        
        textoNuevoObjetivo = document.getElementById('descripcionObjetivo').value;
        codObjetivoEspecifico = document.getElementById('codObjetivoEspecifico').value;
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            descripcionObjetivo: textoNuevoObjetivo,
            codProyecto : {{$proyecto->codProyecto}},
            codObjetivoEspecifico : codObjetivoEspecifico
        }; 
        ruta = "{{route('GestionProyectos.agregarEditarObjetivoEspecifico')}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalObjetivoEspecifico');
        
    }

    function validarObjEspecifico(){
        msjError="";

        
        descr = document.getElementById('descripcionObjetivo').value;
        if(descr == "")
            msjError = ('Debe ingresar una descripción');
           
        tamañoMax400 = {{App\Configuracion::tamañoMaximoDescripcionesLargasProyecto}};
        if(descr.length > tamañoMax400){
            msjError = "La descripción puede tener " + tamañoMax400 + " caracteres como máximo. La actual descripción tiene " + descr.length;
        }

        return msjError;

    }



    /* eliminar */
    codObjetivoEspecificoAEliminar = "0";
    function clickEliminarObjEspecifico(codObjEspecifico){

        objetivo = objetivosEspecificos.find(element => element.codObjEspecifico == codObjEspecifico);
        if(objetivo.cantidadIndicadores!=0){
            alerta("Para eliminar el objetivo específico, debe eliminar todos sus indicadores.")
            return;
        }

        console.log('Se eliminará el ' + objetivo);
        codObjetivoEspecificoAEliminar = codObjEspecifico;
        confirmarConMensaje('Confirmación','¿Seguro de eliminar el objetivo específico "'+objetivo.descripcion+
            '" ? Se eliminarán también sus indicadores',"warning",ejecutarEliminacionObjetivoEspecifico);

    }

    function ejecutarEliminacionObjetivoEspecifico(){
        ruta = "/GestionProyectos/eliminarObjetivoEspecifico/" + codObjetivoEspecificoAEliminar;

        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });
         

    }

    /* -------------------- INDICADORES DE OBJETIVO ---------------------- */

    function clickAgregarIndicadorObjetivo(){ //limpiamos el modal

        document.getElementById('TituloModalIndicadorObjetivoEspecifico').innerHTML = "Agregar Indicador";
        document.getElementById('codIndicadorObjetivo').value = 0;
        document.getElementById('descripcionNuevoIndicador').value = "";
        document.getElementById('ComboBoxObjetivoEspecifico').value = "-1";
        
    }

    function clickEditarIndicadorObjetivo(codIndicadorObj){

        indicador = indicadoresObjetivos.find(element => element.codIndicadorObj == codIndicadorObj);
        console.log(indicador);
        document.getElementById('TituloModalIndicadorObjetivoEspecifico').innerHTML = "Editar Indicador";
        document.getElementById('codIndicadorObjetivo').value = indicador.codIndicadorObj;
        document.getElementById('descripcionNuevoIndicador').value = indicador.descripcion;
        document.getElementById('ComboBoxObjetivoEspecifico').value = indicador.codObjEspecifico;
        
        

    }

    indicadorAEliminar="";
    function clickEliminarIndicador(codIndicador){
        indicador = indicadoresObjetivos.find(element => element.codIndicadorObj == codIndicador);
        
        indicadorAEliminar=codIndicador;
        confirmarConMensaje('Confirmacion','¿Seguro que desea eliminar el indicador "'+indicador.descripcion+'" ?','warning',ejecutarEliminacionIndicador);


    }   
    function ejecutarEliminacionIndicador(){

        ruta = "/GestionProyectos/eliminarIndicador/"+indicadorAEliminar;
        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            
            recargarTodosLosInvocables();
        });
    }

    function clickAgregarIndicadorObjEsp(){

        msjError = validarFrmIndicadorObjEsp();
        if(msjError!="")
        {
            alerta(msjError);
            return ;
        }

        //document.formAgregarEditarIndicador.submit();
        descripcionNuevoIndicador = document.getElementById('descripcionNuevoIndicador').value;
        ComboBoxObjetivoEspecifico = document.getElementById('ComboBoxObjetivoEspecifico').value;
        codIndicadorObjetivo =  document.getElementById('codIndicadorObjetivo').value;
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            descripcionNuevoIndicador: descripcionNuevoIndicador,
            codProyecto : {{$proyecto->codProyecto}},
            ComboBoxObjetivoEspecifico : ComboBoxObjetivoEspecifico,
            codIndicadorObjetivo : codIndicadorObjetivo
        }; 

        ruta = "{{route('GestionProyectos.agregarEditarIndicador')}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalIndicadorObjetivoEspecifico');


    }

    function validarFrmIndicadorObjEsp(){
        codObjEspecifico = document.getElementById('ComboBoxObjetivoEspecifico').value;
        descripcionNuevoIndicador = document.getElementById('descripcionNuevoIndicador').value;
        
        msjError = "";
        if(codObjEspecifico=="-1")
        {
            msjError="Debe seleccionar el objetivo específico.";
        }
        
        if(descripcionNuevoIndicador==""){
            msjError="Debe ingresar la descripción.";            
        }else if(descripcionNuevoIndicador.length>{{App\Configuracion::tamañoDescripcionIBE}} ){
            msjError='La longitud de la descripcion tiene que ser maximo de {{App\Configuracion::tamañoDescripcionIBE}} caracteres.';
            msjError=msjError+' El tamaño actual es de '+descripcionNuevoIndicador.length+' caracteres.';
        }


        return msjError;
    }






</script>