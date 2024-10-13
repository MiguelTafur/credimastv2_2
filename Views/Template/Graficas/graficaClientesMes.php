<?php if($grafica = "clientesMes"){ $clientesMes = $data;?>

<script>

  mes = '<?= $clientesMes['numeroMes']; ?>';
  ano = '<?= $clientesMes['anio']; ?>';

  Highcharts.chart('graficaMesClientes', {

  chart: {
      type: 'line',
      scrollablePlotArea: {
        minWidth: 700,
        scrollPositionX: 1
      }
  },

  title: {
      text: '<?= $clientesMes['mes'].' de '.$clientesMes['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $clientesMes['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($clientesMes['usuarios'] as $dia) {
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
            fntInfoChartPersona([ano, mes, event.point.category]);
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
      name: 'Clientes nuevos',
      data: [
        <?php 
          foreach ($clientesMes['usuarios'] as $usuario) {
            echo $usuario['usuario'].",";
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