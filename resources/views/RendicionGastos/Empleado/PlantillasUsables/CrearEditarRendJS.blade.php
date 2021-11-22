<style>
    
    .hovered:hover{
        background-color:rgb(97, 170, 170);

    }
</style>


{{-- CODIGO QUE SE REUTILIZA EN LAS VISTAS DE CREAR Y EDITAR --}}
<script>

    

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

    
    function validarFormEdit(){ //Retorna TRUE si es que todo esta OK y se puede hacer el submit
        msj='';
            
        
        limpiarEstilos(['resumen']);
        msj = validarTamañoMaximoYNulidad(msj,'resumen',{{App\Configuracion::tamañoMaximoResumen}},'Resumen');
        msj = validarCantidadMaximaYNulidadDetalles(msj,'cantElementos',{{App\Configuracion::valorMaximoNroItem}});

        /* if($('#nombresArchivos').val()=="" ) 
            msj='Debe subir los archivos comprobantes de pago.';
   */
        seleccionadoAñadirArchivos = document.getElementById('ar_añadir').checked;
        seleccionadoSobrescribirArchivos = document.getElementById('ar_sobrescribir').checked;
            
        if(seleccionadoAñadirArchivos || seleccionadoSobrescribirArchivos){
            //ahora sí validamos si se ha ingresado algun archivo
            if( document.getElementById('nombresArchivos').value ==""){    
                msjAux="Debe seleccionar los archivos que se "
                if(seleccionadoAñadirArchivos)
                    msjAux+="añadirán";
                else 
                    msjAux+= "sobrescribirán";
                
                msj = msjAux;
            }
        }
        if( document.getElementById('nombresArchivos').value !=""){
            if(!seleccionadoAñadirArchivos && !seleccionadoSobrescribirArchivos){
                msj = "Seleccione la modalidad con la que se subirán los archivos.";
            }
        }
        return msj;

    }


    function getNombreImagen(index){
            string = detalleRend[index].nombreImagen;
            //console.log('el nobmre de la imagen es:'+string);
            if(string==undefined)
                return "Subir Archivo"
            return string;            
    }

      


    
    indexAEliminar = 0;
    /* Eliminar productos */
    function eliminardetalle(index){
        indexAEliminar = index;
        confirmarConMensaje("Confirmación","¿Desea eliminar el item N° "+(index+1)+"?",'warning',ejecutarEliminacionDetalle);    
    }

    function ejecutarEliminacionDetalle(){
        cont = cont -1;
        $('#cantElementos').val(cont);
        detalleRend.splice(indexAEliminar,1);
        
        console.log('BORRANDO LA FILA' + indexAEliminar);
        //cont--;
        actualizarTabla();
    }



    
    function calcularNroEnRendicionMayor(){
        mayor = 0;
        for (let index = 0; index < detalleRend.length; index++) {
            if(mayor < detalleRend[index].nroEnRendicion)
                mayor = detalleRend[index].nroEnRendicion;
        }
        return mayor;
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
            msjError="";
            // VALIDAMOS
            limpiarEstilos(['fechaComprobante','ComboBoxCDP','ncbte','concepto','importe','codigoPresupuestal']);

            msjError = validarNulidad(msjError,'fechaComprobante','Fecha del comprobante del gasto');
            msjError = validarSelect(msjError,'ComboBoxCDP',-1,'Tipo de comprobante del gasto');            
            msjError = validarTamañoMaximoYNulidad(msjError,'ncbte',{{App\Configuracion::tamañoMaximoNroComprobante}},'Número de comprobante del gasto');
            msjError = validarTamañoMaximoYNulidad(msjError,'concepto',{{App\Configuracion::tamañoMaximoConcepto}},'Concepto');
            msjError = validarPositividadYNulidad(msjError,'importe','Importe');
            msjError = validarTamañoMaximoYNulidad(msjError,'codigoPresupuestal',{{App\Configuracion::tamañoMaximoCodigoPresupuestal}},'Código Presupuestal');
            msjError = validarCodigoPresupuestal(msjError,'codigoPresupuestal', codPresupProyecto,'Código presupuestal');
            
            fecha = document.getElementById('fechaComprobante').value;
            tipo = document.getElementById('ComboBoxCDP').value;
            ncbte = document.getElementById('ncbte').value;
            concepto = document.getElementById('concepto').value;
            importe  = document.getElementById('importe').value;
            codigoPresupuestal = document.getElementById('codigoPresupuestal').value;

            
            if(msjError!=""){
                alerta(msjError);
                return false;
            }
            
            // FIN DE VALIDACIONES
            if(detalleRend.length>0){
                temp=0;
                band=true;

                for (let item = 0; item < detalleRend.length && band; item++) {
                    element = detalleRend[item];
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
                maximo = calcularNroEnRendicionMayor()+1;
                detalleRend.splice(temp,0,{
                    nroEnRendicion: maximo,
                    fecha:fecha,
                    tipo:tipo,
                    ncbte,ncbte,
                    concepto:concepto,
                    importe:importe,            
                    codigoPresupuestal:codigoPresupuestal
                });
            }else{
                maximo = calcularNroEnRendicionMayor()+1;
                detalleRend.push({
                    nroEnRendicion: maximo,
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
            //ACTUALIZAMOS LOS VALORES MOSTRADOS TOTALES    
            //$('#total').val(number_format(total,2)); //TOTAL INCLUIDO IGV
            /* $('#fechaComprobante').val(''); */
            $('#ComboBoxCDP').val(0);
            $('#ncbte').val('');
            
            $('#concepto').val('');
            $('#importe').val('');
            $('#codigoPresupuestal').val('');
            
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
        for (let item = 0; item < detalleRend.length; item++) {
            detalleRend[item].nroEnRendicion=item+1;
            element = detalleRend[item];
            
            cont = item+1;

            total=total +parseFloat(element.importe); 

            //importes.push(importe);
            //item = getUltimoIndex();
            var fila=   /*   */
                        `
                        <tr class="selected" id="fila`+item+`" name="fila` +item+`">            

                            <td style="text-align:center;">              
                                                        
                                <input type="text" class="form-control" name="nroEnRendicion`+item+`" id="nroEnRendicion`+item+`" value="`+element.nroEnRendicion+`" readonly>
                            </td>               
                            <td style="text-align:center;">              
                                <input type="text" class="form-control" name="colFecha`+item+`" id="colFecha`+item+`" value="`+element.fecha+`" readonly style="font-size:10pt;"  >
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
                                <input type="text" class="form-control" value="`+parseFloat(element.importe).toFixed(2)+`" readonly>
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
                        </tr>                 `;


            $('#detalles').append(fila); 
        }

        $('#total').val(parseFloat(total).toFixed(2));
        $('#totalRendido').val(total); //el que se va a leer
        

        var totalGastado= parseFloat(total)    ;
        var totalRecibido= parseFloat( {{$solicitud->totalSolicitado}}  ); 
        //console.log(' total= '+total +'       tot2= ' +{{$solicitud->totalSolicitado}});

        saldoFavEmpl = (totalGastado)-(totalRecibido);
        

        
        
        //console.log("{{Carbon\Carbon::now()->format('d/m/Y')}}" );
        $('#fechaComprobante').val( "{{Carbon\Carbon::now()->format('d/m/Y')}}" );
    
        
        $('#cantElementos').val(cont);
        


        if(saldoFavEmpl>0){ //recibido < gastado -> el empleado debe recibir dinero de cedepas para reponer
            $('#saldoAFavor').val(  parseFloat(saldoFavEmpl).toFixed(2)  ); //puedo hacer esto sin que haya el error pq el input esta disabled
        
            

            document.getElementById("labelAFavorDe").innerHTML= "Saldo a Favor del Colaborador";
        }else{ //recibido > gastado el empleado debe enviar el dinero que no uso
            $('#saldoAFavor').val(  parseFloat(-saldoFavEmpl).toFixed(2)   ); //puedo hacer esto sin que haya el error pq el input esta disabled
            
            document.getElementById("labelAFavorDe").innerHTML= "Saldo a Favor de Cedepas";
        }

        //alerta('se termino de actualizar la tabla con cont='+cont);
    }
    


    function editarDetalle(index){
        
        $('#fecha').val( detalleRend[index].fecha );

        $('#ComboBoxCDP').val( detalleRend[index].tipo );
        $('#ncbte').val( detalleRend[index].ncbte );
        $('#concepto').val( detalleRend[index].concepto );
        $('#importe').val( detalleRend[index].importe );
        $('#codigoPresupuestal').val( detalleRend[index].codigoPresupuestal );
        
        indexAEliminar = index;
        ejecutarEliminacionDetalle();
    }
</script>
