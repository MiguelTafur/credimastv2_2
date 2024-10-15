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
                <input type="tel" class="form-control" id="txtValor" name="txtValor" placeholder="Valor" required onkeypress="return controlTag(event)">
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

<!-- MODAL INFORMACIÓN EN LA GRÁFICA -->
<div class="modal fade" id="modalViewGastoGrafica" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Gastos: <span id="dateGastoGrafica" class="fst-italic fw-normal"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  
        <div class="table-responsive">
          <table class="table table-striped text-center">
            <thead>
              <tr>
                <?php if($_SESSION['idRol'] == 1){echo '<th>REGISTRADO POR</th>';} ?>
                <th>NOMBRE</th>
                <th>VALOR</th>
                <th>HORA</th>
              </tr>
            </thead>
            <tbody id="listgraficaGasto">
              
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

<!-- Modal BUSCAR GASTOS POR RANGO DE FECHA -->
<div class="modal fade" id="modalDetalleGastos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Gastos </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaGastos" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchGastosD()"><i class="bi bi-search me-0"></i></button>
            </div>
          </div>
        </form>
        <div id="divGastosD" class="d-none">
          <table class="table">
            <thead>
            <tr class="text-center">
                <th>REGISTRADO POR</th>
                <th>DIA</th>
                <th>VALOR</th>
                <th>INFO</th>
              </tr>
            </thead>
            <tbody id="datosGastosD"></tbody>
          </table>
          <br>
          <div class="tile-footer text-end" id="divGastosD">
            <b>VALOR TOTAL: <i><mark id="markGastos"></mark></i></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>