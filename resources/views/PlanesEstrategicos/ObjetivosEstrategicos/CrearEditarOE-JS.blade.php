<script>
    function actualizarTabla(){
           
    
           for (let index = 100; index >=0; index--) {
               $('#fila'+index).remove();
           }
           
           //insertamos en la tabla los nuevos elementos
           for (let item = 0; item < detalleObj.length; item++) {
               element = detalleObj[item];
             
               

               var fila=   `<tr class="selected" id="fila`+item+`" name="fila` +item+`">               
                                <td>
                                    <input type="number" class="form-control" id="item`+item+`" name="item`+item+`" value="`+element.item+`" >
                                </td>
                                <td>
                                    <input type="text" class="form-control" id="nombre`+item+`" name="nombre`+item+`" value="`+element.nombre+`" readonly>
                                </td>

                               <td  style="text-align:right;">
                                  <textarea class="form-control" name="descripcion`+item+`" id="descripcion`+item+`" cols="30" rows="2" readonly>`+element.descripcion+`</textarea>
                                           
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
           $('#cantElementos').val(detalleObj.length);
           
                  
   }



       
   function agregarDetalle(){
       msjError="";
       // VALIDAMOS 
       descripcion = document.getElementById('nuevaDescripcion').value;
       if(descripcion==''){ 
           msjError=("Por favor ingrese la descripción");  
       }

       nombre = document.getElementById('nuevoNombre').value;
       if(nombre==''){ 
           msjError=("Por favor ingrese el nombre");  
       }

       
       if(msjError!=""){
           alerta(msjError);
           return false;
       }


       // FIN DE VALIDACIONES

       detalleObj.push({
           item: detalleObj.length+1,
           nombre: nombre,
           descripcion:descripcion
       
       });        
       
       actualizarTabla();    
       document.getElementById('nuevaDescripcion').value = "";
       document.getElementById('nuevoNombre').value = "";
       
   }

   function editarDetalle(index){
  
        document.getElementById('nuevaDescripcion').value = detalleObj[index].descripcion ;
        document.getElementById('nuevoNombre').value = detalleObj[index].nombre ;
        
       eliminardetalle(index);
   }



   function eliminardetalle(index){
       //removemos 1 elemento desde la posicion index
       detalleObj.splice(index,1);
       console.log('BORRANDO LA FILA' + index);
       actualizarTabla();

   }


</script>