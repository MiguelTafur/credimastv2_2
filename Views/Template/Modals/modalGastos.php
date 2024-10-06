<!-- Modal agregar Ruta -->
<div class="modal fade" id="modalFormGastos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Nuevo Gasto</h5>
        <div data-bs-theme="dark">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">    
            <form id="formGasto" name="formGasto" class="row">
              <input type="hidden" id="idGasto" name="idGasto" value="">

              <div class="form-group col-md-6">
                <label class="form-label" for="txtNombre">Nombre</label>
                <input type="text" class="form-control valid validText" id="txtNombre" name="txtNombre" placeholder="Nombre" required>
              </div> 
              <div class="form-group col-md-6">
                <label class="form-label" for="txtValor">Valor</label>
                <input type="tel" class="form-control" id="txtValor" name="txtValor" placeholder="Código" required onkeypress="return controlTag(event)">
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