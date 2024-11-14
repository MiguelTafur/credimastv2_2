<!-- MODAL AGREGAR, ACTUALIZAR Y RENOVAR PRÉSTAMO -->
<div class="modal fade" id="modalFormPrestamo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModal">Nueva Venta</h1>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="row" id="btnClienteNuevo">
          <div class="col-12 text-end">
            <button class="btn btn-warning btn-sm mb-3" type="button" onclick="fntNewClientePrestamo();">
              <i class="bi bi-person-plus-fill"></i>Cliente
            </button>
          </div>
        </div>
        <div class="tile">
          <div class="tile-body">
            <form class="row g-3" id="formPrestamos" name="formPrestamos">
              <input type="hidden" id="idPrestamo" name="idPrestamo" value="">
              <input type="hidden" id="renovar" name="renovar" value="">
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
              <div class="col-12">
                <label for="txtObservacion" class="form-label">Observación</label>
                <textarea class="form-control" id="txtObservacion" name="txtObservacion"></textarea>
              </div>
              <div class="col-12 form-check">
                <input class="form-check-input" type="checkbox" name="diasSemanales" id="diasSemanales">
                <label class="form-check-label" for="diasSemanales">
                  Paga 5 días semanales
                </label>
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

<!-- MDOAL AGREGAR CLIENTE -->
<div class="modal fade myModal" id="modalFormCliente" tabindex="-1" aria-hidden="true">
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
                <input class="form-control valid validText" id="txtDireccion1" name="txtDireccion1" type="text" required placeholder="Dirección del Negocio" >
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

<!-- MODAL INFORMACIÓN EN LA GRÁFICA DE PRÉSTAMOS -->
<div class="modal fade" id="modalViewPrestamoGrafica" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Préstamos: <span id="datePrestamoGrafica" class="fst-italic fw-normal"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  
        <div class="table-responsive">
          <table class="table table-striped text-center">
            <thead>
              <tr>
                <th>CLIENTE</th>
                <th>VALOR</th>
                <th>HORA</th>
                <?php if($_SESSION['idRol'] == 1){echo '<th>USUARIO</th>';} ?>
              </tr>
            </thead>
            <tbody id="listgraficaPrestamo">
              
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

<!-- MODAL INFORMACIÓN EN LA GRÁFICA DE COBRADO -->
<div class="modal fade" id="modalViewCobradoGrafica" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Cobrado: <span id="dateCobradoGrafica" class="fst-italic fw-normal"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  
        <div class="table-responsive">
          <table class="table table-striped text-center">
            <thead>
              <tr>
                <th>CLIENTE</th>
                <th>VALOR</th>
                <th>HORA</th>
                <?php if($_SESSION['idRol'] == 1){echo '<th>USUARIO</th>';} ?>
              </tr>
            </thead>
            <tbody id="listgraficaCobrado">
              
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

<!-- MODAL BUSCAR PRÉSTAMOS POR RANGO DE FECHA -->
<div class="modal fade" id="modalDetallePrestamos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Prestamos </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaPrestamos" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchPrestamosD('<?= $data['prestamo'] ?>')"><i class="bi bi-search me-0"></i></button>
            </div>
          </div>
        </form>
        <div id="divPrestamosD" class="d-none">
          <table class="table">
            <thead>
            <tr class="text-center">
              <th>DIA</th>
              <th>VALOR</th>
              <th>INFO</th>
              <?php //if($_SESSION['idRol'] == 1){echo '<th>REGISTRADO POR</th>';} ?>
              </tr>
            </thead>
            <tbody id="datosPrestamosD"></tbody>
          </table>
          <br>
          <div class="tile-footer text-end" id="divPrestamosD">
            <b>VALOR TOTAL: <i><mark id="markPrestamos"></mark></i></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL BUSCAR COBRADO POR RANGO DE FECHA -->
<div class="modal fade" id="modalDetalleCobrado" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Cobrado </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaCobrado" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchCobradoD()"><i class="bi bi-search me-0"></i></button>
            </div>
          </div>
        </form>
        <div id="divCobradoD" class="d-none">
          <table class="table">
            <thead>
            <tr class="text-center">
              <th>DIA</th>
              <th>VALOR</th>
              <th>INFO</th>
              <?php //if($_SESSION['idRol'] == 1){echo '<th>REGISTRADO POR</th>';} ?>
              </tr>
            </thead>
            <tbody id="datosCobradoD"></tbody>
          </table>
          <br>
          <div class="tile-footer text-end" id="divCobradoD">
            <b>VALOR TOTAL: <i><mark id="markCobrado"></mark></i></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>