{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIcono('iconoGiradorItemsPobBen')" data-toggle="collapse" href="#collapsePobBen" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Poblacion Beneficiaria
                </h4>
                <i id="iconoGiradorItemsPobBen" class="vaAGirar fas fa-plus" style="float:right"></i>
            </div>

        </a>
        <div id="collapsePobBen" class="panel-collapse collapse card-body p-0">

           
        </div>
    </div>    
</div>

<div class="modal fade" id="ModalPoblacionBeneficiaria" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalPoblacionBeneficiaria">Agregar Población Beneficiaria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     
                            
                    <form action="{{route('GestionProyectos.agregarPoblacionBeneficiaria')}}" method="POST" id="FrmagregarPoblacionBeneficiaria" name="FrmagregarPoblacionBeneficiaria">
                        @csrf
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPoblacionBeneficiaria" id="codPoblacionBeneficiaria" value="0">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codProyecto"  value="{{$proyecto->codProyecto}}">
                        
                        <div class="row  m-2">
                            <div class="col-sm">
                                <label for="">Descripción de la población:</label>

                            </div>
                            <div class="w-100"></div>
                            <div class="col-sm">
                                <textarea class="form-control" name="descripcionPob" id="descripcionPob" cols="30" rows="5"
                                ></textarea>
                            </div>    
                        </div>
                        

                        
                
                    </form>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="m-1 btn btn-primary"onclick="clickAgregarPoblacion()">
                        Guardar <i class="fas fa-save"></i>
                    </button>   
                </div>
            
        </div>
    </div>
</div>
 
  
<script>
    

 

    /* Se ejecuta antes de abrirse el modal para nueva poblacion ben */
    function limpiarModalPoblacionBeneficiaria(){
        document.getElementById('descripcionPob').value ="";
        document.getElementById('codPoblacionBeneficiaria').value ="0";
        document.getElementById('TituloModalPoblacionBeneficiaria').innerHTML ="Agregar Población Beneficiaria";
    }

    function clickEditarPoblacionBeneficiaria(codPoblacionBeneficiaria){
        poblacion = poblacionesBeneficiarias.find(element => element.codPoblacionBeneficiaria == codPoblacionBeneficiaria);


        document.getElementById('descripcionPob').value =poblacion.descripcion;
        document.getElementById('codPoblacionBeneficiaria').value =poblacion.codPoblacionBeneficiaria;
        document.getElementById('TituloModalPoblacionBeneficiaria').innerHTML ="Editar Población Beneficiaria";

    }

    /* click en guardar poblacion */
    function clickAgregarPoblacion(){
         
        limpiarEstilos(['descripcionPob']);
        
        msj = "";
        msj = validarTamañoMaximoYNulidad(msj,'descripcionPob',{{App\Configuracion::tamañoDescripcionPB}},'Descripción de la población');
        document.getElementById('descripcionPob').rows = 5;
        
        if(msj!=""){
            alerta(msj)
            return;
        }
        

        
        descripcionPob = document.getElementById('descripcionPob').value;
        codPoblacionBeneficiaria = document.getElementById('codPoblacionBeneficiaria').value;
        csrf = document.getElementsByName('_token')[0].value;
        
        datosAEnviar = {
            _token:   csrf,
            codProyecto : {{$proyecto->codProyecto}},

            codPoblacionBeneficiaria : codPoblacionBeneficiaria,
            descripcionPob: descripcionPob
        }; 
        ruta = "{{route('GestionProyectos.agregarPoblacionBeneficiaria')}}";
         
        $.post(ruta, datosAEnviar, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            recargarTodosLosInvocables();
        });

        cerrarModal('ModalPoblacionBeneficiaria'); 

    }
 



    /* ELIMINAR POBLACION  */
    codPoblacionBeneficiariaAEliminar = "0";
    function confirmarEliminarPoblacionBeneficiaria(codPoblacionBeneficiaria){
        console.log('Se eliminará el ' + codPoblacionBeneficiaria);
        
        codPoblacionBeneficiariaAEliminar = codPoblacionBeneficiaria;
        pob = poblacionesBeneficiarias.find(element => element.codPoblacionBeneficiaria == codPoblacionBeneficiaria);
        descripcion = pob.descripcion;
        confirmarConMensaje("Confirmación",'¿Seguro de eliminar la poblacion beneficiaria "'+descripcion+'" ?',"warning",ejecutarEliminacionPoblacionBeneficiaria);

    }

    function ejecutarEliminacionPoblacionBeneficiaria(){
        ruta ="/GestionProyectos/eliminarPoblacionBeneficiaria/" + codPoblacionBeneficiariaAEliminar;
        $.get(ruta, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            
            recargarTodosLosInvocables();
        });

    }



</script>
