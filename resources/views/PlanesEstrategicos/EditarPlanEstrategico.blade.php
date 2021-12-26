@extends('Layout.Plantilla')

@section('titulo')
    Editar Plan Estratégico
@endsection
@section('estilos')
<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">
@endsection

@section('contenido')

@include('Layout.MensajeEmergenteDatos')


<form id="frmPEI" name="frmPEI" action="{{route('PlanEstrategico.actualizar')}}" method="post" >
    @csrf 


    <div class="well">
        <H3 style="text-align: center;">
            Actualizar Plan Estratégico
        </H3>
    </div>
    <br>

    <div class="row">
        

        <div class="col" style="">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label class="" style="">Código:</label>
                        <div class="">
                            <input type="text" class="form-control" id="codPEI" name="codPEI"
                            value="{{$PEI->codPEI}}"  placeholder="..." readonly>
                        </div>
                    </div>
                    <div class="col">

                        
                        <label class="" style="">Año Inicio:</label>
                        
                        
                        <div class="">
                            <input type="number" class="form-control" id="añoInicio" name="añoInicio" 
                                value="{{$PEI->añoInicio}}" placeholder="Ingrese año..." >
                        </div>

                        
                        

                    </div>
                    <div class="col">
                        <label class="" style="">Año Final:</label>
                        
                        <input type="number" class="form-control" id="añoFin" name="añoFin" 
                            value="{{$PEI->añoFin}}" placeholder="Ingrese año..." >
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                    
                        <button type="button" class="btn btn-primary m-2">
                            <i class="fas fa-save"></i>
                            Guardar
                        </button>
                         
                    
                    
                    </div>
                    
                </div>
                
 
                <br>
                <div class="row">
                    <div class="col">
                        <button type="button" id="" class="btn btn-primary m-2"  onclick="clickAgregarObjEstrategico()"
                            data-toggle="modal" data-target="#ModalObjetivoEstrategico" >
                            Agregar Objetivo Estratégico
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                  
               
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table table-bordered table-hover datatable table-sm fontSize10">
                            <thead>                  
                                <tr>
                                    <th>idBD</th>
                                    <th width="8%" >Item</th>
                                    <th width="15%">Nombre</th>
                                    <th>Objetivo Estratégico</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($listaObjetivos as $objEst )
                                    <tr>
                                        <td>
                                            {{$objEst->codObjetivoEstrategico}}
                                        </td>
                                        <td>    
                                            {{$objEst->item}}
                                        </td>
                                        <td>
                                            {{$objEst->nombre}}
                                        </td>
                                        <td>
                                            {{$objEst->descripcion}}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" 
                                                data-target="#ModalObjetivoEstrategico" onclick="clickEditarObjEst({{$objEst->codObjetivoEstrategico}})"> 
                                                <i class="fas fa-pen"></i>
                                                
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="clickEliminarObjEstrategico({{$objEst->codObjetivoEstrategico}})">
                                                <i class="fas fa-trash"></i>

                                            </button>
                                            
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        
                        </table>
                    </div>

                </div>
                <div class="row">
                    <div class="col" style=" text-align:center">
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="cantElementos" id="cantElementos" value="0">
                    
                        
                        
                        <a href="{{route('PlanEstrategico.listar')}}" class='btn btn-info float-left'>
                            <i class="fas fa-arrow-left"></i> 
                            Regresar al Menu
                        </a>
    
                    </div>
                </div>
            
            </div>
            
        </div>
        


    </div>



</form>


{{-- Este modal sirve tanto para agregar como para editar --}}
<div class="modal fade" id="ModalObjetivoEstrategico" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalObjetivoEstrategico"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('ObjetivoEstrategico.agregarEditarObjetivoEstrategico')}}" method="POST" id="frmObjetivoEstrategico" name="frmObjetivoEstrategico">
                        
                        @csrf
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codPEI" id="codPEI" value="{{$PEI->codPEI}}">
                        
                        
                        {{-- Si se creará uno nuevo, está en 0, si se va a editar tiene el codigo del obj a editar --}}
                        <input type="{{App\Configuracion::getInputTextOHidden()}}" name="codObjetivoEstrategico" id="codObjetivoEstrategico" value="0">


                        <div class="row">
                            <div class="col">
                                <label for="">Nombre</label>
                                <input type="text" class="form-control" name="nombreObjetivoEstrategico" id="nombreObjetivoEstrategico">
        
                            </div>
                            <div class="col">
                                <label for="">Item</label>
                                <input type="number" step="1" min="1" class="form-control" name="item" id="item">
                            </div>

                            <div class="w-100"></div>
                            <div class="col">
                                <label for="">Descripción del objetivo:</label>
                                <textarea class="form-control" name="descripcionObjetivoEstrategico" id="descripcionObjetivoEstrategico" cols="15" rows="6"
                                ></textarea>
                            </div>

                        </div>

                        
                        

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Salir
                    </button>

                    <button type="button" class="btn btn-primary"   onclick="clickGuardarObjetivoEstrategico()">
                        Guardar <i class="fas fa-save"></i>
                    </button>
                </div>
            
        </div>
    </div>
</div>
                

@endsection

@section('script')
@include('Layout.ValidatorJS')
<script type="text/javascript"> 
    


    var listaObjetivos= @php echo json_encode($listaObjetivos); @endphp;
    
    $(window).load(function(){

        //cuando apenas carga la pagina, se debe copiar el contenido de la tabla a detalleSol
        
        $(".loader").fadeOut("slow");
    });


    /* DATOS DEL PEI */

    function clickActualizar(){
        msjError = validarFrmPEI();;
        if(msjError!=""){
            alerta(msjError);
            return;
        }
        
    }

    function submitear(){
        document.frmPEI.submit(); // enviamos el formulario	

    }

    function validarFrmPEI() 
    {

        msj="";
        if (document.getElementById("añoInicio").value == ""){
            msj = ("Ingrese el año inicial del PEI");
            
        }
        if (document.getElementById("añoFin").value == ""){
            msj = ("Ingrese el año Final del PEI");
            
        }

        return msj;
    }

    /* OBJ ESTRATEGICOS DEL PEI */


    function clickGuardarObjetivoEstrategico(){
        msjError = validarfrmObjetivoEstrategico();;
        if(msjError!=""){
            alerta(msjError);
            return;
        }

        document.frmObjetivoEstrategico.submit();

    }

    function validarfrmObjetivoEstrategico(){
        msj="";
        msj = validarTamañoMaximoYNulidad(msj,'nombreObjetivoEstrategico',200,'Nombre del Objetivo Estratégico');
        msj = validarTamañoMaximoYNulidad(msj,'descripcionObjetivoEstrategico',1000,'Nombre del Objetivo Estratégico');
        msj = validarPositividadYNulidad(msj,'item','Número de item');

        return msj;

    }
 
    
    
    function clickEditarObjEst(codObjetivoEstrategico){
        obj = listaObjetivos.find(element => element.codObjetivoEstrategico == codObjetivoEstrategico);

        document.getElementById('TituloModalObjetivoEstrategico').innerHTML = "Editar Objetivo Estratégico";
        
        document.getElementById('codObjetivoEstrategico').value = obj.codObjetivoEstrategico;
        document.getElementById('nombreObjetivoEstrategico').value = obj.nombre;
        document.getElementById('item').value = obj.item;
        
        document.getElementById('descripcionObjetivoEstrategico').value = obj.descripcion;

    }

    function clickAgregarObjEstrategico(){
        document.getElementById('TituloModalObjetivoEstrategico').innerHTML = "Agregar Objetivo Estratégico";
        
        document.getElementById('codObjetivoEstrategico').value = 0;
        document.getElementById('nombreObjetivoEstrategico').value = "";
        document.getElementById('descripcionObjetivoEstrategico').value ="";


    }

    codObjetivoEstrategicoAEliminar = 0;
    function clickEliminarObjEstrategico(codObjetivoEstrategico){
        obj = listaObjetivos.find(element => element.codObjetivoEstrategico == codObjetivoEstrategico);
        codObjetivoEstrategicoAEliminar = codObjetivoEstrategico;
        confirmarConMensaje("Confirmación",'¿Desea eliminar el objetivo estratégico "'+obj.nombre+'" ?',"warning",ejecutarEliminacionObjEstrategico);
    }

    function ejecutarEliminacionObjEstrategico(){
        location.href = "/ObjetivoEstrategico/eliminar/" + codObjetivoEstrategicoAEliminar;

    }


</script>
 

@endsection
