var datatable = function (){

    return {

        init: function(){
            $('#general-table').dataTable(
                {
                    language: {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ningún dato disponible en esta tabla",
                        "sInfo":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        "sInfoEmpty":      "Mostrando 0 a 0  de 0 registros",
                        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix":    "",
                        "sSearch":         "Buscar:",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "Último",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        },
                    },
                    "paging": true,
                    // "lengthChange": false,
                    // "searching": false,
                    "ordering": false,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                }
            );
        }

    }

}();

var validacion = function (){

    return {
        init: function(){
            $(document).on("keypress","input , textarea",function(){
                $(this).removeClass('is-invalid')
                $(this).parent().find('.help-block').addClass('d-none')
                $(this).parent().find('.control-label').removeClass('has-error')
            })
            $(document).on("change","select",function(){
                $(this).removeClass('is-invalid')
                $(this).parent().find('.help-block').addClass('d-none')
                $(this).parent().find('.control-label').removeClass('has-error')
            })
        }
    }

}();

var app = function () {
    return {
        init: function () {
            var $delete = $('.btn-delete');
            $delete.click(function (e) {
                var $data = $(this).data();
                e.preventDefault();
                swal({
                    title: "¿Esta seguro de Eliminar?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#f4488c",
                    confirmButtonText: "Si, eliminar",
                    cancelButtonText: 'Cancelar',
                    closeOnConfirm: false,
                    allowOutsideClick: false
                }, function () {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: $data.url,
                        async: false,
                        data: {id: $data.id},
                        success: function (data) {
                            if (data.status == 200) {
                                swal("¡Eliminado!", "Fue eliminado con éxito", "success");
                                 setTimeout(function () {
                                     window.location.reload();
                                 }, 2000);
                            }else if(data.status==500){
                                swal("Error!", data.message, "error");
                            }
                        }
                    });
                });
            });
        
        }
    }
}();

var productos = [];

var movimiento = function (){

    return {
        init : function(){

            var $btnAgregar = $("#btnAgregarMov");
            

            $btnAgregar.click(function(e){

                var codigo = $("#codExistencia").val();
                var cantidad = $("#step").val();
                var tipo = $("#tipoMov").val();

                if(codigo != "" && cantidad > 0){
                    var $data = $(this).data();
                    e.preventDefault();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: $data.url,
                        async: false,
                        data: {codigo: codigo},
                        success: function (data) {
                            if (data.existe == 1) {

                                cantidad = parseInt(cantidad);
                                var objExitente = buscarProducto(codigo);

                                if(tipo == 2 && cantidad > data.stock){
                                    swal("¡Error!", "El producto no tiene stock suficiente", "error");
                                    return;
                                }

                                if(objExitente == "undefined" || objExitente == null){
                                    var objeto = Object();
                                    objeto.codigo = codigo;
                                    objeto.cantidad = cantidad;
                                    objeto.nombre = data.producto;
                                    productos.push(objeto);
                                }else{
                                    cantidad += objExitente.cantidad;

                                    if(tipo == 2 && cantidad > data.stock){
                                        swal("¡Error!", "El producto no tiene stock suficiente", "error");
                                        return;
                                    }

                                    objExitente.cantidad = cantidad;
                                }

                                mostrarDatos();
                                
                                //console.log(productos)
                            }else{
                                swal("¡Error!", "El codigo no es el correcto", "error");
                            }
                        }
                    });
                }else{
                    swal("¡Error!", "El codigo debe ser ingresado y la cantidad debe ser mayor a cero", "error");
                }

                
                
            });

            if($("#codArray").val() != ""){
                var arreglo_existete= $("#codArray").val().split(",");
                
                arreglo_existete.forEach(element => {
                    var arreglo_objet = element.split('__');

                    var objeto = Object();
                    objeto.codigo = arreglo_objet[0];
                    objeto.cantidad = parseInt(arreglo_objet[2]);
                    objeto.nombre = arreglo_objet[1];
                    productos.push(objeto);

                });

                mostrarDatos();
            }

        },

        modal: function(){

            var $btnBuscar = $("#btnSearchModal");

            $btnBuscar.click(function(e){

                var texto = $("#txtBuscar").val();

                if( texto != ""){

                    var $data = $(this).data();
                    e.preventDefault();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: $data.url,
                        async: false,
                        data: {texto: texto, tipo: $("input[name='rbGrupo']:checked").val()},
                        success: function (data) {
                            if(data.status == 200){

                                var tbody = "";

                                if(data.data.length > 0){

                                    var arreglo = data.data;

                                    arreglo.forEach(element => {

                                        btn = "";

                                        if($("#tipoMov").val() == 1 || $("#tipoMov").val() == 3 || ($("#tipoMov").val() == 2 && element.stock > 0)){
                                            btn = `<a href="javascript:void(0)" data-id="${element.codigoInterno}" data-stock="${element.stock}" class="btn btn-primary" onclick="seleccionarData('${element.codigoInterno}',${element.stock})">
                                                        <i class="fa fa-check"></i>
                                                    </a>`;
                                        }

                                        tbody += `<tr>
                                                    <td>${element.codigoInterno}</td>
                                                    <td>${element.nombre}</td>
                                                    <td>${element.stock}</td>
                                                    <td>
                                                        ${btn}
                                                    </td>
                                                </tr>`;

                                    });

                                }

                                $("#tbody_codExistenteSearch").html(tbody);

                            }
                        }
                    });


                }else{
                    alert("Debe ingresar la descripcion a buscar")
                }

            });

        }

    }

}();

var reporte = function(){

    return {
        init : function(){
            var $btnExportar = $("#btnExportar"); 

            $btnExportar.click(function(e){

                var codigo = $("#codExistencia").val();

                if(codigo != ""){
                    var $data = $(this).data();
                    e.preventDefault();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: $data.url,
                        async: false,
                        method : 'get',
                        data: {codigo: codigo},
                        success: function (response) {
                            console.log(response)
                            if (response.status == 200) {                                
                                
                                window.open(response.data, '_blank');
                                
                            }else{
                                swal("¡Error!", response.message, "error");
                           }
                                        //location.href = response.data;
                                        
                            /*var blob = new Blob([response]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "Sample.pdf";
                            link.click();*/

                        },
                        error: function(e){
                            console.log(e)
                            /*var blob = new Blob([e]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "Sample.pdf";
                            link.click();*/
                        }
                    })
                }else{
                    swal("¡Error!", "El codigo debe ser ingresado", "error");
                }

                
                
            });
        },
        load: function(){
            var $codCategoria = $("#codCategoria");
            var $codExistencia = $("#codExistencia");
            var $btn_buscar = $("#btn_buscar");
            
            $codCategoria.change(function(e){
                $codExistencia.html('<option value="">Seleccione Existente</option>');
                if($codExistencia != ""){
                    var $data = $codExistencia.data();
                    e.preventDefault();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: $data.url,
                        async: false,
                        data: {codigo: $codCategoria.val()},
                        success: function (response) {
                            console.log(response)
                            if (response.status == 200) {                                
                                
                                var option = '<option value="">Seleccione Existente</option>';
    
                                if(response.data.length > 0){
                                    response.data.forEach(element => {
                                        option += `<option value='${element.codExistencia}'>${element.nombre}</option>`;
                                    });
                                }
                                $codExistencia.html(option);
                            }
    
                        },
                        error: function(e){
                            console.log(e)
                        }
                    })
                }
            });

            $btn_buscar.click(function(e){

                var codCategoria = $("#codCategoria").val();
                var codExistencia = $("#codExistencia").val();
                var fecha_inicio = $("#fecha_inicio").val();
                var fecha_fin = $("#fecha_fin").val();

                var mensaje = "";

                if(codCategoria == ""){
                    mensaje +="Debe seleccionar categoria\n";
                }
                if(codExistencia == ""){
                    mensaje +="Debe seleccionar existente\n";
                }
                if(fecha_inicio == ""){
                    mensaje +="Debe seleccionar fecha de inicio\n";
                }
                if(fecha_fin == ""){
                    mensaje +="Debe seleccionar fecha de fin\n";
                }
                if(fecha_inicio > fecha_fin){
                    mensaje +="La fecha de inicio debe ser menor a la fecha de fin";
                }

                if(mensaje != ""){
                    swal("¡Error!", mensaje, "error");
                    return;
                }
                var $data = $(this).data();
                e.preventDefault();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: $data.url,
                        async: false,
                        data: {codigo: codExistencia},
                        success: function (response) {
                            console.log(response)
                            $("#tbody_movimiento").html("");
                            if (response.status == 200) {                                
                                
                                $("#tbody_movimiento").html(response.html);
                            }
    
                        },
                        error: function(e){
                            console.log(e)
                        }
                    })

            });
        }

    }


}();

//funciones

function downloadFile(response) {
    var blob = new Blob([response], {type: 'application/pdf'})
    var url = URL.createObjectURL(blob);
    location.assign(url);
  } 

function eliminarData(codigo){
    if(confirm("¿Esta seguro de eliminar el registro?"))
    {
        //productos = productos.find(element => element.codigo != codigo);
        //var x = productos.filter(function(i) { return i.codigo !== codigo });
        var x = productos.filter(function(i) { return i.codigo != codigo });
        productos = x;
        if(productos == "undefined" || productos == null) productos = [];
        mostrarDatos();
    }
    
}

function seleccionarData(codigo, stock){

    console.log(codigo)

    $("#codExistencia").val(codigo);
    $("#modal-existencia").modal('hide')

}

function mostrarDatos(){

    var tbody = "";
    var $tabla = $("#tbody_codExistente");
    var data = "";
    if (productos.length > 0){
        productos.forEach(element => {
            tbody += `<tr>
                        <td>${element.codigo}</td>
                        <td>${element.nombre}</td>
                        <td>${element.cantidad}</td>
                        <td>
                            <a href="javascript:void(0)" data-id="${element.codigo}" class="btn btn-danger btn-delete-codigo" onclick="eliminarData('${element.codigo}')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>`;

            data += element.codigo+"__"+element.nombre+"__"+element.cantidad+",";
        });

        data = data.substr(0,data.length-1);
        $("#codArray").val(data);

    }

    

    $tabla.html(tbody);

}

function buscarProducto(codigo){

    return productos.find(element => element.codigo == codigo);

}

function filter(__val__){
    var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
    return preg.test(__val__);
    
}

function validateDecimal(evt,input){
    // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        return filter(tempValue);
    }else{
          if(key == 8 || key == 13 || key == 0) {     
              return true;              
          }else if(key == 46){
            return filter(tempValue);
          }else{
              return false;
          }
    }
}

function validateNumberNatural(e) {
    var key = window.event ? e.which : e.keyCode;
    if (key < 48 || key > 57) {
        e.preventDefault();
    }
}

function validateNumberLetter(e) {
    var key = window.event ? e.which : e.keyCode;
    console.log(key)
    if ((key < 48 || key > 57)&&(key<65 || key>90)&&(key<97 || key>122)) {
        e.preventDefault();
    }
}
