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
      <div class="tile">
        <div class="tile-body">
          <div class="row justify-content-center">
            <div class="col-6">
              <div class="card">
                <div class="card-header text-center h6">Información del Resumen</div>
                <div class="card-body">
                  <div class="row justify-content-center">
                    <div class="col-12">
                      <table class="table table-borderless text-center">
                        <tbody>
                          <tr>
                            <th class="w-50">BASE:</th>
                            <td>10</td>
                          </tr>
                          <tr>
                            <th>COBRADO:</th>
                            <td>10</td>
                          </tr>
                          <tr>
                            <th>VENTAS:</th>
                            <td>10</td>
                          </tr>
                          <tr>
                            <th>GASTOS:</th>
                            <td>10</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
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