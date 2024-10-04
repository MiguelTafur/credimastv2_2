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
    <div class="tab-pane fade" id="dashboard-tab-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
      
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>