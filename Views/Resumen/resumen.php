<?php 
  headerAdmin($data);
  getModal('modalResumen',$data); 
  getModal('modalPrestamos',$data); 
  getModal('modalGastos',$data); 
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

    $idResumen = $data['resumenAnterior']['idresumen'] ?? 0;
    $baseResumen = $data['resumenAnterior']['base'] ?? 0;
    $cobradoResumen = $data['resumenAnterior']['cobrado'] ?? 0;
    $prestamoResumen = $data['resumenAnterior']['ventas'] ?? 0;
    $gastosResumen = $data['resumenAnterior']['gastos'] ?? 0;
    $totalResumen = $data['resumenAnterior']['total'] ?? 0;

    $idResumenActual = $data['resumenActual']['idresumen'] ?? 0;
    $baseResumenActual = $data['resumenActual']['base'] ?? 0;
    $cobradoResumenActual = $data['resumenActual']['cobrado'] ?? 0;
    $prestamoResumenActual = $data['resumenActual']['ventas'] ?? 0;
    $gastosResumenActual = $data['resumenActual']['gastos'] ?? 0;
    $totalResumenActual = $data['resumenActual']['total'] ?? 0;

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
                            <td id="baseResumen">
                              <?= 
                                $data['resumenAnterior']['base'] ?? $data['resumenActual']['base'] ?? $data['resumenCerrado']['base'] ?? 0;
                              ?>
                            </td>
                          </tr>
                          <tr>
                            <th>COBRADO:</th>
                            <td>
                              <?= 
                                $data['resumenAnterior']['cobrado'] ?? $data['resumenActual']['cobrado'] ?? $data['resumenCerrado']['cobrado'] ?? 0;
                              ?>
                            </td>
                          </tr>
                          <tr>
                            <th>VENTAS:</th>
                            <td id="prestamoResumen">
                              <?= 
                                $data['resumenAnterior']['ventas'] ?? $data['resumenActual']['ventas'] ?? $data['resumenCerrado']['ventas'] ?? 0;
                              ?>
                            </td>
                          </tr>
                          <tr>
                            <th>GASTOS:</th>
                            <td id="gastosResumen">
                              <?= 
                                $data['resumenAnterior']['gastos'] ?? $data['resumenActual']['gastos'] ?? $data['resumenCerrado']['gastos'] ?? 0;
                              ?>
                            </td>
                          </tr>
                        </tbody>
                        <caption class="text-end mt-3">TOTAL :&nbsp;&nbsp;&nbsp;
                          <span id="totalResumen">
                            <?= 
                              $data['resumenAnterior']['total'] ?? $data['resumenActual']['total'] ?? $data['resumenCerrado']['total'] ?? 0;
                            ?>
                          </span>
                        </caption>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="d-grid d-md-flex gap-2 justify-content-md-around">
                    <button 
                      title="Editar Base" 
                      class="btn btn-secondary btn-sm me-1 
                      <?= !empty($data['resumenCerrado']['status']) && $data['resumenCerrado']['status'] == 1 ? 'disabled' : ''; ?>" 
                      onclick="fntEditBase(<?= !empty($totalResumen) ? $totalResumen : $totalResumenActual; ?>, <?= !empty($baseResumen) ? $baseResumen : $baseResumenActual; ?>)">
                      <i class="bi bi-pencil-square me-1"></i>
                      Base
                    </button>
                    <div class="btn-group" role="group" aria-label="prestamos">
                      <button title="Registrar Préstamo" class="btn btn-secondary btn-sm me-1 <?= !empty($data['resumenCerrado']['status']) && $data['resumenCerrado']['status'] == 1 ? 'disabled' : ''; ?>" onclick="fntNewVenta(<?= !empty($prestamoResumen) ? $prestamoResumen : $prestamoResumenActual; ?>, <?= !empty($totalResumen) ? $totalResumen : $totalResumenActual; ?>)">
                        <i class="bi bi-plus-circle me-1"></i>
                        Préstamo
                      </button>
                      <button class="btn btn-secondary btn-sm" id="btnViewPrestamos">
                        <i class="bi bi-eye me-0"></i>
                      </button>
                    </div>
                    <div class="btn-group" role="group" aria-label="prestamos">
                      <button title="Registrar Gasto" class="btn btn-secondary btn-sm me-1 <?= !empty($data['resumenCerrado']['status']) && $data['resumenCerrado']['status'] == 1 ? 'disabled' : ''; ?>" onclick="fntNewGasto(<?= !empty($gastosResumen) ? $gastosResumen : $gastosResumenActual; ?>, <?= !empty($totalResumen) ? $totalResumen : $totalResumenActual; ?>)">
                        <i class="bi bi-plus-circle me-1"></i>
                        Gasto
                      </button>
                      <button class="btn btn-secondary btn-sm" id="btnViewGastos">
                        <i class="bi bi-eye me-0"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header text-center text-body-secondary h6">Acciones Resumen <?= $resumenAnterior ?? ''; ?></div>
                <div class="card-body text-center">
                  <form name="formResumen" id="formResumen">
                    <div class="d-grid d-md-flex gap-2 justify-content-md-around">
                      <input type="hidden" name="status" id="status" value="<?= empty($data['resumenCerrado']['status']) ?? 1; ?>">
                      <input type="hidden" name="idResumen" id="idResumen" value="<?= $data['resumenAnterior']['idresumen'] ?? $data['resumenActual']['idresumen'] ?? $data['resumenCerrado']['idresumen'] ?? 0; ?>">
                      <button class="btn <?= !empty($data['resumenCerrado']['status']) && $data['resumenCerrado']['status'] == 1 ? 'btn-success disabled' : 'btn-warning'; ?>" type="submit">
                        <i class="bi bi-check-circle-fill"></i>
                        <?= !empty($data['resumenCerrado']['status']) && $data['resumenCerrado']['status'] == 1 ? 'Registrado' : 'Registrar'; ?>
                      </button>
                      <?php if(!empty($data['resumenCerrado']['status']) && $data['resumenCerrado']['status'] == 1) : ?>
                        <button class="btn btn-warning" type="submit">
                          <i class="bi bi-pencil-square"></i>
                          Corregir
                        </button>
                      <?php endif; ?>
                    </div>
                  </form>
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