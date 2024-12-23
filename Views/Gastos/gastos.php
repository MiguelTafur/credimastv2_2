<?php
  headerAdmin($data);
  getModal('modalGastos',$data);
?>


<main class="app-content">
  <div class="app-title align-items-center">
    <div class="d-flex justify-content-between w-100 mt-2">
      <div class="mt-2 mt-lg-0">
        <h1>
          <!-- <i class="bi bi-clipboard2-pulse"></i> -->
          <button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();">
              <i class="bi bi-plus-circle"></i>Gasto
          </button>
        </h1>
      </div>
      <!-- UL DE LA LISTA Y EL DASHBOARD -->
      <ul class="nav nav-underline" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista-tab-pane" type="button" role="tab" aria-controls="lista-tab-pane" aria-selected="true">LISTA</button>
        </li>
        <?php if($_SESSION['idRol'] == 1) : ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-tab-pane" type="button" role="tab" aria-controls="dashboard-tab-pane" aria-selected="false">DASHBOARD</button>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <!-- ALERTAS DELR ESUMEN ANTERIOR -->
  <?php
    if(!empty($data['resumenAnterior'])) {
      resumenAnterior($data);
    }

    if(getResumenActual1($_SESSION['idRuta'])) {
      resumenOk($data);
    }
  ?>

  <!-- LISTA DE PAGAMENTOS -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasRightLabel">PAGAMENTOS</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="text-center">Fecha</th>
              <th class="text-center">Hora</th>
              <th class="text-center">Abono</th>
            </tr>
          </thead>
          <tbody id="tbodyPagamentos">
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- LISTA Y DASHBOARD -->
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="lista-tab-pane" role="tabpanel" aria-labelledby="lista-tab" tabindex="0">
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-striped align-middle" id="tableGastos">
                  <thead>
                    <tr>
                      <th>FECHA</th>
                      <th>NOMBRE</th>
                      <th>VALOR</th>
                      <th>HORA</th>
                      <th>DETALLES</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="dashboard-tab-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
      <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <button class="nav-link active" id="nav-mensual-tab" data-bs-toggle="tab" data-bs-target="#nav-mensual" type="button" role="tab" aria-controls="nav-mensual" aria-selected="true">Mensual</button>
          <button class="nav-link" id="nav-anual-tab" data-bs-toggle="tab" data-bs-target="#nav-anual" type="button" role="tab" aria-controls="nav-anual" aria-selected="false">Anual</button>
        </div>
      </nav>
      <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-mensual" role="tabpanel" aria-labelledby="nav-mensual-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Gráfica Mensual</h3>
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="date-picker gastosMes form-control" name="gastosMes" id="gastosMes" placeholder="Mes y Año">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchGastosMes()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaMesGastos"></div>
            <button class="btn btn-warning btn-sm mt-4" onclick="fntViewDetalleGastos()"><i class="bi bi-calendar4-week"></i>Buscar Gastos por rango de fechas</button>
          </div>
        </div>
        <div class="tab-pane fade" id="nav-anual" role="tabpanel" aria-labelledby="nav-anual-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Gráfica Anual</h3> 
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="gastosAnio form-control" name="gastosAnio" id="gastosAnio" placeholder="Mes y Año" minlength="4" maxlength="4" onkeypress="return controlTag(event)">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchGastosAnio()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaAnioGastos"></div>  
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>

<script>

let mes = '<?= $data['gastosMDia']['numeroMes']; ?>';
let ano = '<?= $data['gastosMDia']['anio']; ?>';

//MENSUAL
Highcharts.chart('graficaMesGastos', 
{
  chart: {
      type: 'line',
      scrollablePlotArea: {
        minWidth: 700,
        scrollPositionX: 1
      }
  },

  title: {
      text: '<?= $data['gastosMDia']['mes'].' de '.$data['gastosMDia']['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $data['gastosMDia']['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($data['gastosMDia']['gastos'] as $dia) {
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
          foreach ($data['gastosMDia']['gastos'] as $gasto) {
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

//ANUAL
Highcharts.chart('graficaAnioGastos', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<?= $data['gastosAnio']['anio']; ?>'
    },
    subtitle: {
        text: '<b>Total: <?= $data['gastosAnio']['totalGastos']; ?></b>'
    },
    xAxis: {
        type: 'category',
        labels: {
            autoRotation: [-45, -90],
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'CREDIMAST'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: '{point.y}'
    },
    series: [{
        name: 'Gastos',
        colors: [
            '#9b20d9', '#9215ac', '#861ec9', '#7a17e6', '#7010f9', '#691af3',
            '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
            '#3667c9', '#2f72c3'
        ],
        colorByPoint: true,
        groupPadding: 0,
        data: [
          <?php 
            foreach ($data['gastosAnio']['meses'] as $mes) {
              echo "['".$mes['mes']."',".$mes['total']."],";
            }
          ?> 
        ],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            inside: true,
            verticalAlign: 'top',
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});

</script>