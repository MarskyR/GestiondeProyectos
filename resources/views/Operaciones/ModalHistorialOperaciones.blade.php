
<style>
    #tablaHistorial td,th{
        text-align: center
    }
</style>

{{-- MODAL PARA VER HISTORIAL --}}
<div class="modal fade" id="ModalHistorial" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Historial del documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="">
                
                <table id="tablaHistorial" class="table table-striped table-bordered table-condensed table-hover">

                    <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
                        <th  >Item</th>                                        
                        <th >Fecha hora</th>                                 
                        <th >Nombre</th>
                        <th>Rol</th>
                        <th >Acción</th>
                    </thead>

                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($listaOperaciones as $operacion)
                            <tr>
                                <td>
                                    {{$i}}               
                                </td>
                                <td>
                                    {{$operacion->getFechaHora()}}               
                                </td>
                                <td>
                                    {{$operacion->getEmpleado()->getNombreCompleto()}}               
                                </td>
                                <td>
                                    {{$operacion->getPuesto()->nombre}}
                                </td>
                                <td>
                                    {{$operacion->getTipoOperacion()->nombre}}       
                                    @if($operacion->getTipoOperacion()->nombre == "Observar")
                                        <button type="button" class="btn btn-primary btn-xs" onclick="alertaMensaje('Observación',`{{$operacion->descripcionObservacion}}`,'info')">
                                            Razón observación
                                            <i class="fas fa-eye">

                                            </i>
                                        </button>
                                        
                                    @endif        
                                </td>
                            </tr>
                        @php
                            $i++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>