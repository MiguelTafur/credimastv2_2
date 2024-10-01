<?php 
  headerAdmin($data);
  getModal('modalResumen',$data); 
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="bi bi-file-earmark-diff"></i> 
        <?= $data['page_title'] ?> 
      </h1>
    </div>
    <ul class="nav nav-underline" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="resumen-tab" data-bs-toggle="tab" data-bs-target="#resumen-tab-pane" type="button" role="tab" aria-controls="resumen-tab-pane" aria-selected="true">RESUMEN</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-tab-pane" type="button" role="tab" aria-controls="dashboard-tab-pane" aria-selected="false">DASHBOARD</button>
      </li>
    </ul>
  </div>

  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="resumen-tab-pane" role="tabpanel" aria-labelledby="resumen-tab" tabindex="0">
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="dashboard-tab-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
      
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>