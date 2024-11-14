<?php 
  headerAdmin($data);
  getModal('modalClientes',$data); 
?>

<main class="app-content">
  <div class="app-title">
    <div class="d-flex justify-content-between w-100 mt-2">
      <div class="mt-2 mt-lg-0">
        <h1>
          <?php if($_SESSION['permisosMod']['w']){ ?>
          <button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();" >
              <i class="bi bi-person-plus-fill"></i>Cliente
          </button>
          <?php } ?>
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

  <!-- LISTA Y DASHBOARD -->
  <div class="tab-content" id="myTabContent">
    <!-- LISTA -->
    <div class="tab-pane fade show active" id="lista-tab-pane" role="tabpanel" aria-labelledby="lista-tab" tabindex="0">
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="tableClientes">
                  <thead>
                    <tr>
                      <th>NOMBRE</th>
                      <th>NEGOCIO</th>
                      <th>TELEFONO</th>
                      <th class="text-center">ACCIONES</th>
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

    <!-- DASHBOARD -->
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
                  <input class="date-picker clientesMes form-control" name="clientesMes" id="clientesMes" placeholder="Mes y Año">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchClientesMes()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaMesClientes"></div>
          </div>
        </div>
        <div class="tab-pane fade" id="nav-anual" role="tabpanel" aria-labelledby="nav-anual-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Gráfica Anual</h3> 
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="clientesAnio form-control" name="clientesAnio" id="clientesAnio" placeholder="Mes y Año">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchClientesAnio()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaAnioClientes"></div>  
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>

<script>

let mes = '<?= $data['clientesMDia']['numeroMes']; ?>';
let ano = '<?= $data['clientesMDia']['anio']; ?>';

//MENSUAL
Highcharts.chart('graficaMesClientes', 
{
  chart: {
      type: 'line',
      scrollablePlotArea: {
        minWidth: 700,
        scrollPositionX: 1
      }
  },

  title: {
      text: '<?= $data['clientesMDia']['mes'].' de '.$data['clientesMDia']['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $data['clientesMDia']['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($data['clientesMDia']['usuarios'] as $dia) {
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
          foreach ($data['clientesMDia']['usuarios'] as $usuario) {
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

//ANUAL
Highcharts.chart('graficaAnioClientes', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<?= $data['clientesAnio']['anio']; ?>'
    },
    subtitle: {
        text: '<b>Total: <?= $data['clientesAnio']['totalUsuarios'] ?></b>'
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
        pointFormat: ''
    },
    series: [{
        name: 'Clientes',
        colors: [
            '#9b20d9', '#9215ac', '#861ec9', '#7a17e6', '#7010f9', '#691af3',
            '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
            '#3667c9', '#2f72c3'
        ],
        colorByPoint: true,
        groupPadding: 0,
        data: [
          <?php 
            foreach ($data['clientesAnio']['meses'] as $mes) {
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