<?php if($grafica = "gastosMes"){ $gastosMes = $data;?>

<script>

  mes = '<?= $gastosMes['numeroMes']; ?>';
  ano = '<?= $gastosMes['anio']; ?>';

  Highcharts.chart('graficaMesGastos', {

  chart: {
      type: 'line',
      scrollablePlotArea: {
        minWidth: 700,
        scrollPositionX: 1
      }
  },

  title: {
      text: '<?= $gastosMes['mes'].' de '.$gastosMes['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $gastosMes['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($gastosMes['gastos'] as $dia) {
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
            fntInfoChartGasto([ano, mes, event.point.category]);
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
      name: 'Gastos',
      data: [
        <?php 
          foreach ($gastosMes['gastos'] as $gasto) {
            echo $gasto['gasto'].",";
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