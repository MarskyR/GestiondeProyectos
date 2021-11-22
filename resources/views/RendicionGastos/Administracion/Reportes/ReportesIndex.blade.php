@extends ('Layout.Plantilla')

@section('contenido')
<div>
  <h3> REPORTES </h3>

{{-- 
- Reporte de gastos por empleados con %.
-	Reporte de gastos por sedes con %.
- Reporte de 
-	Reporte de las solicitudes.
-	Reporte de las rendiciones
 --}}
<style>
.col{
  margin-top: 10px;


}

</style>
   
        <form method="POST" action="{{route('rendicionGastos.reportes')}}" onsubmit="return validarTextos()">
          @csrf
            <div class="container">
              <div class="row" >
                <div class="col"></div>
                <div class="col"></div>
                
                
                <div class="col">
                  
                    <label for="">Tipo de Reporte</label>
                </div>

                <div class="col">
                  
               
                    <select class="custom-select" id="tipoInforme" name="tipoInforme" onchange="cambioSelect()">
                      <option value="0">-- Seleccionar -- </option>
                      <option value="1">Por Sedes </option>
                      <option value="2">Por Empleados </option>
                      <option value="3">Por Proyectos</option>
                      <option value="4">Por Empleados de una Sede</option>
                      
                    </select>
                


                </div>
                
                <div class="col"></div>
                <div class="col"></div>
                <div class="w-100"></div>
                <div class="col"></div>
                <div class="col"></div>
         
                



                <div class="col">
                  <label for="fechaComprobante">Fecha Inicio</label>
                </div>
                <div class="col">
                 
                  <div class="input-group date form_date " data-date-format="yyyy-mm-dd" data-provide="datepicker">
                    <input type="text"  class="form-control" name="fechaI" id="fechaI"
                          value="{{ Carbon\Carbon::now()->subDay(10)->format('Y-m-d') }}"> 
                    <div class="input-group-btn">                                        
                        <button class="btn btn-primary date-set" type="button">
                            <i class="fas fa-calendar"></i>
                        </button>
                    </div>
                  </div>

                </div>
                
                
                <div class="col"></div>
                <div class="col"></div>
                <div class="w-100"></div>
                <div class="col"></div>
                <div class="col"></div>
               
                
                <div class="col">
                  <label for="fechaComprobante">Fecha Fin</label>
                </div>
                

                <div class="col">
                  
                  <div class="input-group date form_date " data-date-format="yyyy-mm-dd" data-provide="datepicker">
                    <input type="text"  class="form-control" name="fechaF" id="fechaF"
                          value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" > 
                    <div class="input-group-btn">                                        
                        <button class="btn btn-primary date-set" type="button">
                            <i class="fas fa-calendar"></i>
                        </button>
                    </div>
                  </div>
                </div>
            
               

               

                <div class="col"></div>
                <div class="col"></div>
                

                <div class="w-100"></div>
                <div class="col"></div>
                <div class="col"></div>

                <div class="col" id="labelSede" name="labelSede"  style="display: none">
                  <label for=""> Sede </label>
                </div>
                

                <div class="col" id="selectSede" name="selectSede"  style="display: none">
                  <select class="form-control"  id="ComboBoxSede" name="ComboBoxSede" >
                    <option value="0">-- Seleccionar -- </option>
                    @foreach($listaSedes as $itemSede)
                        <option value="{{$itemSede['codSede']}}" >
                            {{$itemSede->nombre}}
                        </option>                                 
                    @endforeach 
                  </select> 
                </div>

                <div class="col"></div>
                <div class="col"></div>

                <div class="w-100"></div>

                <div class="col"></div>
                <div class="col"></div>

                <div class="col">
                  <button type="submit" class="btn btn-primary">Ver Informe</button>
                  
                </div>
                <div class="col"></div>
                <div class="col"></div>

              </div>

              <div class="row"> 
                
                


                <div class="col">
                </div>
                
                <div class="col">
                </div>
                
                <div class="col">
                </div>
                
                <div class="col">
                </div>
                
                <div class="col">
                </div>
                
              </div>


                          

                
              
            </div>
          
          




        </form>
   

{{-- AQUI FALTA EL CODIGO SESSION DATOS ENDIF xdd --}}
      @if (session('datos'))
        <div class ="alert alert-warning alert-dismissible fade show mt-3" role ="alert">
            {{session('datos')}}
          <button type = "button" class ="close" data-dismiss="alert" aria-label="close">
              <span aria-hidden="true"> &times;</span>
          </button>
          
        </div>
      @ENDIF

 

</div>
@endsection

@section('script')

<script>

    function validarTextos(){
        msj = '';
        if(  $('#tipoInforme').val() == 0  )
            msj = 'Seleccione un tipo de informe';
        if( $('#tipoInforme').val() == 4 && $('#ComboBoxSede').val() == 0)
          msj = 'Seleccione una sede para reportar';
        
        if( msj!='')
        {  
            alerta(msj); 
            return false;
        }

      return true;
    }

    function cambioSelect(){
      valor = $('#tipoInforme').val()
      if(valor==4)//empleados de una sede
      {
        document.getElementById('labelSede').style.display='';
        document.getElementById('selectSede').style.display='';
      }else{
        document.getElementById('labelSede').style.display='none';
        document.getElementById('selectSede').style.display='none';
      }


    }


</script>

@endsection