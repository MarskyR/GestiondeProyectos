

{{-- ESTA PLANTILLA SOLO CONTIENE LOS CAMPOS Y EL DESPLEGABLE DE LOS ITEMS DE SOLICITUD
    , NO CONTIENE LA TABLA --}}

<div class="container" >
    <div class="row">           
        <div class="col-md" > {{-- COLUMNA IZQUIERDA 1 --}}
            <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                <div class="row">
                    <div class="colLabel">
                            <label for="fecha">Fecha:</label>
                    </div>
                    <div class="col">
                        
                        <input type="text"  class="form-control" name="fechaHoy" id="fechaHoy" disabled
                            value="{{$rendicion->formatoFechaHoraRendicion()}}" >     
                                
                    </div>
                    @if(App\Configuracion::activarVerHistorialDeDocumentos)
                            
                    
                        <div class="col">
                            <button type="button" class="btn btn-primary btn-sm fontSize8" style=""  
                                data-toggle="modal" data-target="#ModalHistorial">
                                Ver Historial  
                            </button>
                        </div>
                    @endif


                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div class="colLabel">
                            <label for="ComboBoxProyecto">Proyecto:</label>

                    </div>
                    <div class="col"> {{-- input de proyecto --}}
                        <input readonly type="text" class="form-control" name="proyecto" id="proyecto" 
                        value="{{"[".$solicitud->getProyecto()->codigoPresupuestal."] ".$solicitud->getNombreProyecto()}}">    
        
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div class="colLabel">
                            <label for="fecha">Colaborador:</label>

                    </div>
                    <div class="col">
                            <input  readonly type="text" class="form-control" name="colaboradorNombre" id="colaboradorNombre" value="{{$empleado->getNombreCompleto()}}">    

                    </div>
                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div class="colLabel">
                            <label for="fecha">Cod Colaborador:</label>
                    </div>

                    <div class="col">
                            <input readonly  type="text" class="form-control" name="codColaborador" id="codColaborador" value="{{$empleado->codigoCedepas}}">    
                    </div>
                    
                    <div class="colLabel">
                            <label for="fecha">Importe Recibido:</label>

                    </div>
                    <div class="col">
                            <input readonly  type="text" class="form-control" name="importeRecibido" 
                                id="importeRecibido" value="{{number_format($solicitud->totalSolicitado,2)}}">    
                    </div>
                    




                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div class="colLabel">
                            <label for="codSolicitud">Cod Sol. Fondos:</label>

                    </div>
                    <div class="col">
                            <input value="{{$solicitud->codigoCedepas}}" type="text" class="form-control" name="codSolicitud" id="codSolicitud" readonly>     
                    </div>
                  
                    


                </div>


            </div>
            
            
            
            
        </div>
        

        <div class="col-md"> {{-- COLUMNA DERECHA --}}
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label for="fecha">Resumen de la actividad <b id="contador" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                        <textarea class="form-control" name="resumen" id="resumen" readonly aria-label="With textarea" rows="4"
                         >{{$rendicion->resumenDeActividad}}</textarea>
        
                    </div>
                </div>

                <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                    <div class="row">
                        

                        

                        <div class="w-100"></div> {{-- SALTO LINEA --}}

                        <div  class="colLabel">
                                <label for="estado">Estado de <br> la Rend
                                    @if($rendicion->verificarEstado('Observada')){{-- Si está observada --}}& Obs @endif:</label>
                        </div>
                        <div class="col"> {{-- Combo box de estado --}}
                            <textarea readonly type="text" class="form-control" name="estado" id="estado"
                            style="background-color: {{$rendicion->getColorEstado()}} ;
                                color:{{$rendicion->getColorLetrasEstado()}};   "readonly 
                            >{{$rendicion->getNombreEstado()}}{{$rendicion->getObservacionONull()}}
                            </textarea>
                                      
                        </div>
                        <div class="w-100"></div> {{-- SALTO LINEA --}}



                        <div class="colLabel">
                            <label for="fecha">Cod. Rendición:</label>
                        </div>
                        <div class="col">
                             <input type="text" class="form-control" name="codigoCedepas" id="codigoCedepas" readonly value="{{$rendicion->codigoCedepas}}">     
                        </div>
    

                    </div>

                    

                </div>

            </div>

            
            
        </div>
    </div>
</div>

@include('SolicitudFondos.Plantillas.DesplegableDetallesSOF')  


@php
    $listaOperaciones = $rendicion->getListaOperaciones();
@endphp
@include('Operaciones.ModalHistorialOperaciones')


@include('Layout.EstilosPegados')