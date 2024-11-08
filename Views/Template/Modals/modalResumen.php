<!-- CANVAS VER PRÉSTAMOS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPrestamos" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">PRÉSTAMOS</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>CLIENTE</th>
            <th>VALOR</th>
            <th>HORA</th>
            <th>USUARIO</th>
            <th>ACCIÓN</th>
          </tr>
        </thead>
        <tbody id="tbodyPrestamos">
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CANVAS VER GASTOS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasGastos" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">GASTOS</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>NOMBRE</th>
            <th>VALOR</th>
            <th>HORA</th>
            <th>USUARIO</th>
            <th>ACCIÓN</th>
          </tr>
        </thead>
        <tbody id="tbodyGastos">
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CANVAS VER BASE -->
<?php 
  if(getBaseActualAnterior() != 0) {
    $baseAnterior = getBaseActualAnterior()['anterior'];
    $baseActual = getBaseActualAnterior()['actual'];
    $idBaseActual = getBaseActualAnterior()['idBaseActual'];
  }
?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBase" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">Base</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <h6 class="text-end mb-4">Anterior:  <span class="h6 fw-bold fst-italic" id="baseAnterior"><?= $baseAnterior ?></span></h6>
    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-secondary">
        <span id="baseActual">Actual: <?= $baseActual ?></span>
          <button class="btn btn-danger btn-sm" title="Eliminar Base" onclick="fntDelBase(<?= $idBaseActual; ?>)"><i class="bi bi-trash3-fill me-0"></i></button>
        </form>
      </li>
    </ul>
  </div>
</div>

<!-- MODAL BUSCAR RESUMEN POR RANGO DE FECHA -->
<div class="modal fade" id="modalDetalleResumen" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Resumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaResumen" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchResumenD()"><i class="bi bi-search me-0"></i></button>
            </div>
          </div>
        </form>
        <div id="divResumenD" class="d-none">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
              <tr class="text-center">
                  <th>DIA</th>
                  <th>BASE</th>
                  <th>COBRADO</th>
                  <th>VENTAS</th>
                  <th>GASTOS</th>
                  <th>TOTAL</th>
                </tr>
              </thead>
              <tbody id="datosResumenD"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
