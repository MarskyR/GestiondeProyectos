@extends ('Layout.Plantilla')

@section('contenido')

  
<div class="row">
    <!-- Left col -->
    <section class="col-lg-1 connectedSortable ui-sortable"></section>
    <section class="col-lg-9 connectedSortable ui-sortable">
      <!-- Custom tabs (Charts with tabs)-->
      <div class="card">
        <div class="card-header ui-sortable-handle" style="cursor: move;">
          <h3 class="card-title">
            <i class="fas fa-chart-pie mr-1"></i>
            <b>Empleados</b>
            </h3>
        </div><!-- /.card-header -->
        <div class="card-body">
          <div class="tab-content p-0">
            <!-- Morris chart - Sales -->
            <div id="piechart"></div>
            
          </div>
        </div><!-- /.card-body -->
      </div>

     
      <!-- /.card -->
    </section>
    <!-- /.Left col -->
    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <!-- right col -->
</div>

    

@endsection

@section('script')

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable([
        ['Language', 'Rating'],
        <?php

           

            if( count($listaX) > 0){
                $i=0;
                while($i< count($listaX)  ){
                    $row = $listaX[$i];
                    echo "['".$row->NombreEmp."', ".$row->Suma_Empleado."],";
                    $i++;
                }
            }

        ?>
        ]);
        
        var options = {
            title: 'Gastos según Empleados Desde' + ' {{$fechaI}} hasta {{$fechaF}}',
            width: 900,
            height: 500,
        };
        
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        
        chart.draw(data, options);
    }
    </script>


@endsection
