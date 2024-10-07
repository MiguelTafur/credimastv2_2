<!-- Modal agregar Ruta -->
<div class="modal fade" id="modalFormBase" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Nueva Base</h5>
        <div data-bs-theme="dark">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">    
            <form id="formBase" name="formBase" class="row">
              <input type="hidden" id="idBase" name="idBase" value="">
              <div class="form-group col-12 mb-4    ">
                <label class="form-label" for="txtValor">Valor</label>
                <input type="tel" class="form-control" id="txtValor" name="txtValor" placeholder="Valor" required onkeypress="return controlTag(event)">
              </div>  
              <div class="form-group col-md-12">
                <label class="form-label" for="txtObservacion">Observación</label>
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