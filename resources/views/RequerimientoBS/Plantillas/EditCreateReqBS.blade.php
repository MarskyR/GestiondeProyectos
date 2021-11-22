
<style>
    
    .hovered:hover{
        background-color:rgb(97, 170, 170);

    }
</style>
@include('Layout.ValidatorJS')
   
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



function agregarDetalle(){
    // VALIDAMOS
    msjError = "";
    limpiarEstilos(['ComboBoxUnidad','cantidad','descripcion','codProyecto']);

    msjError= validarSelect(msjError,'codProyecto',-1,'Proyecto');

    msjError = validarSelect(msjError,'ComboBoxUnidad',-1,'Unidad de Medida');
    msjError = validarPositividadYNulidad(msjError,'cantidad','Cantidad');

    msjError = validarPuntoDecimal(msjError,'cantidad','Cantidad');

    msjError = validarTamañoMaximoYNulidad(msjError,'descripcion',{{App\Configuracion::tamañoMaximoDescripcion}},'Descripción')
    msjError = validarTamañoMaximoYNulidad(msjError,'codigoPresupuestal',{{App\Configuracion::tamañoMaximoCodigoPresupuestal}},'Código Presupuestal del item');
    msjError= validarCodigoPresupuestal(msjError,'codigoPresupuestal',codPresupProyecto,'Código Presupuestal');

    tipo = document.getElementById('ComboBoxUnidad').value;

    cantidad = document.getElementById('cantidad').value;
    codProyecto = document.getElementById('codProyecto').value;
    descripcion = document.getElementById('descripcion').value;
    codigoPresupuestal= document.getElementById('codigoPresupuestal').value;
    
    if(msjError!=""){
        //alerta(msjError);
        alerta(msjError);
        return false;
    }

    // FIN DE VALIDACIONES

    detalleReq.push({
        tipo:tipo,
        cantidad:cantidad,
        descripcion:descripcion,         
        codigoPresupuestal:codigoPresupuestal
    });        
    cont++;
    actualizarTabla();

    

    //$('#fechaComprobante').val("");
    $('#ComboBoxUnidad').val(0);
    $('#cantidad').val('');

    $('#descripcion').val('');
    $('#codigoPresupuestal').val('');
}

function editarDetalle(index){

    //$('#fechaComprobante').val( detalleReq[index].fecha );
    $('#ComboBoxUnidad').val( detalleReq[index].tipo );
    $('#cantidad').val( detalleReq[index].cantidad );
    $('#descripcion').val( detalleReq[index].descripcion );
    //$('#importe').val( detalleReq[index].importe );
    $('#codigoPresupuestal').val( detalleReq[index].codigoPresupuestal );

    indexAEliminar = index;
    
    ejecutarEliminacionDetalle();
}

indexAEliminar = 0;
/* Eliminar productos */
function eliminardetalle(index){
    
    $('#cantElementos').val(cont); 

    indexAEliminar = index;
    confirmarConMensaje("Confirmación","¿Desea eliminar el item N° "+(index+1)+"?",'warning',ejecutarEliminacionDetalle);    
}

/* Eliminar productos */
function ejecutarEliminacionDetalle(){
    cont = cont - 1;
    detalleReq.splice(indexAEliminar,1);
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
    for (let item = 0; item < detalleReq.length; item++) {
        element = detalleReq[item];
        cont = item+1;

        //total=total +parseFloat(element.importe); 

        //importes.push(importe);
        //item = getUltimoIndex();
        var fila= `  <tr class="selected" id="fila`+item+`" name="fila` +item+`">          
    
                    
                        <td style="text-align:center;">            
                            <input type="text" class="form-control" name="colTipo`+item+`" id="colTipo`+item+`" value="`+element.tipo+`" readonly>
                        </td>              
                        <td style="text-align:center;">               
                            <input type="text" class="form-control" name="colCantidad`+item+`" id="colCantidad`+item+`" value="`+element.cantidad+`" readonly>
                        </td>             
                        <td>

                            <input type="text" class="form-control" name="colDescripcion`+item+`" id="colDescripcion`+item+`" value="`+element.descripcion+`" readonly>
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
                    


                    </tr>                 `;


        $('#detalles').append(fila); 
    }

    //$('#total').val(number_format(total,2));
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
