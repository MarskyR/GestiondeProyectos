@extends ('Layout.Plantilla')

@section('contenido')

  

    <div id="piechart"></div>


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
            title: 'Gastos según Empleados Desde' + ' {{$fechaI}} hasta {{$fechaF}} en la Sede {{$sede->nombre}}',
            width: 900,
            height: 500,
        };
        
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        
        chart.draw(data, options);
    }
    </script>


@endsection
