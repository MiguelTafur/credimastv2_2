<!-- MODAL REGISTRAR Y EDITAR CLIENTE -->
<div class="modal fade" id="modalFormCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
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
            <form class="row g-3" id="formCliente" name="formCliente">
              <input type="hidden" id="idCliente" name="idCliente" value="">
              <p class="text-warning fst-italic">Todos los campos son obligatorios.</p>
              <div class="col-md-6">
                <label class="form-label" for="txtIdentificacion">Identificación</label>
                <input class="form-control valid" id="txtIdentificacion" name="txtIdentificacion" type="text" required placeholder="Identificación del Cliente" >
              </div>
              <div class="col-md-6">
                <label class="form-label" for="txtNombre">Nombre</label>
                <input class="form-control valid validText" id="txtNombre" name="txtNombre" type="text" required placeholder="Nombre del Cliente" >
              </div>
              <div class="col-md-6">
                <label class="form-label" for="txtNegocio">Negocio</label>
                <input class="form-control valid validText" id="txtNegocio" name="txtNegocio" type="text" required placeholder="Negocio del Cliente" >
              </div>
              <div class="col-md-6">
                <label class="form-label" for="txtTelefono">Teléfono</label>
                <input type="tel" class="form-control valid validNumber" id="txtTelefono" name="txtTelefono" required onkeypress="return controlTag(event)" placeholder="Teléfono del Cliente" >
              </div>
              <div class="col-md-6">
                <label class="form-label" for="txtDireccion1">Dirección Negocio</label>
                <input class="form-control valid" id="txtDireccion1" name="txtDireccion1" type="text" required placeholder="Dirección del Negocio" >
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="txtDireccion2">Dirección Casa</label>
                <input class="form-control valid" id="txtDireccion2" name="txtDireccion2" type="text" placeholder="Dirección de la Casa" >
              </div>
              <div class="tile-footer">
                <button class="btn btn-warning" type="submit">
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

<!-- MODAL VER INFORMACIÓN DEL CLIENTE -->
<div class="modal fade" id="modalViewCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Datos del Cliente</h1>
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
                  <td>IDENTIFICACIÓN:</td>
                  <td id="celIdentificacion"></td>
                </tr>
                <tr>
                  <td>NOMBRES:</td>
                  <td id="celNombres"></td>
                </tr>
                <tr>
                  <td>NEGÓCIO:</td>
                  <td id="celApellidos"></td>
                </tr>
                <tr>
                  <td>TELÉFONO:</td>
                  <td id="celTelefono"></td>
                </tr>
                <tr>
                  <td>DIRECCIÓN NEGOCIO:</td>
                  <td id="celDireccion1"></td>
                </tr>
                <tr>
                  <td>DIRECCIÓN CASA:</td>
                  <td id="celDireccion2"></td>
                </tr>
                <tr>
                  <td>FECHA REGISTRO:</td>
                  <td id="celFechaRegistro"></td>
                </tr>
                <tr>
                  <td>PRÉSTAMOS REALIZADOS:</td>
                  <td id="celPrestamos"></td>
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

<!-- MODAL INFORMACIÓN EN LA GRÁFICA -->
<div class="modal fade" id="modalViewPersonaGrafica" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Clientes nuevos: <span id="datePersonaGrafica" class="fst-italic fw-normal"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  
        <div class="table-responsive">
          <table class="table table-striped text-center">
            <thead>
              <tr>
                <th>NOMBRE</th>
                <th>NEGOCIO</th>
              </tr>
            </thead>
            <tbody id="listgraficaPersona">
              
            </tbody>
          </table>
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