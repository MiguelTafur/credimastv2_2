<!-- Modal agregar y editar usuario -->
<div class="modal fade" id="modalFormUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Nuevo Usuario</h1>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <p class="text-warning fst-italic">Todos los campos son obligatorios.</p>
        <div class="tile">
          <div class="tile-body">
            <form class="row g-3" id="formUsuario" name="formUsuario">
              <input type="hidden" id="idUsuario" name="idUsuario" value="">
              <div class="col-md-6">
                <label for="txtNombre" class="form-label">Nombre</label>
                <input type="text" class="form-control valid validText" id="txtNombre" name="txtNombre" required placeholder="Nombre del Usuario">
              </div>
              <div class="col-md-6">
              <label for="txtEmail" class="form-label">Email</label>
              <input type="email" class="form-control valid validEmail" id="txtEmail" name="txtEmail" required autocomplete="username" placeholder="Email del Usuario">
              </div>
              <div class="col-md-4" id="divListRuta">
                <label for="listRuta" class="form-label">Ruta</label>
                <select class="form-select" id="listRuta" name="listRuta" required style="width: 100%;"></select>
              </div>
              <div class="col-md-4" id="divListRol">
                <label for="listRolid" class="form-label">Tipo Usuario</label>
                <select class="form-select" id="listRolid" name="listRolid" required style="width: 100%;"></select>
              </div>
              <div class="col-md-4" id="divListStatus">
                <label for="listStatus" class="form-label">Status</label>
                <select class="form-select" id="listStatus" name="listStatus" required style="width: 100%;">
                  <option value=""></option>
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
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

<!-- Modal detalles de usuario -->
<div class="modal fade" id="modalViewUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Datos de Usuario</h1>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="tile my-2">
          <div class="tile-body">
            <table class="table table-bordered align-middle">
              <tbody>
                <tr>
                  <td>NOMBRE:</td>
                  <td id="celNombres"></td>
                </tr>
                <tr>
                  <td>EMAIL</td>
                  <td id="celEmail"></td>
                </tr>
                <tr>
                  <td>TIPO USUARIO</td>
                  <td id="celTipoUsuario"></td>
                </tr>
                <tr>
                  <td>ESTADO:</td>
                  <td id="celEstado"></td>
                </tr>
                <tr>
                  <td>FECHA REGISTRO:</td>
                  <td id="celFechaRegistro"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
