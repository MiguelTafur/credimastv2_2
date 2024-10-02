<?php 
  headerAdmin($data);
  getModal('modalPrestamos',$data); 
?>


<main class="app-content">
  <div class="app-title">
    <div>
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
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-tab-pane" type="button" role="tab" aria-controls="dashboard-tab-pane" aria-selected="false">DASHBOARD</button>
      </li>
    </ul>
  </div>

  <!-- ALERTA -->
  <div class="alert alert-warning font-monospace" role="alert">
    <i class="bi bi-exclamation-triangle"></i>
    Pendiente por finalizar el <a href="<?= base_url(); ?>/resumen" class="alert-link">Resumen</a> del dia <span class="fst-italic fw-bold"><?= date("d-m-Y", strtotime($data['resumenAnterior']['datecreated'])); ?></span>
  </div>

  <div class="alert alert-light alert-dismissible fade show font-monospace" role="alert">
    <i class="bi bi-info-circle"></i>
    Recuerda que los <strong class="fst-italic">Pagos</strong> y <strong class="fst-italic">Préstamos</strong> realizados serán registrados con la misma <strong class="fst-italic">Fecha</strong> del resumen pendiente.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <div class="alert alert-light alert-dismissible fade show font-monospace" role="alert">
    <i class="bi bi-info-circle"></i>
    Si eliminas todos los <strong class="fst-italic">Datos</strong>, el <strong class="fst-italic">Resumen</strong> será <strong class="fst-italic">Eliminado</strong> automáticamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <!-- OFFCANVAS DE LOS PAGAMENTOS -->
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

  <!-- TABLA DE LOS PRESTAMOS -->
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="lista-tab-pane" role="tabpanel" aria-labelledby="lista-tab" tabindex="0">
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
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
      
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>