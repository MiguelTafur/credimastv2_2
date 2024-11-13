<?php
  headerAdmin($data);
  getModal('modalPrestamos',$data);
?>

<style>
  #tile {
    position: relative;
  }

  .payAll {
    position: absolute;
    right: 2.5%;
    top: .5%;
  }
  
  @media (min-width: 768px) {
    .payAll {
      position: absolute;
      left: 50%;
    }
}
</style>


<main class="app-content">
  <div class="app-title">
    <div class="mt-2 mt-lg-0">
      <h1>
        <i class="bi bi-cash-coin"></i>
        <?= $data['page_title'] ?>
        <button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();" >
            <i class="bi bi-plus-lg m-0"></i>
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

  <!-- <button class="btn btn-danger mb-3" onclick="accion()">Acción pagos</button>
  <button class="btn btn-danger mb-3" onclick="accionPrestamos()">Acción préstamos</button>
  <button class="btn btn-danger mb-3" onclick="accionPrestamosUsuario()">Acción préstamos usuarioid</button> -->

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
        <table class="table table-striped align-middle caption-top">
          <caption class="mb-2">CLIENTE: &nbsp;&nbsp;<span class="text-decoration-underline" id="cptCliente"></span></caption>
          <thead>
            <tr>
              <th class="text-center">FECHA</th>
              <th class="text-center">VALOR</th>
              <th class="text-center">HORA</th>
              <th class="text-center">USUARIO</th>
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
          <div class="tile" id="tile">
            <!-- BOTÓN DE PAGAR VARIOS ABONOS -->
            <div class="payAll fs-1 text-warning">
              <a href="#" onclick="fntPayAll();"><i class="bi bi-coin me-0" aria-hidden="true"></i></a>
            </div>
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-striped align-middle" id="tablePrestamos">
                  <thead>
                    <tr>
                      <th></th>
                      <th>CLIENTE</th>
                      <th>ABONO</th>
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
      <!-- WIDGETS -->
      <div class="row">
        <div class="col-lg-6">
          <div class="widget-small light "><i class="icon bi bi-cash fs-1"></i>
            <div class="info">
              <h4>VALOR ACTIVO</h4>
              <p><span class="fst-italic" id="valorActivo"><?= valorActivoYEstimadoPrstamos()['valorActivo']; ?></span></p>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="widget-small light "><i class="icon bi bi-coin fs-1"></i>
            <div class="info">
              <h4>COBRADO ESTIMADO</h4>
              <p><span class="fst-italic" id="cobradoEstimado"><?= valorActivoYEstimadoPrstamos()['cobradoEstimado']; ?></span></p>
            </div>
          </div>
        </div>
      </div>
      <hr class="border border-warning border-1 mb-4">
      <!-- GRÁFICA VENTAS -->
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
              <h3 class="tile-title">Ventas</h3>
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="date-picker prestamosMes form-control" name="prestamosMes" id="prestamosMes" placeholder="Mes y Año">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchPrestamosMes()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaMesPrestamos"></div>
            <button class="btn btn-warning btn-sm mt-4" onclick="fntViewDetallePrestamos()"><i class="bi bi-calendar4-week"></i>Buscar Préstamos por rango de fechas</button>
          </div>
        </div>
        <div class="tab-pane fade" id="nav-anual" role="tabpanel" aria-labelledby="nav-anual-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Ventas</h3> 
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="prestamosAnio form-control" name="prestamosAnio" id="prestamosAnio" placeholder="Año" minlength="4" maxlength="4" onkeypress="return controlTag(event)">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchPrestamosAnio()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaAnioPrestamos"></div>  
          </div>
        </div>
      </div>

      <hr class="border border-warning border-1 mb-4">
      <!-- GRÁFICAS COBRADO -->
      <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <button class="nav-link active" id="nav-mensual2-tab" data-bs-toggle="tab" data-bs-target="#nav-mensual2" type="button" role="tab" aria-controls="nav-mensual2" aria-selected="true">Mensual</button>
          <button class="nav-link" id="nav-anual2-tab" data-bs-toggle="tab" data-bs-target="#nav-anual2" type="button" role="tab" aria-controls="nav-anual2" aria-selected="false">Anual</button>
        </div>
      </nav>
      <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-mensual2" role="tabpanel" aria-labelledby="nav-mensual2-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Cobrado</h3>
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="date-picker cobradoMes form-control" name="cobradoMes" id="cobradoMes" placeholder="Mes y Año">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchCobradoMes()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaMesCobrado"></div>
            <button class="btn btn-warning btn-sm mt-4" onclick="fntViewDetalleCobrado()"><i class="bi bi-calendar4-week"></i>Buscar Cobrado por rango de fechas</button>
          </div>
        </div>
        <div class="tab-pane fade" id="nav-anual2" role="tabpanel" aria-labelledby="nav-anual2-tab" tabindex="0">
          <div class="tile">
            <div class="container-title d-flex justify-content-between flex-wrap">
              <h3 class="tile-title">Cobrado</h3> 
              <form class="mb-2 mb-md-0">
                <div class="input-group">
                  <input class="cobradoAnio form-control" name="cobradoAnio" id="cobradoAnio" placeholder="Año" minlength="4" maxlength="4" onkeypress="return controlTag(event)">
                  <button type="button" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchCobradoAnio()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  </button>
                </div>
              </form>
            </div>
            <div id="graficaAnioCobrado"></div>  
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>

<script>
let mes = '<?= $data['prestamosMDia']['numeroMes']; ?>';
let ano = '<?= $data['prestamosMDia']['anio']; ?>';

//MENSUAL VENTAS
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
      text: '<?= $data['prestamosMDia']['mes'].' de '.$data['prestamosMDia']['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $data['prestamosMDia']['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($data['prestamosMDia']['prestamos'] as $dia) {
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
          foreach ($data['prestamosMDia']['prestamos'] as $prestamo) {
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

//ANUAL VENTAS
Highcharts.chart('graficaAnioPrestamos', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<?= $data['prestamosAnio']['anio']; ?>'
    },
    subtitle: {
        text: '<b>Total: <?= $data['prestamosAnio']['totalPrestamos']; ?></b>'
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
        name: 'Préstamos',
        colors: [
            '#9b20d9', '#9215ac', '#861ec9', '#7a17e6', '#7010f9', '#691af3',
            '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
            '#3667c9', '#2f72c3'
        ],
        colorByPoint: true,
        groupPadding: 0,
        data: [
          <?php 
            foreach ($data['prestamosAnio']['meses'] as $mes) {
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

//MENSUAL COBRADO
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
      text: '<?= $data['cobradoMDia']['mes'].' de '.$data['cobradoMDia']['anio']; ?>'
  },

  subtitle: {
      text: '<b>Total: <?= $data['cobradoMDia']['total']; ?></b>'
  },

  yAxis: {
      title: {
          text: 'CREDIMAST'
      }
  },

  xAxis: {
    categories: [
      <?php 
        foreach ($data['cobradoMDia']['totalCobrado'] as $dia) {
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
      name: 'Cobrado',
      data: [
        <?php 
          foreach ($data['cobradoMDia']['totalCobrado'] as $cobrado) {
            echo $cobrado['cobrado'].",";
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

//ANUAL COBRADO
Highcharts.chart('graficaAnioCobrado', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<?= $data['cobradoAnio']['anio']; ?>'
    },
    subtitle: {
        text: '<b>Total: <?= $data['cobradoAnio']['totalCobrado']; ?></b>'
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
        name: 'Cobrado',
        colors: [
            '#9b20d9', '#9215ac', '#861ec9', '#7a17e6', '#7010f9', '#691af3',
            '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
            '#3667c9', '#2f72c3'
        ],
        colorByPoint: true,
        groupPadding: 0,
        data: [
          <?php 
            foreach ($data['cobradoAnio']['meses'] as $mes) {
              echo "['".$mes['mes']."',".$mes['cobrado']."],";
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