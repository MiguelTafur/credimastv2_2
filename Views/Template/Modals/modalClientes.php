<div class="modal fade" id="modalFormCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Nuevo Cliente</h1>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">
            <form id="formCliente" name="formCliente">
              <input type="hidden" id="idUsuario" name="idUsuario" value="">
              <p class="text-warning fst-italic">Todos los campos son obligatorios.</p>
              <div class="mb-3">
                <label class="form-label">Identificación</label>
                <input class="form-control valid validText" id="txtIdentificacion" name="txtIdentificacion" type="text" placeholder="Identificación del Cliente" >
              </div>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input class="form-control valid validText" id="txtNombre" name="txtNombre" type="text" placeholder="Nombre del Cliente" >
              </div>
              <div class="mb-3">
                <label class="form-label">Negocio</label>
                <input class="form-control valid validText" id="txtApellido" name="txtApellido" type="text" placeholder="Negocio del Cliente" >
              </div>
              <div class="mb-3">
                <label class="form-label">Teéfono</label>
                <input type="tel" class="form-control valid validNumber" id="txtTelefono" name="txtTelefono" required="" onkeypress="return controlTag(event)" placeholder="Teléfono del Cliente" >
              </div>
              <div class="mb-3">
                <label class="form-label">Dirección Negocio</label>
                <input class="form-control valid validText" id="txtDireccion1" name="txtDireccion1" type="text" placeholder="Dirección del Negocio" >
              </div>
              <div class="mb-3">
                <label class="form-label">Dirección Casa</label>
                <input class="form-control valid validText" id="txtDireccion2" name="txtDireccion2" type="text" placeholder="Dirección de la Casa" >
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

