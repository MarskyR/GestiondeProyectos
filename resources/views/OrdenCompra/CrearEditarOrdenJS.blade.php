<style>


    .CBgrande{
      width: 60px;
      height: 30px;
  
    }
    .colLabel2{
        margin-top: 3px;
    }
</style>

{{-- CODIGO JUNTADO DE JS QUE USAN EN COMUN EL CREAR Y EDITAR ORDEN DE COMPRA --}}
@include('Layout.ValidatorJS')
   
<script type="application/javascript">
    var igv=0.18;
    var cont=0;
    var total=0;
    var detalleOrden=[];
    var listaArchivos = '';

    function clickDiv(orden){
        switch (orden) {
            case 1:
                radioButton = document.getElementById("valorVentaRadio");
                document.getElementById("valorVentaDetalle").readOnly=false;
                document.getElementById("precioVentaDetalle").readOnly=true;
                break;
            case 2:
                radioButton = document.getElementById("precioVentaRadio");
                document.getElementById("valorVentaDetalle").readOnly=true;
                document.getElementById("precioVentaDetalle").readOnly=false;
                break;
        }
        radioButton.checked = true;
    }
    function cambioTipoValorRadio() {
        radioBottom = document.getElementById("valorVentaRadio").checked; 
        cantidad = $("#cantidadDetalle").val();
        console.log(radioBottom);
        
        if (document.getElementById("valorVentaRadio").checked){
            document.getElementById("valorVentaDetalle").readOnly=false;
            document.getElementById("precioVentaDetalle").readOnly=true;
            //$("#precioVentaDetalle").val(0);
        }else{
            document.getElementById("valorVentaDetalle").readOnly=true;
            document.getElementById("precioVentaDetalle").readOnly=false;
            //$("#valorVentaDetalle").val(0);
        }

        precioVenta = $("#precioVentaDetalle").val();
        total=precioVenta*cantidad;
        //$("#totalDetalle").val(number_format(precioVenta*cantidad,2)); 
        $("#totalDetalle").val(total.toFixed(2)); 
    }
    function cambioValorInput() {
        exoneradoIGV = document.getElementById("exoneradoIGV").checked; 
        radioBottom = document.getElementById("valorVentaRadio").checked; 
        console.log(exoneradoIGV);

        valorVenta = $("#valorVentaDetalle").val(); 
        precioVenta = $("#precioVentaDetalle").val();
        cantidad = $("#cantidadDetalle").val();

        if (exoneradoIGV){
            if (radioBottom){
                //$("#precioVentaDetalle").val(number_format(valorVenta*(1+igv),2)); 
                precioTemp=valorVenta*(1+igv);
                //$("#precioVentaDetalle").val(precioTemp.toFixed(2)); 
                $("#precioVentaDetalle").val(precioTemp); 
            }else{
                //$("#valorVentaDetalle").val(number_format(1*precioVenta/(1+igv),2)); 
                valorTemp=1*precioVenta/(1+igv);
                //$("#valorVentaDetalle").val(valorTemp.toFixed(2)); 
                $("#valorVentaDetalle").val(valorTemp); 
                console.log("entro");
            }
            
        }else{
            if (radioBottom){
                //$("#precioVentaDetalle").val(number_format(valorVenta,2)); 
                $("#precioVentaDetalle").val(valorVenta); 
            }else{
                //$("#valorVentaDetalle").val(number_format(precioVenta,2)); 
                $("#valorVentaDetalle").val(precioVenta); 
            }
        }
        precioVenta = $("#precioVentaDetalle").val();
        total=precioVenta*cantidad;
        //$("#totalDetalle").val(number_format(precioVenta*cantidad,2)); 
        $("#totalDetalle").val(total.toFixed(2)); 
    }
    function cambioEstadoIGV() {
        exoneradoIGV = document.getElementById("exoneradoIGV").checked; 
        cantidad = $("#cantidadDetalle").val();
        if(exoneradoIGV){
            valorVenta = $("#valorVentaDetalle").val(); 
            //$("#precioVentaDetalle").val(number_format(valorVenta*(1+igv),2));
            precioTemp=valorVenta*(1+igv);
            //$("#precioVentaDetalle").val(precioTemp.toFixed(2));
            $("#precioVentaDetalle").val(precioTemp);
        }else{
            valorVenta = $("#valorVentaDetalle").val(); 
            //$("#precioVentaDetalle").val(number_format(valorVenta,2));
            $("#precioVentaDetalle").val(valorVenta);
        }

        precioVenta = $("#precioVentaDetalle").val();
        total=precioVenta*cantidad;
        //$("#totalDetalle").val(number_format(precioVenta*cantidad,2)); 
        $("#totalDetalle").val(total.toFixed(2)); 
    }

    function agregarDetalle(){
        msjError = "";
        // VALIDAMOS
        limpiarEstilos(['cantidadDetalle','comboUnidadMedida','descripcionDetalle','valorVentaDetalle','precioVentaDetalle','totalDetalle']);

        msjError = validarPositividadYNulidad(msjError,'cantidadDetalle','Cantidad');
        msjError = validarSelect(msjError,'comboUnidadMedida',-1,'Unidada de Medida');
        msjError = validarTamañoMaximoYNulidad(msjError,'descripcionDetalle',{{App\Configuracion::tamañoDescripcionOC}},'Descripcion');  
        //msjError = validarPositividadYNulidad(msjError,'valorVentaDetalle','Valor de Venta');
        //msjError = validarPositividadYNulidad(msjError,'precioVentaDetalle','Precio de Venta');   
        //msjError = validarPositividadYNulidad(msjError,'totalDetalle','Subtotal');
        if (parseFloat($("#valorVentaDetalle").val())<=0) msjError="Por favor ingrese el valor de venta del detalle.";  
        if (parseFloat($("#precioVentaDetalle").val())<=0) msjError="Por favor ingrese el precio de venta del detalle.";  
        
        if(msjError!=""){
            alerta(msjError);
            return false;
        }



        cantidad = $("#cantidadDetalle").val();   
        codUnidadMedida = $("#comboUnidadMedida").val();
        descripcionUnidadMedida = document.getElementById('comboUnidadMedida').options[document.getElementById('comboUnidadMedida').selectedIndex].text;  
        valorVenta = $("#valorVentaDetalle").val();  
        descripcion= $("#descripcionDetalle").val(); 
        precioVenta = $("#precioVentaDetalle").val();  
        subtotal = $("#totalDetalle").val();
        exoneradoIGV = document.getElementById("exoneradoIGV").checked; 
        if(exoneradoIGV){
            exoneradoIGV=1;
        }else exoneradoIGV=0;


        // FIN DE VALIDACIONES
        detalleOrden.push({
            cantidad:cantidad,
            descripcion:descripcion,
            valorVenta:valorVenta,
            precioVenta:precioVenta,
            subtotal:subtotal,

            exoneradoIGV:exoneradoIGV,
            codUnidadMedida:codUnidadMedida,
            descripcionUnidadMedida:descripcionUnidadMedida
        });

        //console.log(detalleOrden);

        cont++;
        actualizarTabla();

        $('#cantidadDetalle').val(1);
        $('#comboUnidadMedida').val(-1);

        $('#descripcionDetalle').val('');
        $('#valorVentaDetalle').val(0);
        $('#precioVentaDetalle').val(0);
        $('#totalDetalle').val(0);
    }
    function editarDetalle(index){

        $('#cantidadDetalle').val(detalleOrden[index].cantidad);
        $('#comboUnidadMedida').val(detalleOrden[index].codUnidadMedida);

        $('#descripcionDetalle').val(detalleOrden[index].descripcion);
        $('#valorVentaDetalle').val(detalleOrden[index].valorVenta);
        $('#precioVentaDetalle').val(detalleOrden[index].precioVenta);
        $('#totalDetalle').val(detalleOrden[index].subtotal);

        if(detalleOrden[index].exoneradoIGV==1){
            document.getElementById("exoneradoIGV").checked=true; 
        }else document.getElementById("exoneradoIGV").checked=false; 


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
        detalleOrden.splice(indexAEliminar,1);
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
        if(detalleOrden.length==0){
            cont=0;
        }
        for (let item = 0; item < detalleOrden.length; item++) {
            element = detalleOrden[item];
            cont = item+1;

            total=total +parseFloat(element.subtotal); 

            esExonerado=element.exoneradoIGV;
            if(esExonerado==0){
                esExonerado='';
            }else esExonerado='checked';

            var fila= 
                /*   */
                    `  <tr class="selected" id="fila`+item+`" name="fila` +item+`">          
                            <td style="text-align:center;">               
                                <input type="text" class="form-control" name="colCantidad`+item+`" id="colCantidad`+item+`" value="`+element.cantidad+`" readonly>
                            </td>             
                            <td style="text-align:center;">               
                                <input type="text" class="form-control" value="`+element.descripcionUnidadMedida+`" readonly>
                                <input type="hidden" class="form-control" name="colUnidadMedida`+item+`" id="colUnidadMedida`+item+`" value="`+element.codUnidadMedida+`" readonly>
                            </td> 
                            <td style="text-align:center;">               
                                <input type="text" class="form-control" name="colDescripcion`+item+`" id="colDescripcion`+item+`" value="`+element.descripcion+`" readonly>
                            </td> 
                            <td style="text-align:center;">               
                                <input type="text" class="form-control" value="`+parseFloat(element.valorVenta).toFixed(2)+`" readonly>
                                <input type="hidden" class="form-control" name="colValorVenta`+item+`" id="colValorVenta`+item+`" value="`+(element.valorVenta)+`" readonly>
                            </td> 
                            <td style="text-align:center;">               
                                <input type="text" class="form-control" value="`+parseFloat(element.precioVenta).toFixed(2)+`" readonly>
                                <input type="hidden" class="form-control" name="colPrecioVenta`+item+`" id="colPrecioVenta`+item+`" value="`+(element.precioVenta)+`" readonly>
                            </td> 
                            <td  style="text-align:right;">              
                                <input type="text" class="form-control" style="text-align:right" value="`+parseFloat(element.subtotal).toFixed(2)+`" readonly>
                                <input type="hidden" class="form-control" name="colSubTotal`+item+`" id="colSubTotal`+item+`" value="`+(element.subtotal)+`" readonly>
                            </td>   
                            <td class="text-center"> 
                                 
                                    <input type="checkbox" `+esExonerado+` disabled style="transform: scale(1.5);">
                                    <input type="hidden" class="form-control" name="colExonerado`+item+`" id="colExonerado`+item+`" value="`+element.exoneradoIGV+`" readonly>
                                           
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

        $('#total').val(total);
        $('#totalMostrado').val(number_format(total,2));
        $('#cantElementos').val(cont);
        
    }
    

    function actualizarCodPresupProyecto(){
        codProyecto = $('#codProyecto').val();
        $.get('/obtenerCodigoPresupuestalDeProyecto/'+codProyecto, 
            function(data)
            {   
                codPresupProyecto = data.substring(0,2); //Pa agarrarle solo los 2 digitos
                console.log('Se ha actualizado el codPresupuestal del proyecto:[' +codPresupProyecto+"]" );

                document.getElementById('partidaPresupuestal').placeholder = codPresupProyecto+"...";
            }
        );
    }

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

    
    
    function validarFormularioCrear(){
        msj='';
        limpiarEstilos(['codProyecto','codMoneda','partidaPresupuestal','señores','ruc','direccion',
                        'atencion','referencia','observacion']);

        msj = validarSelect(msj,'codProyecto',-1,'proyecto');
        msj = validarSelect(msj,'codMoneda',-1,'moneda');
        
        msj = validarTamañoMaximoYNulidad(msj,'partidaPresupuestal',{{App\Configuracion::tamañoSeñoresOC}},'código presupuestal');
        msj = validarCodigoPresupuestal(msj,'partidaPresupuestal',codPresupProyecto,'código presupuestal');

        msj = validarTamañoMaximoYNulidad(msj,'señores',{{App\Configuracion::tamañoSeñoresOC}},'señores');
        msj = validarTamañoExacto(msj,'ruc',11,'RUC');
        msj = validarTamañoMaximoYNulidad(msj,'direccion',{{App\Configuracion::tamañoSeñoresOC}},'direccion');
        msj = validarTamañoMaximoYNulidad(msj,'atencion',{{App\Configuracion::tamañoAtencionOC}},'atencion');
        msj = validarTamañoMaximoYNulidad(msj,'referencia',{{App\Configuracion::tamañoReferenciaOC}},'referencia');
        msj = validarTamañoMaximoYNulidad(msj,'observacion',{{App\Configuracion::tamañoObservacionOC}},'observacion');
            
        if( $('#cantElementos').val()<=0 ){
            msj='Debe ingresar Items';
        }
        //if($('#nombresArchivos').val()=="" ) msj='Debe subir los archivos comprobantes de pago.';
        
        return msj;
    }
</script>