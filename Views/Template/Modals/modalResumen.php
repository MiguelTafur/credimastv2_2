<!-- CANVAS VER PRÉSTAMOS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPrestamos" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">PRÉSTAMOS</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Valor</th>
            <!-- <th>Acciones</th> -->
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
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody id="tbodyGastos">
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CANVAS VER GASTOS -->
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
