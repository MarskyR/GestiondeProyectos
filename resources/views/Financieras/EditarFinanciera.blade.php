@extends('Layout.Plantilla')

@section('titulo')
    Editar Entidad Financiera
@endsection
@section('estilos')
<link rel="stylesheet" href="/calendario/css/bootstrap-datepicker.standalone.css">
<link rel="stylesheet" href="/select2/bootstrap-select.min.css">
@endsection

@section('contenido')


<form id="frmEntidadFinanciera" name="frmEntidadFinanciera" role="form" action="{{route('EntidadFinanciera.actualizar')}}" 
class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">

@csrf 


<div class="well"><H3 style="text-align: center;">Actualizar Nombre de Entidad</H3></div>
<br>
<div class="container">
    <div class="row">
        <div class="col-2" style="">
            
        
        </div>
        

        <div class="col" style="">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label class="" style="">Código:</label>
                        <div class="">
                            <input type="text" class="form-control" id="codEntidadFinanciera" name="codEntidadFinanciera"
                            value="{{$entidadFinanciera->codEntidadFinanciera}}"  placeholder="..." readonly>
                        </div>
                    </div>

                    <div class="w-100"></div>

                    <div class="col">

                        
                        <label class="" style="">Nombre:</label>
                        
                        
                        <div class="">
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                value="{{$entidadFinanciera->nombre}}" placeholder="Nombre..." >
                        </div>
                    </div>
                    
                    
                
                    <div class="w-100"></div>
                    <br>
                    <div class="col" style=" text-align:center">
                 
                       
                        <button type="button" class="btn btn-primary float-right" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" 
                            onclick="registrar()">
                            <i class='fas fa-save'></i> 
                            Registrar
                        </button> 
                        
                        <a href="{{route('EntidadFinanciera.listar')}}" class='btn btn-info float-left'>
                            <i class="fas fa-arrow-left"></i> 
                            Regresar al Menu
                        </a>
    
                    </div>

                </div>

            </div>
               
        </div>
        <div class="col-2" >
         
        
        </div>


    </div>


</div>

</form>
@endsection
@include('Layout.ValidatorJS')
@section('script')
<script type="text/javascript"> 
    function registrar(){
        msje = validarregistro();
        if(msje!="")
            {
                alerta(msje);
                return false;
            }
        
        confirmar('¿Seguro de registrar la Entidad Financiera?','info','frmEntidadFinanciera');
        
    }
                
    function validarregistro(){ //Retorna TRUE si es que todo esta OK y se puede hacer el submit
        msj='';
        
        limpiarEstilos(['nombre']);
        msj = validarTamañoMaximoYNulidad(msj,'nombre',{{App\Configuracion::tamañoMaximoNombreEF}},'Nombre');

        return msj;
    }
</script>
@endsection
