@extends('Layout.Plantilla') 

@section('titulo')
  Seguimiento de las Metas
@endsection

@section('contenido')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>

<link rel="stylesheet" href="/libs/morris.css">
<script src="/libs/morris.min.js" charset="utf-8"></script>

  <h1>
    Seguimiento de las Metas
  </h1>

  @include('Layout.MensajeEmergenteDatos')

  <div class="card">
    <div class="card-header" style=" ">
      <div class="row">
        <div class="col">
          
          <h3>Proyecto:</h3>
          {{$indicador->getProyecto()->nombre}}
        </div>
        <div class="col">
          <h3>Res. Esperado: </h3>
          
          {{$indicador->getResultadoEsperado()->descripcion}}
        </div>
        <div class="col">
          <h3>Actividad: </h3>
          
          {{$indicador->getActividad()->descripcion}}
        </div>
        <div class="col">
          <h3>Indicador:</h3>
           
          {{ "[".$indicador->meta."] ".$indicador->unidadMedida}}
        </div>
      </div>
    </div>
  </div>

@include('Layout.MensajeEmergenteDatos')
  <div class="card">
    <div class="card-header" style=" ">
      <h3 class="card-title">Metas</h3>

      <div class="card-tools">
        <ul class="nav nav-pills ml-auto">
        
        </ul>
      </div> 

    </div>
    <!-- /.card-header -->


    <div class="card-body p-1">
      <div class="">
        <div class="row">
          <div class="col-4">
            <div class="table-responsive">                           
              <table id="detalles" class="table table-bordered table-sm" style='background-color:#FFFFFF;font-size: 0.9em'> 
                    
                  
                  <thead class="thead-default" style="background-color:#3c8dbc;color: #fff; font-size:9pt">     
                      <th width="15%" class="text-center">Mes</th>                                              
                      
                      <th width="15%" class="text-center">Programada</th>
                      <th class="text-center">Ejecutada</th>

                      <th class="text-center">Desviacion</th>
                      <th class="text-center">%Ejecucion</th>
                      
                      <th width="10%" class="text-center">Op.</th>                                            
                      
                  </thead>
                  <tfoot>
    
                                                                                      
                  </tfoot>
                  <tbody>
                    @for($i = 0; $i < count($arr); $i++)
                    <tr>
                      <td style="text-align: center">{{$arr[$i]['y']}}</td>

                      <td style="text-align: right">{{$arr[$i]['a']}}</td>
                      <td style="text-align: right">
                        @if($arr[$i]['b']==0)
                          <em>No ingresado</em>
                        @else
                        {{$arr[$i]['b']}}
                        @endif
                        
                      </td>

                      <td style="text-align: right">{{$constantes[$i][0]}}</td>
                      <td style="text-align: right">
                        <div class="row">

                          
                          <div class="col"  style="color: {{$colores[$i]}}">
                            @if(!is_null($constantes[$i][0]) && !is_null($constantes[$i][1]))
                            %{{$constantes[$i][1]}}
                            @endif
                          </div>

                        </div>
                        
                      </td>

                      <td style="text-align: center">
                          <!--
                          <i class="fas fa-pen"></i>
                          -->  
                          @if(!is_null($constantes[$i][0]) && !is_null($constantes[$i][1]))
                          <a href="#" title="Eliminar" onclick="swal({//sweetalert
                              title:'¿Seguro de eliminar la cantidad del periodo {{$arr[$i]['y']}}?',
                              //type: 'warning',  
                              type: 'warning',
                              showCancelButton: true,//para que se muestre el boton de cancelar
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText:  'SÍ',
                              cancelButtonText:  'NO',
                              closeOnConfirm:     true,//para mostrar el boton de confirmar
                              html : true
                          },
                          function(){//se ejecuta cuando damos a aceptar
                            window.location.href='{{route('IndicadorActividad.EliminarValor',$indices[$i])}}';
                          });"><i class="fas fa-trash"></i></a>  
                          @endif
                          
                      </td>
                      
                    </tr>
                    @endfor   
                  </tbody>
              </table>
            </div> 
          </div>
          <div class="col-8">
            <h2>Grafico</h2>
            <hr>
            {{$indicador->getVistaGrafico()}}
          </div>
        </div>
        
      </div>
    </div>
    <!-- /.card-body -->
    
  </div>
  <!-- /.card -->
  <br>
<style>
  .circulo{
    
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
     
  }

</style>




@endsection
