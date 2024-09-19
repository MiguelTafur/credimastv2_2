<?php 
  headerAdmin($data);
  getModal('modalPrestamos',$data); 
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
          <i class="bi bi-person-workspace"></i> 
          <?= $data['page_title'] ?> 
          <button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();" >
              <i class="bi bi-person-plus-fill m-0"></i>
          </button>
      </h1>
    </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
    <li class="breadcrumb-item"><a href="<?= base_url(); ?>/prestamos"><?= $data['page_title'] ?></a></li>
  </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-dark align-middle" id="tablePrestamos">
              <thead>
                <tr>
                  <th>CRÉDITO</th>
                  <th>FORMATO</th>
                  <th>SALDO</th>
                  <th>CLIENTE</th>
                  <th>ABONO</th>
                  <th>DETALLES</th>
                </tr>
              </thead>
              <tbody class="table-group-divider">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>