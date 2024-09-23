<div class="modal fade modalPermisos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">Permisos Roles de Usuario</h5>
            <div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
        <div class="modal-body">
            <?php 
                //dep($data);
             ?>
            <div class="col-md-12">
              <div class="tile">
                <form action="" id="formPermisos" name="formPermisos">
                  <input type="hidden" id="idrol" name="idrol" value="<?= $data['idrol']; ?>" required="">
                  <div class="table-responsive">
                    <table class="table text-center">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Módulo</th>
                          <th>Ver</th>
                          <th>Crear</th>
                          <th>Actualizar</th>
                          <th>Eliminar</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php 
                            $no=1;
                            $modulos = $data['modulos'];
                            for ($i=0; $i < count($modulos); $i++) 
                            { 

                              $permisos = $modulos[$i]['permisos'];
                              $rCheckR = $permisos['r'] === 1 ? " checked " : "";
                              $wCheckW = $permisos['w'] === 1 ? " checked " : "";
                              $uCheckU = $permisos['u'] === 1 ? " checked " : "";
                              $dCheckD = $permisos['d'] === 1 ? " checked " : "";

                              $idmod = $modulos[$i]['idmodulo'];
                          ?>
                        <tr>
                          <td>
                            <?= $no; ?>
                            <input type="hidden" name="modulos[<?= $i; ?>][idmodulo]" value="<?= $idmod ?>" required >
                          </td>
                          <td>
                            <?= $modulos[$i]['titulo']; ?>
                          </td>
                          <td>
                            <div class="form-check form-switch d-flex justify-content-center">
                              <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][r]" <?= $rCheckR ?>>
                            </div>
                          </td>
                          <td>
                            <div class="form-check form-switch d-flex justify-content-center">
                              <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][w]" <?= $wCheckW ?>>
                            </div>
                          </td>
                          <td>
                            <div class="form-check form-switch d-flex justify-content-center">
                              <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][u]" <?= $uCheckU ?>>
                            </div>
                          </td>
                          <td>
                            <div class="form-check form-switch d-flex justify-content-center">
                              <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][d]" <?= $dCheckD ?>>
                            </div>
                          </td>
                        </tr>
                          <?php 
                              $no++;
                              }
                          ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="text-center">
                    <button class="btn btn-success" type="submit"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Guardar</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill" aria-hidden="true"></i> Salir</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
    </div>
  </div>
</div>