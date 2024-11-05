<?php
  headerAdmin($data);
  getModal('modalPrestamos',$data);
?>


<main class="app-content">
  <div class="app-title">
    <div class="mt-2 mt-lg-0">
      <h1>
        <i class="bi bi-cash-coin"></i>
        <?= $data['page_title'] ?>
      </h1>
    </div>

    
  </div>

  <!-- <button class="btn btn-danger mb-3" onclick="accion()">Acción pagos</button>
  <button class="btn btn-danger mb-3" onclick="accionPrestamos()">Acción préstamos</button>
  <button class="btn btn-danger mb-3" onclick="accionPrestamosUsuario()">Acción préstamos usuarioid</button> -->

  <!-- ALERTAS DELR ESUMEN ANTERIOR -->
  <?php
    if(!empty($data['resumenAnterior'])) {
      resumenAnterior($data);
    }
  ?>

  <!-- LISTA -->
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-striped align-middle" id="tablePrestamosFinalizados">
              <thead>
                <tr>
                  <th class="text-center">CIERRE</th>
                  <th>CLIENTE</th>
                  <th class="text-center">PAGOS</th>
                  <th class="text-center">DETALLES</th>
                </tr>
              </thead>
              <tbody class="table-group-divider" id="prestamosFinalizados">
                <?php
                  foreach ($data['prestamosFinalizados'] as $prestamo) : 
                    $formato = $prestamo['formato'] == 1 ? 'Diario' : ($prestamo['formato'] == 2 ? 'Semanal' : 'Mensual');
                    $total = $prestamo['monto'] + ($prestamo['monto'] * ($prestamo['taza'] * 0.01)); 
                    $parcela = $total / $prestamo['plazo']; 
                    $detalles = '<b>Inicio</b>:  '.date("d-m-Y", strtotime($prestamo['datecreated'])).'<br>
                                  <b>Crédito</b>: '.$prestamo['monto'].'<br>
                                  <b>Formato</b>: '.$formato.'<br>
                                  <b>Taza</b>: '.$prestamo['taza'].'%<br>
                                  <b>Parcela</b>: '.$parcela.'<br>
                                  <b>Pagado</b>: '.$total.'<br>';
                ?>

                  <tr>
                    <td><?= fechaInline(date("d-m-Y", strtotime($prestamo['datefinal']))); ?></td>
                    <td><?= nombresApellidos($prestamo['nombre'], $prestamo['negocio']); ?></td>
                    <td class="text-center">
                      <button 
                        tabindex="0" 
                        role="button" 
                        class="btn btn-link btn-sm link-warning link-underline-opacity-0 p-0  " 
                        data-bs-toggle="popover" 
                        data-bs-placement="left" 
                        data-bs-content="<?= getPagosPrestamo($prestamo['idprestamo']); ?>" 
                        title="FECHA &nbsp;<div class='vr'></div>&nbsp; PAGO &nbsp;<div class='vr'></div>&nbsp; HORA &nbsp;<div class='vr'></div>&nbsp; USUARIO">
                        <i class="bi bi-cash-stack me-0"></i>
                      </button>
                    </td>
                    <td class="text-center"><a
                          tabindex="0"
                          role="button" 
                          class="btn btn-link btn-sm link-warning link-underline-opacity-0 p-0" 
                          style="font-size: inherit;"
                          data-bs-toggle="popover" 
                          data-bs-placement="left" 
                          data-bs-content="<?= $detalles; ?>" 
                          title="DETALLES">
                          <i class="bi bi-info-circle me-0"></i>
                      </a>
                    </td>
                  </tr>

                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <button class="btn btn-warning btn-sm mt-4" onclick="fntViewDetallePrestamos()"><i class="bi bi-calendar4-week"></i>
            Buscar Préstamo por rango de fechas
          </button>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>