<div id="table1"></div>



<script>
    //var datosActuales=<?php echo json_encode($arr); ?>;
    new Morris.Line({//META - EJECUTADA
        element: 'table1',
        data: <?php echo json_encode($arr); ?>,
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['META', 'EJECUTADA'],
        resize: true,
        lineColors: ['#C14D9F','#2CB4AC'],
        lineWidth: 1,
        gridTextSize: 10,
        //parseTime: false,//hace que los parametros de las funciones ya no traten a el ejex como tipo date
        xLabelFormat: function (x) {//para como se vera en el ejex
          var IndexToMonth = [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dic" ];
          var month = IndexToMonth[ x.getMonth() ];
          var year = x.getFullYear();
          return month+'-'+year;
        },
        dateFormat: function (x) {//para ver como se mostrara en los puntos de del plano
          var IndexToMonth = [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dic" ];
          var month = IndexToMonth[ new Date(x).getMonth() ];
          var year = new Date(x).getFullYear();
          return month+'-'+year;
        }
    });
  
  
  </script>