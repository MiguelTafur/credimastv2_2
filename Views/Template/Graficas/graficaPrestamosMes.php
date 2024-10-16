<?php if($grafica = "prestamosMes"){ $prestamosMes = $data;?>

<script>

mes = '<?= $prestamosMes['numeroMes']; ?>';
ano = '<?= $prestamosMes['anio']; ?>';

Highcharts.chart('graficaMesPrestamos', 
{
  chart: {
      type: 'line',
      scrollablePlotArea: {
        minWidth: 700,
        scrollPositionX: 1
      }
  },

  title: {
      text: '<?= $prestamosMes['mes'].' de '.$prestamosMes['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $prestamosMes['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($prestamosMes['prestamos'] as $dia) {
          echo $dia['dia'].",";
        }
      ?>
    ]
  },

  plotOptions: {
      series: {
        cursor: 'pointer',
        events: {
          click: function(event){
            fntInfoChartPrestamo([ano, mes, event.point.category]);
          }
        }
      },
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },

  series: [{
      name: 'Ventas',
      data: [
        <?php 
          foreach ($prestamosMes['prestamos'] as $prestamo) {
            echo $prestamo['prestamo'].",";
          }
        ?>
      ]
  }],

  responsive: {
      rules: [{
          condition: {
              maxWidth: 500
          },
          chartOptions: {
              legend: {
                  layout: 'horizontal',
                  align: 'center',
                  verticalAlign: 'bottom'
              }
          }
      }]
  }
});
</script>

<?php } ?>