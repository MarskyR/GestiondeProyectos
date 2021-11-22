@extends('Layout.Plantilla')

@section('titulo')
Ver Solicitud
@endsection

{{-- ESTA VISTA LA USA EL EMPLEADO, PARA VER UNA SOLICITUD DE FONDOS --}}

@section('contenido')
<div >
    <p class="h1" style="text-align: center">Ver Solicitud de Fondos</p>


</div>

<form method = "POST" action = ""  >
        
    {{-- CODIGO DEL EMPLEADO --}}
    <input type="hidden" name="codigoCedepas" id="codigoCedepas" value="{{ $empleadoLogeado->codigoCedepas }}">

    @csrf
            @include('SolicitudFondos.Plantillas.VerSOF')
                
            <div class="row" id="divTotal" name="divTotal">                       
                <div class="col-md-8">
                </div>   
                <div class="col-md-2">                        
                    <label for="">Total : </label>    
                </div>   
                <div class="col-md-2">
                    {{-- HIDDEN PARA GUARDAR LA CANT DE ELEMENTOS DE LA TABLA --}}
                    <input type="hidden" name="cantElementos" id="cantElementos">                              
                    <input type="text" class="form-control text-right" name="total" id="total" value="{{number_format($solicitud->totalSolicitado,2)}}" readonly>                              
                </div>   

                <div class="w-100"></div>

                <div class="col-md-8">
                </div>   
                <div class="col-md-2">
                    
                    @if($solicitud->getNombreEstado()=='Rechazada')
                    <label for="">Razón de rechazo:</label>
                    <textarea name="" id="" readonly cols="30" rows="2" class="form-control">{{$solicitud->razonRechazo}}</textarea>
                    @endif

                </div>
                
            </div>
                    

      
        
        <div class="col-md-12 text-center">  
            <div id="guardar">
                <div class="form-group">
                    
                    
                    <div class="">
                        <div class="row">
                            <div class="col">
                                @include('SolicitudFondos.Plantillas.DesplegableDescargarArchivosSoli')
                                <a href="{{route('SolicitudFondos.Empleado.Listar')}}" class='btn btn-info float-left'>
                                    <i class="fas fa-arrow-left"></i>
                                    Regresar al Menu
                                </a>
                            </div>
                            <div class="col"></div>
                            <div class="col"></div>
                            <div class="col"></div>
                            
                            
                        </div>
                    </div>
                   
                        
                          
                    
                               
                    
                    
                
                </div>    
            </div>
        </div>
    </div>

</form>
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


<style>
    
    
    </style>


@section('script')
     
@endsection
