
<style>
    
    .hovered:hover{
        background-color:rgb(97, 170, 170);

    }
</style>

<script>





var codPresupProyecto = -1;
function actualizarCodPresupProyecto(){
    codProyecto = $('#codProyecto').val();
    $.get('/obtenerCodigoPresupuestalDeProyecto/'+codProyecto, 
        function(data)
        {   
            codPresupProyecto = data.substring(0,2); //Pa agarrarle solo los 2 digitos
            console.log('Se ha actualizado el codPresupuestal del proyecto:[' +codPresupProyecto+"]" );

            document.getElementById('codigoPresupuestal').placeholder = codPresupProyecto+"...";
        }
        );

}

function compararFechas(fecha, fechaIngresada){//1 si la fecha ingresada es menor (dd-mm-yyyy)
    diaActual=fechaIngresada.substring(0,2);
    mesActual=fechaIngresada.substring(3,5);
    anioActual=fechaIngresada.substring(6,10);
    dia=fecha.substring(0,2);
    mes=fecha.substring(3,5);
    anio=fecha.substring(6,10);


    if(parseInt(anio,10)>parseInt(anioActual,10)){
        //console.log('el año ingresado es menor');
        return 1;
    }else if(parseInt(anio,10)==parseInt(anioActual,10)){
        
        if(parseInt(mes,10)>parseInt(mesActual,10)){
            //console.log('el mes ingresado es menor');
            return 1;
        }else if(parseInt(mes,10)==parseInt(mesActual,10)){

            if(parseInt(dia,10)>parseInt(diaActual,10)){
                //console.log('el dia ingresado es menor');
                return 1;
            }else{
                return 0;
            }

        }else return 0;

    }else return 0;

}


function agregarDetalle(){
    msjError = "";
    // VALIDAMOS
    limpiarEstilos(['fechaComprobante','ComboBoxCDP','ncbte','concepto','codProyecto','importe','codigoPresupuestal']);

    if( codPresupProyecto == -1  ) msjError="Por favor seleccione un proyecto antes de añadir Items.";  
    msjError = validarSelect(msjError,'ComboBoxCDP',-1,'Tipo de CDP');  
    msjError = validarNulidad(msjError,'fechaComprobante','Fecha de Comprabante');
    msjError = validarTamañoMaximoYNulidad(msjError,'ncbte',{{App\Configuracion::tamañoMaximoNroComprobante}},'Numero de CDP'); 
    msjError = validarTamañoMaximoYNulidad(msjError,'concepto',{{App\Configuracion::tamañoMaximoConcepto}},'Concepto');   
    msjError = validarTamañoMaximoYNulidad(msjError,'codigoPresupuestal',{{App\Configuracion::tamañoMaximoCodigoPresupuestal}},'Código Presupuestal');
    msjError = validarCodigoPresupuestal(msjError,'codigoPresupuestal',codPresupProyecto,'Código presupuestal');
    msjError = validarPositividadYNulidad(msjError,'importe','Importe');

    if(msjError!=""){
        alerta(msjError);
        return false;
    }

    fecha = $("#fechaComprobante").val();       
    tipo = $("#ComboBoxCDP").val(); 
    ncbte= $("#ncbte").val();   
    concepto=$("#concepto").val();  
    importe=$("#importe").val();    
    codigoPresupuestal=$("#codigoPresupuestal").val();     //el que agregó el user
    
    
    // FIN DE VALIDACIONES
    if(detalleRepo.length>0){
        temp=0;
        band=true;

        for (let item = 0; item < detalleRepo.length && band; item++) {
            element = detalleRepo[item];
            //console.log('FECHA Nº' + item + ': '+element.fecha+' se compara con '+fecha);
            if(compararFechas(element.fecha, fecha)==1){ 
                //console.log('mi fecha es menor');
                temp=item;
                band=false;
            }else{
                //console.log('mi fecha es mayor');
                temp=item+1;
            }
        }
        detalleRepo.splice(temp,0,{
            fecha:fecha,
            tipo:tipo,
            ncbte,ncbte,
            concepto:concepto,
            importe:importe,            
            codigoPresupuestal:codigoPresupuestal
        });
    }else{
        detalleRepo.push({
            fecha:fecha,
            tipo:tipo,
            ncbte,ncbte,
            concepto:concepto,
            importe:importe,            
            codigoPresupuestal:codigoPresupuestal
        });
    }
    


    cont++;
    actualizarTabla();

    $('#fechaComprobante').val("");
    $('#ComboBoxCDP').val(0);
    $('#ncbte').val('');

    $('#concepto').val('');
    $('#importe').val('');
    $('#codigoPresupuestal').val('');
}

function editarDetalle(index){

    $('#fechaComprobante').val( detalleRepo[index].fecha );
    $('#ComboBoxCDP').val( detalleRepo[index].tipo );
    $('#ncbte').val( detalleRepo[index].ncbte );
    $('#concepto').val( detalleRepo[index].concepto );
    $('#importe').val( detalleRepo[index].importe );
    $('#codigoPresupuestal').val( detalleRepo[index].codigoPresupuestal );

    indexAEliminar = index;
    ejecutarEliminacionDetalle();
}

    


    indexAEliminar = 0;
    /* Eliminar productos */
    function eliminardetalle(index){
        indexAEliminar = index;
        confirmarConMensaje("Confirmación","¿Desea eliminar el item N° "+(index+1)+"?",'warning',ejecutarEliminacionDetalle);    
    }



function ejecutarEliminacionDetalle(){


    cont = cont - 1;
    $('#cantElementos').val(cont);
    detalleRepo.splice(indexAEliminar,1);
    console.log('BORRANDO LA FILA' + indexAEliminar);
    actualizarTabla();


}


function actualizarTabla(){
    //funcion para poner el contenido de detallesVenta en la tabla
    //tambien actualiza el total
    //$('#detalles')
    total=0;
    //vaciamos la tabla
    for (let index = 100; index >=0; index--) {
        $('#fila'+index).remove();
        //console.log('borrando index='+index);
    }
    
    //insertamos en la tabla los nuevos elementos
    for (let item = 0; item < detalleRepo.length; item++) {
        element = detalleRepo[item];
        cont = item+1;

        total=total +parseFloat(element.importe); 

        //importes.push(importe);
        //item = getUltimoIndex();
        var fila= 
            /* html */
                `  
                    <tr class="selected" id="fila`+item+`" name="fila` +item+`">          
                        <td style="text-align:center;">               
                            <input type="text" class="form-control" name="colFecha`+item+`" id="colFecha`+item+`" value="`+element.fecha+`" readonly>
                        </td>             
                    
                        <td style="text-align:center;">            
                            <input type="text" class="form-control" name="colTipo`+item+`" id="colTipo`+item+`" value="`+element.tipo+`" readonly>
                        </td>              
                        <td style="text-align:center;">               
                            <input type="text" class="form-control" name="colComprobante`+item+`" id="colComprobante`+item+`" value="`+element.ncbte+`" readonly>
                        </td>             
                        <td>

                            <input type="text" class="form-control" name="colConcepto`+item+`" id="colConcepto`+item+`" value="`+element.concepto+`" readonly>
                        </td>              
                    
                    

                        <td  style="text-align:right;">              
                            <input type="text" class="form-control" style="text-align:right" value="`+number_format(element.importe,2)+`" readonly>
                            <input type="hidden" class="form-control" name="colImporte`+item+`" id="colImporte`+item+`" value="`+(element.importe)+`" readonly>
                        </td>              
                        <td style="text-align:center;">              
                        <input type="text" class="form-control" name="colCodigoPresupuestal`+item+`" id="colCodigoPresupuestal`+item+`" value="`+element.codigoPresupuestal+`" readonly>
                        </td>              
                        <td style="text-align:center;">              
                            <button type="button" class="btn btn-danger btn-xs" onclick="eliminardetalle(`+item+`);">
                                <i class="fa fa-times" ></i>              
                            </button>             
                            <button type="button" class="btn btn-xs" onclick="editarDetalle(`+item+`);">
                                <i class="fas fa-pen"></i>            
                            </button> 
                            
                        </td>               
                    


                    </tr>                 
                    
                    `;


        $('#detalles').append(fila); 
    }

    $('#total').val(number_format(total,2));
    $('#cantElementos').val(cont);
    
}



var listaArchivos = '';
    
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
        console.log('Archivo ' + index + ': '+ nombreAr);
        listaArchivos = listaArchivos +', '+  nombreAr; 
    }
    listaArchivos = listaArchivos.slice(1, listaArchivos.length);
    document.getElementById("divFileImagenEnvio").innerHTML= listaArchivos;
    $('#nombresArchivos').val(listaArchivos);
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
    

    return msj;

}

</script>
