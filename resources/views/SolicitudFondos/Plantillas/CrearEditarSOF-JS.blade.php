
<style>
    
    
</style>

<script>


    
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
            for (let item = 0; item < detalleSol.length; item++) {
                element = detalleSol[item];
                cont = item+1;
    
                total=total +parseFloat(element.importe);
                itemMasUno = item+1;
                

                var fila=      /* html */
                            `<tr class="selected" id="fila`+item+`" name="fila` +item+`">               
                                <td style="text-align:center;">              
                                   <input type="text" class="form-control" name="colItem`+item+`" id="colItem`+item+`" value="`+itemMasUno+`" readonly>
                                </td>             
                                <td> 
                                   <input type="text" class="form-control" name="colConcepto`+item+`" id="colConcepto`+item+`" value="`+element.concepto+`" readonly>
                                </td>               
                                <td  style="text-align:right;">
                                   <input type="text" style="text-align:right;" class="form-control" value = "`+number_format(element.importe,2)+`" readonly> 
                                   <input type="hidden" style="text-align:right;" class="form-control" name="colImporte`+item+`" id="colImporte`+item+`" value="`+ (element.importe)+`" readonly>
                                </td>               
                                <td style="text-align:center;">              
                                <input type="text" class="form-control" style="text-align:center;" name="colCodigoPresupuestal`+item+`" id="colCodigoPresupuestal`+item+`" value="`+element.codigoPresupuestal+`" readonly>
                                </td>              
                                <td style="text-align:center;">              
                                    <button type="button" class="btn btn-danger btn-xs" onclick="eliminardetalle(`+item+`);">
                                        <i class="fa fa-times" ></i>               
                                    </button>       
                                    <button type="button" class="btn btn-xs" onclick="editarDetalle(`+item+`);">
                                        <i class="fas fa-pen"></i>            
                                    </button>        
                                </td>               
                            </tr>         `;
    
    
                $('#detalles').append(fila); 
            }
            
            $('#totalMostrado').val(number_format(total,2));
            $('#total').val(total);
            
            
            $('#cantElementos').val(cont);
            $('#item').val(cont+1);
            
          
            //alerta('se termino de actualizar la tabla con cont='+cont);
}
    
var codPresupProyecto = -1;
function actualizarCodPresupProyecto(){
    codProyecto = $('#ComboBoxProyecto').val();
    $.get('/obtenerCodigoPresupuestalDeProyecto/'+codProyecto, 
        function(data)
        {   
            codPresupProyecto = data.substring(0,2); //Pa agarrarle solo los 2 digitos
            console.log('Se ha actualizado el codPresupuestal del proyecto:[' +codPresupProyecto+"]" );
            document.getElementById('codigoPresupuestal').placeholder = codPresupProyecto+"...";
        }
        );

}

function editarDetalle(index){
    $('#concepto').val( detalleSol[index].concepto );
    $('#importe').val( detalleSol[index].importe );
    $('#codigoPresupuestal').val( detalleSol[index].codigoPresupuestal );
    
    indexAEliminar = index;
    ejecutarEliminacionDetalle();
}
        
function agregarDetalle(){
    msjError="";
    // VALIDAMOS 
    limpiarEstilos(['concepto','codigoPresupuestal','importe']);
    msjError = validarTamañoMaximoYNulidad(msjError,'concepto',{{App\Configuracion::tamañoMaximoConcepto}},'Concepto');
    msjError = validarTamañoMaximoYNulidad(msjError,'codigoPresupuestal',{{App\Configuracion::tamañoMaximoCodigoPresupuestal}},'Código Presupuestal');
    msjError = validarCodigoPresupuestal(msjError,'codigoPresupuestal',codPresupProyecto,'Código presupuestal');
    msjError= validarPositividadYNulidad(msjError,'importe','Importe');
    msjError= validarSelect(msjError,'ComboBoxProyecto','-1','Proyecto');
 
    if(msjError!=""){
        alerta(msjError);
        return false;
    }

    concepto = document.getElementById('concepto').value;
    importe = document.getElementById('importe').value;
    codigoPresupuestal = document.getElementById('codigoPresupuestal').value;
    

    // FIN DE VALIDACIONES

        item = cont+1;   
        detalleSol.push({
            item:item,
            concepto:concepto,
            importe:importe,            
            codigoPresupuestal:codigoPresupuestal
        });        
        cont++;
    actualizarTabla();
    //ACTUALIZAMOS LOS VALORES MOSTRADOS TOTALES    
    //$('#total').val(number_format(total,2)); //TOTAL INCLUIDO IGV
    $('#concepto').val('');
    $('#importe').val('');
    $('#codigoPresupuestal').val('');
    
}


indexAEliminar = 0;
/* Eliminar productos */
function eliminardetalle(index){
    indexAEliminar = index;
    confirmarConMensaje("Confirmación","¿Desea eliminar el item N° "+(index+1)+"?",'warning',ejecutarEliminacionDetalle);    
}

function ejecutarEliminacionDetalle(){
    //removemos 1 elemento desde la posicion index
    
    cont = cont - 1;
    $('#cantElementos').val(cont);

    detalleSol.splice(indexAEliminar,1);
    console.log('BORRANDO LA FILA' + indexAEliminar);
    actualizarTabla();
    indexAEliminar=0;

}


</script>