@extends('Layout.Plantilla')
@section('titulo')
    Crear Puesto
@endsection
@section('contenido')


<script type="text/javascript"> 
          
    function validarregistro() 
    {
        var expreg = new RegExp("^(?=^.{3,300}$)^[A-ZÁÉÍÓÚñáéíóúÑ][a-zA-ZÁÉÍÓÚñáéíóúÑ0-9 \\%\\,\\(\\)]*[_]?[a-zA-ZÁÉÍÓÚñáéíóúÑ0-9 ]+[.\\ ]?$");
                if (document.getElementById("descripcion").value == ""){
                    alerta("Ingrese descripción del puesto");
                    $("#descripcion").focus();
                }
                else if(!expreg.test(document.getElementById("descripcion").value)){
                    alerta("Ingrese una correcta descripción (entre 3-300 caracteres)(inicio con mayúscula)");
                    $("#descripcion").focus();
                }
                else{
                    document.frmPuesto.submit(); // enviamos el formulario	
                }
                    
        //document.frmPuesto.submit(); // enviamos el formulario	
    }
        
</script>
    <div class="well"><H3 style="text-align: center;">CREAR PUESTO

    </H3></div>
    <br>
    <form id="frmPuesto" name="frmPuesto" role="form" action="{{route('GestiónPuestos.store')}}" class="form-horizontal form-groups-bordered" method="post" enctype="multipart/form-data">
        @csrf 

            <div class="form-group row" style="margin-left:250px;">
                <label class="col-sm-1 col-form-label">Nombre:</label>
                <div class="col-sm-7">
                    <textarea class="form-control" rows="3" name="descripcion" id="descripcion" placeholder="Nombre ..." style="margin-top: 0px; margin-bottom: 0px; height: 61px;"></textarea>
                </div>
            </div>

            <br />
            <!--
             <input type="button" class="btn btn-primary"  value="Guardar" onclick="validarregistro()" /> -->
            <button type="button" class="btn btn-primary float-right" id="btnRegistrar" data-loading-text="<i class='fa a-spinner fa-spin'></i> Registrando" onclick="swal({//sweetalert
                title:'¿Seguro de crear el puesto?',
                text: '',     //mas texto
                type: 'info',//e=[success,error,warning,info]
                showCancelButton: true,//para que se muestre el boton de cancelar
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText:  'SÍ',
                cancelButtonText:  'NO',
                closeOnConfirm:     true,//para mostrar el boton de confirmar
                html : true
            },
            function(){//se ejecuta cuando damos a aceptar
                validarregistro();
            });"><i class='fas fa-save'></i> Registrar</button> 
            <a href="{{route('GestiónPuestos.Listar')}}" class='btn btn-info float-left'><i class="fas fa-arrow-left"></i> Regresar al Menu</a>
    </form>
@endsection
