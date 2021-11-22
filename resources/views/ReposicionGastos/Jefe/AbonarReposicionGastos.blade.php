@extends('Layout.Plantilla')

@section('titulo')
    @if($reposicion->verificarEstado('Aprobada'))
                    Abonar
                @else 
                    Ver
                @endif
            
                
                Reposición de Gastos
    @endsection
@section('contenido')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<div class="row">
    <div class="col-md-10">
        <p class="h1" style="margin-left:430px;">
            @if($reposicion->verificarEstado('Aprobada'))
                Abonar
            @else 
                Ver
            @endif
        
            
            Reposición de Gastos
        </p>
    </div>
    <div class="col-md-2">
        <br>
        <a  href="{{route('ReposicionGastos.exportarPDF',$reposicion->codReposicionGastos)}}" 
            class="btn btn-warning btn-sm btn-right" style="margin-left:60px;">
            <i class="entypo-pencil"></i>
            PDF
          </a>
        <a target="blank" href="{{route('ReposicionGastos.verPDF',$reposicion->codReposicionGastos)}}" 
            class="btn btn-warning btn-sm btn-right">
            <i class="entypo-pencil"></i>
            verPDF
        </a>
    </div>
</div>


<form method = "POST" action = "{{route('ReposicionGastos.Empleado.store')}}" onsubmit="return validarTextos()"  enctype="multipart/form-data">
    
    {{-- CODIGO DEL EMPLEADO --}}
    {{-- CODIGO DE LA SOLICITUD QUE ESTAMOS RINDIENDO --}}
    <input type="hidden" name="codEmpleado" id="codEmpleado" value="{{$reposicion->codEmpleadoSolicitante}}">
    <input type="hidden" name="codReposicionGastos" id="codReposicionGastos" value="{{$reposicion->codReposicionGastos}}">
    
    @csrf
    
    
    
    @include('ReposicionGastos.PlantillaVerREP')



                   
                
    <div class="row" id="divTotal" name="divTotal">                       
        <div class="col"  style="">

            @include('ReposicionGastos.DesplegableDescargarArchivosRepo')
            
        </div>   
        <div class="col-md-2" style="text-align:center">                        
            <label for="">Total Gastado: </label>    
        </div>   
        <div class="col-md-2">
            {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
            <input type="hidden" name="cantElementos" id="cantElementos">
            <input type="hidden" name="codigoCedepas" id="codigoCedepas">                          
            <input type="hidden" name="totalRendido" id="totalRendido">

            <input type="text" class="form-control text-right" name="total" 
                id="total" readonly value="{{number_format($reposicion->totalImporte,2)}}">   

        </div>   

        

    </div>
               
           
        
    <div class="col-md-12 text-center">  
        <div id="guardar">
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <a href="{{route('ReposicionGastos.Administracion.Listar')}}" class='btn btn-info float-left'>
                            <i class="fas fa-arrow-left"></i> Regresar al Menú
                        </a>  
                    </div>
                    <div class="col">
                        @if($reposicion->verificarEstado('Aprobada'))
                            <div class="row">
                                <div class="col">
                                    <a id="botonAbonar" href="#" class="btn btn-success float-right" onclick="clickAbonar()">
                                        <i class="fas fa-check"></i> Marcar como Abonada</a>
                                    <button type="button" class='btn btn-warning float-right' style="margin-right:6px;"
                                        data-toggle="modal" data-target="#ModalObservar">
                                        <i class="fas fa-eye-slash"></i>
                                        Observar
                                    </button> 
                                    <a href="#" class="btn btn-danger float-right" onclick="clickRechazar()" style="margin-right:6px;">
                                        <i class="fas fa-times"></i> Rechazar</a> 
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>    
        </div>
    </div>


</form>


    <!-- MODAL -->
    <div class="modal fade" id="ModalObservar" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="TituloModalObservar">Observar Reposición de Gastos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formObservar" name="formObservar" action="{{route('ReposicionGastos.observar')}}" method="POST">
                            @csrf
                            <input type="hidden" name="codReposicionGastosModal" id="codReposicionGastosModal" value="{{$reposicion->codReposicionGastos}}">
                            
                            <div class="row">
                                <div class="col-5">
                                    
                                    <label>Observacion <b id="contador2" style="color: rgba(0, 0, 0, 0.548)"></b></label>
                                </div>
                                <div class="w-100"></div> {{-- SALTO LINEA --}}
                                <div class="col">
                                    <textarea class="form-control" name="observacion" id="observacion" cols="30" rows="4"  placeholder='Ingrese observación aquí...'></textarea> 
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Salir
                        </button>

                        <button type="button" onclick="clickObservar()" class="btn btn-primary">
                        Guardar <i class="fas fa-save"></i>
                        </button>
                    </div>
            </div>
        </div>
    </div>

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


    </style>

@section('script')



       {{-- PARA EL FILE  --}}
<script type="application/javascript">
    //se ejecuta cada vez que escogewmos un file
    $(document).ready(function(){
        contadorCaracteres('observacion','contador2','{{App\Configuracion::tamañoMaximoObservacion}}');
    });
    
    function clickRechazar() {
        confirmarConMensaje('¿Esta seguro de rechazar la reposición?','','warning',ejecutarRechazar);
    }
    function ejecutarRechazar() {
        window.location.href="{{route('ReposicionGastos.rechazar',$reposicion->codReposicionGastos)}}"; 
    }

    function clickAbonar() {
        confirmarConMensaje('¿Esta seguro de abonar la reposicion?','','warning',ejecutarAbonar);
    }
    function ejecutarAbonar() {
        window.location.href="{{route('ReposicionGastos.abonar',$reposicion->codReposicionGastos)}}";
    }
    
    function clickObservar() {
        texto=$('#observacion').val();
        if(texto==''){
            alerta('Ingrese observacion');
            return false;
        }
        tamañoActualObs = texto.length;
        tamañoMaximoObservacion =  {{App\Configuracion::tamañoMaximoObservacion}};
        if( tamañoActualObs  > tamañoMaximoObservacion){
            alerta('La observación puede tener máximo hasta ' +    tamañoMaximoObservacion + 
                " caracteres. (El tamaño actual es " + tamañoActualObs + ")");
            return false;
        }

        confirmarConMensaje('¿Esta seguro de observar la reposicion?','','warning',ejecutarObservar);
    }
    function ejecutarObservar() {
        document.formObservar.submit();
    }


    /*
    function observar(){
        texto=$('#observacion').val();
        if(texto!=''){
            reposicion=$('#codReposicionGastos').val();
            //window.location.href='/Reposicion/'+reposicion+'*'+texto+'/observar';
            window.location.href='/ReposicionGastos/'+reposicion+'*'+texto+'/Observar'; 
        }
        else{ 
            alerta('Ingrese observacion');
        }
    }*/
    function cambio(index){

        if(index=='imagenEnvio'){//si es pal comprobante de envio
            
            //DEPRECADO PORQUE AHORA EL ARCHIVO DE CBTE DE DEVOLUCION DE FONDOS SE ADJUNTA COMO UN CBTE MÁS
            /* var idname= 'imagenEnvio'; 
            var filename = $('#imagenEnvio').val().split('\\').pop();
            console.log('filename= '+filename+'    el id es='+idname+'  el index es '+index)
            jQuery('span.'+idname).next().find('span').html(filename);
            document.getElementById("divFileImagenEnvio").innerHTML= filename;
            $('#nombreImgImagenEnvio').val(filename);
             */
        }
        else{ //para los CDP de la tabla
            var idname= 'imagen'+index; 
            var filename = $('#imagen'+index).val().split('\\').pop();
            console.log('filename= '+filename+'    el id es='+idname+'  el index es '+index)
            //jQuery('span.'+idname).next().find('span').html(filename);
            document.getElementById("divFile"+index).innerHTML= filename;
            $('#nombreImg'+index).val(filename);
            
        
        }
    
    }


</script>

     <script>
        var cont=0;
        
        //var IGV=0;
        var total=0;
        var detalleRend=[];
        //var importes=[];
        //var controlproducto=[];
        //var totalSinIGV=0;
        //var saldoFavEmpl=0;

                //GENERACION DE codigoCedepas
                var d = new Date();
                codEmp = $('#codigoCedepasEmpleado').val();
                mes = (d.getMonth()+1.0).toString();
                if(mes.length > 0) mes = '0' + mes;

                year =  d.getFullYear().toString().substr(2,2)  ;
                $('#codigoCedepas').val( codEmp +'-'+ d.getDate() +mes + year + cadAleatoria(2));
                //alerta($('#codigoCedepas').val());
    
        

        function alertaArchivo(){
            alerta('Asegúrese de haber añadido todos los ítems antes de subir los archivos.');

        }

        function cadAleatoria(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var abecedario = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            var charactersLength = characters.length;
            var abecedarioLength = abecedario.length;
            for ( var i = 0; i < length; i++ ) {
                if(i==0)//primer caracter fijo letra
                    result += abecedario.charAt(Math.floor(Math.random() * abecedarioLength));
                else//los demas da igual que sean numeros
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));

            }
            return result;
        }
    
        function number_format(amount, decimals) {
            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto
            decimals = decimals || 0; // por si la variable no fue fue pasada
            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0) 
                return parseFloat(0).toFixed(decimals);
            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);
            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;
            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');
            return amount_parts.join('.');
        }
    
    
    </script>
     










@endsection
