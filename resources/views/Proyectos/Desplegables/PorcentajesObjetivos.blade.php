{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

<div class="panel-group card">
    <div class="panel panel-default">
        <a id="giradorItems" onclick="girarIcono('iconoGiradorPorcentajesObjetivos')" data-toggle="collapse" href="#collapsePorcentajesObj" style=""> 
            <div class="panel-heading card-header" style="">
                <h4 class="panel-title card-title" style="">
                    Obj Estratégicos y del Milenio
                </h4>
                <i id="iconoGiradorPorcentajesObjetivos" class="vaAGirar fas fa-plus" style="float:right"></i>
            </div>
        </a>
        <div id="collapsePorcentajesObj" class="panel-collapse collapse card-body p-0"  style="margin-top: 5px">

           
            <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">
            <div class="row">
                <div class="col-sm text-center">
                    <label for="">PEI del proyecto:</label>
                </div>

                <div class="col-sm">
                    <select class="form-control" name="codPEI" id="codPEI" {{$disabled}} onchange="clickCambiarPEI()">
                        <option value="-1">-- Seleccionar --</option>
                        @foreach($listaPEIs as $itemPEI)
                        <option value="{{$itemPEI->codPEI}}"
                            @if($itemPEI->codPEI == $proyecto->codPEI)
                                selected
                            @endif
                            >{{$itemPEI->getPeriodo()}}</option>    
                        @endforeach
                    </select>
                    
                </div>
            </div>
             
            <form action="" id="formPorcentajesObjetivos" name="formPorcentajesObjetivos" method="post">
                @csrf

                <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">

                <div id="contenedorTablaObjEstr" class="table-responsive" style="">                           
                    
                </div> 

                <div class="row"  >
                    <div class="col"></div>
                   
                    <div class="col">
                        @if($proyecto->sePuedeEditar())
                        
                        <button type="button" onclick="clickGuardarPorcentajesEstrategicos()" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar % Obj Estratégicos
                        </button>
                        @endif
                    </div>
                    
                </div>
            </form>


            <div>
                
                <label class="m-1" for="">
                    Objetivos del milenio:
                </label>
                <input type="hidden" name="codProyecto" value="{{$proyecto->codProyecto}}">

                <div class="table-responsive">                           
                    <table id="" class="table table-striped table-bordered table-condensed table-hover table-sm fontSize9 m-2" style='background-color:#FFFFFF; width:95%;'> 
                        <thead class="thead-default" style="background-color:#3c8dbc;color: #fff; ">
                            <th width="5%"> Item</th>
                            <th  width="50%" class="text-center">Descripción Objetivo del Milenio</th>                                        
                            <th   width="20%" class="text-center">
                                0-100% 

                                
                            </th>                                        
                        </thead>
                        <tbody>
                            @foreach($relacionesObjMilenio as $relacion)
                                <tr class="selected">
                                    <td>
                                        {{$relacion->getObjetivoMilenio()->item}}
                                        
                                    </td>
                                    
                                    <td  style="text-align:center;">               
                                        {{$relacion->getObjetivoMilenio()->descripcion}}
                                    </td>       
                                    <td  style="text-align:right;"> 
                                        
                                        <input class="form-control text-right " step="1"  type="number" min="0" max="100" onchange="actualizarPorcentajeTotalMilenio()"
                                        name="porcentajeObjMilenio{{$relacion->codRelacion}}" id="porcentajeObjMilenio{{$relacion->codRelacion}}"  
                                            {{$readonly}} value="{{$relacion->porcentaje}}" 
                                        >
                                    </td>               
                                </tr>                

                            @endforeach  
                                <tr style="background-color: rgb(170, 170, 170)">
                                    <td></td>
                                    <td class="fontSize12 text-right">
                                        <b >
                                            Total:
                                        </b>
                                        
                                    </td>
                                    <td class="text-center fontSize12" >
                                        {{-- Aqui se muestra el total --}}
                                        <b id="totalPorcentajeMilenio">
                                             {{$proyecto->getTotalAportesMilenio()}}
                                        </b>
                                        

                                    </td>
                                </tr>
                        </tbody>
                    </table>
                    
                </div> 

          
                <div class="row">
                    <div class="col">

                    </div>
                    <div class="col text-center">
                        @if($proyecto->sePuedeEditar())
                            <button type="button" onclick="clickGuardarPorcMilenio()" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Guardar % Obj Milenio
                            </button>
                        @endif
                    </div>
                </div>

            </div>









        </div>


    


    </div>    
</div>
{{-- FIN LISTADO DESPLEGABLE DE SOLICITUD ENLAZADA --}}

 
<script>

    

    function actualizarPorcentajeTotalEstrategicos(){
        elementoTotal = document.getElementById('totalPorcentajeEstrategicos');
        totalAcumulado = 0;

        porcentajesObjetivosEstrategicos.forEach(element => {

            nombreElemento = "porcentaje" + element.codRelacion;
            valor = document.getElementById(nombreElemento).value;
            totalAcumulado += parseFloat(valor);
        });
        
        elementoTotal.innerHTML = totalAcumulado;
        
        if(totalAcumulado==100){
            elementoTotal.className = 'letraVerde'; 
            return;
        }

        if(totalAcumulado<100)
            elementoTotal.className = 'letraAmarilla';
        else
            elementoTotal.className = 'letraRoja';
    }
    

    var codPEIActual = {{$proyecto->codPEI}};
    codPEISeleccionado = 0 ;
    
    function clickCambiarPEI(){
        const SelectorPEI = document.getElementById('codPEI');
        if(SelectorPEI.value == "-1")
        {
            alerta('Debes seleccionar un PEI');
            return;
        }
    
        codPEISeleccionado = SelectorPEI.value; //almacenamos el valor seleccionado
        SelectorPEI.value = codPEIActual; //seteamos en el SELECT el valor actual por si el usuario marca NO

        pei = listaPEIs.find(element => element.codPEI == codPEISeleccionado);
        periodo = pei.añoInicio +"-"+ pei.añoFin 
        confirmarConMensaje("Confirmación",'¿Desea actualizar el PEI del Proyecto "{{$proyecto->nombre}}" al PEI de  '+periodo+' ?',"warning",submitearActualizacionPEI);
        
    }

    function submitearActualizacionPEI(){
        SelectorPEI = document.getElementById('codPEI');
        codPEI = codPEISeleccionado;
        datosEnvio = 
            {
                _token: "{{csrf_token()}}",
                codProyecto: "{{$proyecto->codProyecto}}",
                codPEI : codPEI
            };

            
        ruta = "{{route('GestionProyectos.actualizarPEI')}}"
        $.post(ruta,datosEnvio, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
            recargarTodosLosInvocables();
            codPEIActual = codPEI;
            SelectorPEI.value = codPEI;
            codPEISeleccionado = 0;
             
        });


    }

    

    function clickGuardarPorcentajesEstrategicos(){

        actualizarPorcentajeTotalEstrategicos();
        total = parseFloat(document.getElementById('totalPorcentajeEstrategicos').innerHTML);
        if(total!=100){
            alerta('La suma de los aportes debe ser 100, el valor actual es ' + total);
            return;
        }

        confirmarConMensaje("Confirmación","¿Desea actualizar los porcentajes de los objetivos estratégicos?","warning",submitearActualizacionPorcentajes);
       


    }


    function submitearActualizacionPorcentajes(){

        //document.formPorcentajesObjetivos.submit();
        
        datosEnvio = 
            {
                _token: "{{csrf_token()}}",
                codProyecto: "{{$proyecto->codProyecto}}"
            };

        porcentajesObjetivosEstrategicos.forEach(element => {
            //  
            nombreElemento = "porcentaje" + element.codRelacion;
            datosEnvio[nombreElemento] = document.getElementById(nombreElemento).value;
        });
         
            
            
        ruta = "{{route('GestionProyectos.actualizarPorcentajesObjetivos')}}"
        $.post(ruta,datosEnvio, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
        });
        

    }


    /* OBJETIVOS DEL MILENIO */
    
    function actualizarPorcentajeTotalMilenio(){
        elementoTotal = document.getElementById('totalPorcentajeMilenio');
        totalAcumulado = 0;
        @foreach($relacionesObjMilenio as $relacion)
            totalAcumulado +=  parseFloat(document.getElementById('porcentajeObjMilenio{{$relacion->codRelacion}}').value);     
        @endforeach

        elementoTotal.innerHTML = totalAcumulado;
        
        if(totalAcumulado==100){
            elementoTotal.className = 'letraVerde'; 
            return;
        }

        if(totalAcumulado<100)
            elementoTotal.className = 'letraAmarilla';
        else
            elementoTotal.className = 'letraRoja';
    }
    
    function clickGuardarPorcMilenio(){
        msjError = validarActualizacionObjMilenio();
        if(msjError!=""){
            alerta(msjError);
            return;
        }

        

        confirmarConMensaje("Confirmación","¿Desea actualizar los porcentajes de los objetivos del Milenio?","warning",actualizarPorcentajesMilenio);
    }

    function validarActualizacionObjMilenio(){
        msjError = "";

        suma = 0;
        //validamos que todos sumen 100
        @foreach ( $relacionesObjMilenio as $relacion )
           
            nombreElemento = 'porcentajeObjMilenio{{$relacion->codRelacion}}';
            limpiarEstilos([nombreElemento]);
            objProvisional = document.getElementById(nombreElemento);
            suma += parseFloat(objProvisional.value);

            msjError = validarNoNegatividad(msjError,nombreElemento,'Aporte al objetivo del Milenio N°{{$relacion->getObjetivoMilenio()->item}}')

        @endforeach

        if(suma!=100){
            msjError = "La suma de los porcentajes debe ser 100% .La suma actual es " + suma;
            ponerEnRojoTodosObjMilenio();
        }


        return msjError;

    }
    function ponerEnRojoTodosObjMilenio(){

        @foreach ( $relacionesObjMilenio as $relacion )
            
            nombreElemento = 'porcentajeObjMilenio{{$relacion->codRelacion}}';
            ponerEnRojo(nombreElemento);
        @endforeach
    }

    function actualizarPorcentajesMilenio(){
        datosEnvio = 
            {
                _token: "{{csrf_token()}}",
                codProyecto: "{{$proyecto->codProyecto}}"
            };
         
        @foreach ($relacionesObjMilenio as $relacion)
            nombreElemento = 'porcentajeObjMilenio{{$relacion->codRelacion}}';

            datosEnvio.codRelacion{{$relacion->codRelacion}}   =  '{{$relacion->codRelacion}}';
            datosEnvio.codPorcentaje{{$relacion->codRelacion}} =  document.getElementById(nombreElemento).value;
        @endforeach

        console.log(datosEnvio);
        $.post("/GestionProyectos/actualizarPorcentajesMilenio",datosEnvio, function(dataRecibida){
            console.log('DATA RECIBIDA:');
            console.log(dataRecibida);
            objetoRespuesta = JSON.parse(dataRecibida);
            alertaMensaje(objetoRespuesta.titulo,objetoRespuesta.mensaje,objetoRespuesta.tipoWarning);
        });


    }








</script>
