<!-- Modal agregar y Préstamo -->
<div class="modal fade" id="modalFormPrestamo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Registrar Préstamo</h1>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 text-end">
            <button class="btn btn-warning btn-sm mb-3">
              <i class="bi bi-person-plus-fill"></i>Cliente
            </button>
          </div>
        </div>
        <div class="tile">
          <div class="tile-body">
            <form class="row g-3" id="formPrestamos" name="formPrestamos">
              <input type="hidden" id="idCliente" name="idCliente" value="">
              <div class="col-12">
                <label for="listClientes" class="form-label">Cliente</label>
                <select class="form-select listClientes" id="listClientes" name="listClientes" required style="width: 100%;"></select>
              </div>
              <div class="col-md-6">
              <label for="txtMonto" class="form-label">Monto</label>
              <input type="tel" class="form-control valid validNumber" id="txtMonto" name="txtMonto" required placeholder="Monto" onkeypress="return controlTag(event)">
              </div>
              <div class="col-md-6" id="divListRuta">
                <label for="txtTaza" class="form-label">Taza</label>
                <input type="tel" class="form-control valid validNumber" id="txtTaza" name="txtTaza" required placeholder="Taza" onkeypress="return controlTag(event)">
              </div>
              <div class="col-md-6" id="divListRol">
                <label for="listFormato" class="form-label">Formato</label>
                <select class="form-select" id="listFormato" name="listFormato" required style="width: 100%;">
                  <option value=""></option>
                  <option value="1">Diario</option>
                  <option value="2">Semanal</option>
                  <option value="3">Mensual</option>
                </select>
              </div>
              <div class="col-md-6" id="divListStatus">
                <label for="txtPlazo" class="form-label">Plazo</label>
                <input type="tel" class="form-control valid validNumber" id="txtPlazo" name="txtPlazo" required placeholder="Plazo" onkeypress="return controlTag(event)">
              </div>
              <div class="col-12" id="divListStatus">
                <label for="txtObservacion" class="form-label">Observación</label>
                <textarea class="form-control" id="txtObservacion" name="txtObservacion"></textarea>
              </div>
              <div class="tile-footer mt-4">
                <button class="btn btn-warning" type="submit">
                  <i class="bi bi-check-circle-fill"></i>
                  <span id="btnText">Registrar</span>
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