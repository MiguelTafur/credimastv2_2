<?php
  headerAdmin($data);
  getModal('modalGastos',$data);
?>


<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="bi bi-clipboard2-pulse"></i>
        <?= $data['page_title'] ?>
        <button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();">
            <i class="bi bi-plus-lg me-0"></i>
        </button>
      </h1>
    </div>

    <!-- UL DE LA LISTA Y EL DASHBOARD -->
    <ul class="nav nav-underline" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista-tab-pane" type="button" role="tab" aria-controls="lista-tab-pane" aria-selected="true">LISTA</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-tab-pane" type="button" role="tab" aria-controls="dashboard-tab-pane" aria-selected="false">DASHBOARD</button>
      </li>
    </ul>
  </div>

  <!-- ALERTAS DELR ESUMEN ANTERIOR -->
  <?php
    if(!empty($data['resumenAnterior'])) {
      resumenAnterior($data);
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
              <form>
                <div class="input-group">
                  <input class="date-picker gastosMes form-control" name="gastosMes" id="gastosMes" placeholder="Mes y Año">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchGastosMes()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaMesGastos"></div>
          </div>
        </div>
        <div class="tab-pane fade" id="nav-anual" role="tabpanel" aria-labelledby="nav-anual-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Gráfica Anual</h3> 
              <form>
                <div class="input-group">
                  <input class="gastosAnio form-control" name="gastosAnio" id="gastosAnio" placeholder="Mes y Año">
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

</script>