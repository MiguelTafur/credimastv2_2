<?php 
  headerAdmin($data);
  getModal('modalClientes',$data); 
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="bi bi-people-fill m-0"></i>
        <?= $data['page_title'] ?> 
        <?php if($_SESSION['permisosMod']['w']){ ?>
        <button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();" >
            <i class="bi bi-person-plus-fill m-0"></i>
        </button>
        <?php } ?>
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
    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button class="nav-link active" id="nav-mensual-tab" data-bs-toggle="tab" data-bs-target="#nav-mensual" type="button" role="tab" aria-controls="nav-mensual" aria-selected="true">Mensual</button>
        <button class="nav-link" id="nav-anual-tab" data-bs-toggle="tab" data-bs-target="#nav-anual" type="button" role="tab" aria-controls="nav-anual" aria-selected="false">Anual</button>
      </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="nav-mensual" role="tabpanel" aria-labelledby="nav-mensual-tab" tabindex="0">
        <div class="tile">
          <div class="container-title d-flex justify-content-between flex-wrap ">
            <h3 class="tile-title">Gráfica Mensual</h3>
            <div class="dflex">
              <form action="">
                <div class="input-group">
                  <input class="date-picker clientesMes" name="clientesMes" id="clientesMes" placeholder="Mes y Año">
                  <button type="submit" class="btn btn-warning btn-sm" id="button-addon2" onclick="fntSearchClientesMes()">
                    <i class="bi bi-search me-0" title="Buscar fecha"></i>
                  <!-- </button> -->
                <!-- </div> -->
              </form>
            </div>
          </div>
        </div>
        <div id="graficaMesClientes"></div>
      </div>
      <div class="tab-pane fade" id="nav-anual" role="tabpanel" aria-labelledby="nav-anual-tab" tabindex="0">
        <div class="container-title">
          <div class="tile">
            <h3 class="tile-title">Gráfica Anual</h3> 
          
          <!-- <div class="dflex">
            <input class="clientesAnio" name="clientesAnio" placeholder="Año" minlength="4" maxlength="4" onkeypress="return controlTag(event);">
            <button type="button" class="btn btn-info btn-sm me-0" onclick="fntSearchClientesAnio()">
              <i class="bi bi-search" title="Procurar data"></i>
            </button>
          </div> -->
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

  Highcharts.chart('graficaMesClientes', {

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
</script>