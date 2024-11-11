<?php if($grafica = "cobradoMes"){ $cobradoMes = $data;?>

<script>

mes = '<?= $cobradoMes['numeroMes']; ?>';
ano = '<?= $cobradoMes['anio']; ?>';

Highcharts.chart('graficaMesCobrado', 
{
  chart: {
      type: 'line',
      scrollablePlotArea: {
        minWidth: 700,
        scrollPositionX: 1
      }
  },

  title: {
      text: '<?= $cobradoMes['mes'].' de '.$cobradoMes['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $cobradoMes['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($cobradoMes['totalCobrado'] as $dia) {
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
            fntInfoChartCobrado([ano, mes, event.point.category]);
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
          foreach ($cobradoMes['totalCobrado'] as $prestamo) {
            echo $prestamo['cobrado'].",";
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