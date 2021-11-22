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
                    echo "['".$row->Sede."', ".$row->Suma_Sede."],";
                    $i++;
                }
            }

        ?>
        ]);
        
        var options = {
            title: 'Gastos según sedes Desde' + ' {{$fechaI}} hasta {{$fechaF}}',
            width: 900,
            height: 500,
        };
        
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        
        chart.draw(data, options);
    }
    </script>


@endsection
