<?php 
  headerAdmin($data);
  getModal('modalRutas',$data); 
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
				<i class="bi bi-buildings"></i> 
				<?= $data['page_title'] ?> 
				<?php if($_SESSION['permisosMod']['w']){ ?>
				<button class="btn btn-warning btn-sm ms-1" type="button" onclick="openModal();" >
						<i class="bi bi-building-fill-add m-0"></i>
				</button>
				<?php } ?>
      </h1>
    </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
    <li class="breadcrumb-item"><a href="<?= base_url(); ?>/rutas"><?= $data['page_title'] ?></a></li>
  </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-dark align-middle" id="tableRutas">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Código</th>
                  <th>Dia Pagamento</th>
                  <th>Acciones</th>
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