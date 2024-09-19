<div class="modal fade" id="modalFormRol" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Nuevo Rol</h1>
        <div data-bs-theme="dark">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">
            <form id="formRol" name="formRol">
              <input type="hidden" id="idRol" name="idRol" value="">  
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input class="form-control valid validText" id="txtNombre" name="txtNombre" type="text" placeholder="Nombre del rol" >
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="2" placeholder="Descripción del rol" ></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label" for="listStatus">Status</label>
                <select class="form-select" id="listStatus" name="listStatus" required style="width: 100%;">
                  <option value=""></option>
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>
              <div class="tile-footer">
                <button class="btn btn-warning" type="submit" id="btnActionForm">
                  <i class="bi bi-check-circle-fill"></i>
                  <span id="btnText">Registrar</span>
                </button>&nbsp;&nbsp;&nbsp;
                <a class="btn btn-secondary" href="#" data-bs-dismiss="modal">  
                  <i class="bi bi-x-circle-fill me-2"></i>Cancelar
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

