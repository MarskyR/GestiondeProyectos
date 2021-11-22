@extends('Layout.Plantilla')

@section('titulo')
    @if($reposicion->verificarEstado('Abonada'))
        Contabilizar
    @else 
        Ver
    @endif
Reposición
@endsection

@section('contenido')

@include('Layout.EstilosPegados')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div class="row">
    <div class="col-md-10">
        <p class="h1" style="text-align:center">
            @if($reposicion->verificarEstado('Abonada'))
            Contabilizar
            @else 
            Ver
            @endif
            
            
            Reposición de Gastos
        </p>
    </div>

    <div class="col-md-2">
        
        <a  href="{{route('ReposicionGastos.exportarPDF',$reposicion->codReposicionGastos)}}" 
            class="btn btn-warning btn-sm btn-right" style="">
            <i class="entypo-pencil"></i>
            Descargar PDF
        </a>
        <a target="blank" href="{{route('ReposicionGastos.verPDF',$reposicion->codReposicionGastos)}}" 
            class="btn btn-warning btn-sm btn-right">
            <i class="entypo-pencil"></i>
            Ver PDF
        </a>
    </div>
</div>



<form method = "POST" action = "{{route('ReposicionGastos.Empleado.store')}}" onsubmit="return validarTextos()"  enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$reposicion->codEmpleadoSolicitante}}">
    <input type="hidden" name="codReposicionGastos" id="codReposicionGastos" value="{{$reposicion->codReposicionGastos}}">
    
    @csrf
   
    
    <div class="container" >
        <div class="row">           
            <div class="col-md"> {{-- COLUMNA IZQUIERDA 1 --}}
                <div class="container"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}

                    <div class="row">
                        <div  class="colLabel">
                                <label for="fecha">Fecha</label>
                        </div>
                        <div class="col">
                                                
                            <input type="text"  class="form-control" name="fechaHoy" id="fechaHoy" disabled
                                value="{{$reposicion->getFechaHoraEmision()}}" >     
                                     
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
                        <div  class="colLabel">
                                <label for="ComboBoxProyecto">Proyecto</label>

                        </div>
                        <div class="col"> {{-- input de proyecto --}}
                            <input type="text" class="form-control" name="codProyecto" id="codProyecto" value="{{$reposicion->getProyecto()->nombre}}" disabled>
                        </div>
                    
                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel">
                                <label for="fecha">Moneda</label>

                        </div>

                        <div class="col">
                            <input type="text" class="form-control" name="codMoneda" id="codMoneda" value="{{$reposicion->getMoneda()->nombre}}" disabled>
                        </div>
                        

                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel">
                                <label for="fecha">Banco</label>

                        </div>

                        <div class="col">
                            <input type="text" class="form-control" name="codBanco" id="codBanco" value="{{$reposicion->getBanco()->nombreBanco}}" disabled>  
                        </div>



                        <div class="w-100"></div> {{-- SALTO LINEA --}}
                        <div  class="colLabel">
                                <label for="fecha">Código Cedepas</label>

                        </div>

                        <div class="col">
                            <input type="text" class="form-control" name="" id="" value="{{$reposicion->codigoCedepas}}" disabled>  
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md"> {{-- COLUMNA DERECHA --}}
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <label for="fecha">Resumen de la actividad</label>
                            <textarea class="form-control" name="resumen" id="resumen" 
                                aria-label="With textarea" style="height:50px;" disabled
                                >{{$reposicion->resumen}}</textarea>
                            
                        </div>
                    </div>

                    <div class="container row"> {{-- OTRO CONTENEDOR DENTRO DE LA CELDA --}}
                    <div  class="colLabel2">
                            <label for="fecha">CuentaBancaria</label>

                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="numeroCuentaBanco" id="numeroCuentaBanco" 
                        value="{{$reposicion->numeroCuentaBanco}}" readonly>  
                    </div>

                    <div class="w-100"></div> {{-- SALTO LINEA --}}
                    <div  class="colLabel2">
                            <label for="fecha">Girar a Orden de </label>

                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="girarAOrdenDe" id="girarAOrdenDe" 
                        value="{{$reposicion->girarAOrdenDe}}" readonly>  
                    </div>

                    <div class="w-100"></div>
                    <div class="colLabel2">
                        <label for="">Estado:</label>
                    </div>

                    <div class="col">
                        <input type="text" value="{{$reposicion->getNombreEstado()}}" class="form-control" readonly 
                            style="background-color: {{$reposicion->getColorEstado()}};
                                    width:95%;
                                    color: {{$reposicion->getColorLetrasEstado()}} ;
                            ">
                    </div>





                    </div>

                </div>

                
                
            </div>
        </div>
    </div>
    <br>
         
    <div class="table-responsive">                           
        <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" style='background-color:#FFFFFF;'> 
            
            <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                <th width="11%" class="text-center">Fecha Cbte</th>                                        
                <th width="14%">Tipo</th>                                 
                <th width="11%"> N° Cbte</th>
                <th width="26%" class="text-center">Concepto </th>
            
                <th width="11%" class="text-center">Importe </th>
                <th width="11%" class="text-center">Cod Presup </th>                                         
                <th>Contabilizar</th>
            </thead>
            <tfoot>

                                                                                
            </tfoot>
            <tbody>
          
                @foreach($detalles as $itemdetalle)
                    <tr>
                        <td>{{$itemdetalle->fechaComprobante}}</td>
                        <td>{{$itemdetalle->getNombreTipoCDP()}}</td>
                        <td>{{$itemdetalle->nroComprobante}}</td>
                        <td>{{$itemdetalle->concepto}}</td>
                
                        <td style="text-align: right">{{number_format($itemdetalle->importe,2)}}</td>
                       
                        <td style="text-align: center"> {{$itemdetalle->codigoPresupuestal}}</td>
                        <td style="text-align:center;">               
                            <input id="checkBoxContabilizarItem{{$itemdetalle->codDetalleReposicion}}" type="checkbox"  readonly
                            @if($reposicion->verificarEstado('Contabilizada')) {{-- Ya está contabilizada --}}
                                @if($itemdetalle->contabilizado=='1')
                                    checked
                                @endif    
                                onclick="return false;"
                            @else {{-- Caso normal cuando se está contabilizando --}}
                                onclick="contabilizarItem({{$itemdetalle->codDetalleReposicion}})"
                            @endif
                            
                            >
                        </td>    
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
    
            
              
    <input type="{{App\Configuracion::getInputTextOHidden()}}" id="listaContabilizados" 
    name = "listaContabilizados" value="">

    <div class="row" id="divTotal" name="divTotal">     
        <div class="col">
            @include('ReposicionGastos.DesplegableDescargarArchivosRepo')
            <a href="{{route('ReposicionGastos.Contador.Listar')}}" 
                class='btn btn-info float-left float-left'>
                <i class="fas fa-arrow-left"></i> 
                Regresar al Menú
            </a>    
        </div>
        
        <div class="col">
            <div class="row">
                
                <div class="col">                        
                    <label for="">Total Gastado: </label>    
                </div>   
                <div class="col">
                    {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                    <input type="hidden" name="cantElementos" id="cantElementos">
                    <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
                    <input type="hidden" name="totalRendido" id="totalRendido">

                    <input type="text" class="form-control text-right" 
                        name="total" id="total" readonly value="{{$reposicion->getMoneda()->simbolo}} {{number_format($reposicion->totalImporte,2)}}">   

                </div>   
                <div class="w-100"></div>
                <div class="col"></div>
                <div class="col">
                    @if($reposicion->codEstadoReposicion==3)
                    <div class="col">
                        <button id="botonContabilizar" type="button" onclick="guardarContabilizar()"
                            class='btn btn-success'  style="float:right;">
                            <i class="fas fa-check"></i>
                            Guardar como Contabilizado
                        </button>    
                    </div>
                    @endif
                </div>


            </div>
        </div>

        

    
    </div>

    
    <br>
        
    
    <div class="col-md-12 text-center">  
        <div id="">
            <div class="form-group">
                          
            </div>    
        </div>

    </div>




</form>

@php
    $listaOperaciones = $reposicion->getListaOperaciones();
@endphp
@include('Operaciones.ModalHistorialOperaciones')


@endsection

{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}
{{-- ************************************************************************************************************* --}}

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

<style>


    .hovered:hover{
    background-color:rgb(97, 170, 170);
    
}

    input[type='checkbox'] {
        /* -webkit-appearance:none; */
        width:25px;
        height:25px;
        background:white;
        border-radius:15px;
        border:2px solid #555;
    }
    input[type='checkbox']:checked {
        background: #abd;
    }

    </style>

@section('script')



       {{-- PARA EL FILE  --}}
<script type="application/javascript">
        @if (App\Configuracion::enProduccion)
            document.getElementById('listaContabilizados').type = "hidden"
        @endif
        var listaItems = [];//para contabilizar
        function contabilizarItem(item){
            
            if( listaItems.indexOf(item)==-1  )
            { //no lo tiene 
                listaItems.push(item);
            }
            else 
            { //ya lo tiene, lo quitamos
                let pos = listaItems.indexOf(item);
                listaItems.splice(pos,1);
                
            }
           
            $('#listaContabilizados').val(listaItems);

        }
        /*
        function guardarContabilizar (){

            
            codReposicion = {{$reposicion->codReposicionGastos}};

            msjExtra = "";
            if(listaItems.length==0){
                msjExtra = "No ha marcado ningún Item... ";
            }

            swal({//sweetalert
                title: msjExtra+'¿Seguro de contabilizar la reposicion?',
                text: '',
                type: 'info',  
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText:  'SÍ',
                cancelButtonText:  'NO',
                closeOnConfirm:     true,//para mostrar el boton de confirmar
                html : true
            },
            function(){
                location.href = '/ReposicionGastos/'+ codReposicion +'*' +listaItems+'/Contabilizar';
            });
            
        }
        */
        function guardarContabilizar() {
            codReposicion = {{$reposicion->codReposicionGastos}};

            msjExtra = "";
            if(listaItems.length==0){
                alerta('No ha marcado ningún Item... ');
                msjExtra = "No ha marcado ningún Item... ";
                return false;
            }
            confirmarConMensaje('¿Seguro de contabilizar la reposicion?','','warning',ejecutarContabilizar);
        }
        function ejecutarContabilizar() {
            codReposicion = {{$reposicion->codReposicionGastos}};
            location.href = '/ReposicionGastos/'+ codReposicion +'*' +listaItems+'/Contabilizar';
        }

        
    
    
    </script>
     










@endsection
