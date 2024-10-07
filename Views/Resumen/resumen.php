<?php 
  headerAdmin($data);
  getModal('modalPrestamos',$data); 
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="bi bi-file-earmark-diff"></i> 
        <?= $data['page_title'] ?> 
      </h1>
    </div>

    <!-- UL DE LA LISTA Y EL DASHBOARD -->
    <ul class="nav nav-underline" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="resumen-tab" data-bs-toggle="tab" data-bs-target="#resumen-tab-pane" type="button" role="tab" aria-controls="resumen-tab-pane" aria-selected="true">RESUMEN</button>
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
      $resumenAnterior = 'Anterior';
    } 

    $baseResumen = $data['resumenAnterior']['base'] ?? 0;
    $cobradoResumen = $data['resumenAnterior']['cobrado'] ?? 0;
    $prestamoResumen = $data['resumenAnterior']['ventas'] ?? 0;
    $gastosResumen = $data['resumenAnterior']['gastos'] ?? 0;
    $totalResumen = $data['resumenAnterior']['total'] ?? 0;
  ?>

  <!-- LISTA Y DASHBOARD -->
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="resumen-tab-pane" role="tabpanel" aria-labelledby="resumen-tab" tabindex="0">
      <div class="tile">
        <div class="tile-body">
          <div class="row justify-content-center">
            <div class="col-6">
              <div class="card mb-4">
                <div class="card-header text-center text-body-secondary h6">Información del Resumen <?= $resumenAnterior ?? ''; ?></div>
                <div class="card-body">
                  <div class="row justify-content-center">
                    <div class="col-12">
                      <table class="table table-borderless text-center mb-0">
                        <tbody>
                          <tr>
                            <th class="w-50">BASE:</th>
                            <td id="baseResumen"><?= $baseResumen; ?></td>
                          </tr>
                          <tr>
                            <th>COBRADO:</th>
                            <td><?= $cobradoResumen; ?></td>
                          </tr>
                          <tr>
                            <th>VENTAS:</th>
                            <td id="prestamoResumen"><?= $prestamoResumen; ?></td>
                          </tr>
                          <tr>
                            <th>GASTOS:</th>
                            <td id="gastosResumen"><?= $gastosResumen; ?></td>
                          </tr>
                        </tbody>
                        <caption class="text-end mt-3">TOTAL :&nbsp;&nbsp;&nbsp;<span id="totalResumen"><?= $totalResumen; ?></span></caption>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="d-grid d-md-flex gap-2 justify-content-md-around">
                    <button class="btn btn-secondary btn-sm" onclick="fntNewBase(<?= $baseResumen; ?>, <?= $totalResumen; ?>)">
                      <i class="bi bi-plus-circle me-1"></i>
                      Base
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="fntNewVenta(<?= $prestamoResumen; ?>, <?= $totalResumen; ?>)">
                      <i class="bi bi-plus-circle me-1"></i>
                      Préstamo
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="fntNewGasto(<?= $gastosResumen; ?>, <?= $totalResumen; ?>)">
                      <i class="bi bi-plus-circle me-1"></i>
                      Gasto
                    </button>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header text-center text-body-secondary h6">Finalizar Resumen <?= $resumenAnterior ?? ''; ?></div>
                <div class="card-body text-center">
                  <button class="btn btn-warning" type="submit">
                    <i class="bi bi-check-circle-fill"></i>
                    Registrar
                  </button>
                </div>
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