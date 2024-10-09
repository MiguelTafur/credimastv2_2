<!-- MODAL EDITAR BASE -->
<div class="modal fade" id="modalFormBase" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Editar Base</h5>
        <div data-bs-theme="dark">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">    
            <form id="formBase" name="formBase" class="row">
              <div class="form-group col-12 mb-4">
                <label class="form-label" for="txtValor">Valor</label>
                <input type="tel" class="form-control" id="txtValor" name="txtValor" placeholder="Valor" required onkeypress="return controlTag(event)">
              </div>  
              <div class="tile-footer mt-4">
                <button class="btn btn-warning" type="submit">
                  <i class="bi bi-check-circle-fill"></i>
                  <span id="btnText">Actualizar</span>
                </button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                  <i class="bi bi-x-circle-fill me-2"></i>Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

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
            <th class="text-center">Cliente</th>
            <th class="text-center">Valor</th>
            <th class="text-center">Acciones</th>
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
            <th class="text-center">Nombre</th>
            <th class="text-center">Valor</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbodyGastos">
        </tbody>
      </table>
    </div>
  </div>
</div>