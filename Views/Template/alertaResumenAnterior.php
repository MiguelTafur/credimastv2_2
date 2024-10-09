<div class="alert alert-warning font-monospace" role="alert">
    <i class="bi bi-exclamation-triangle"></i>
    Pendiente por finalizar el <a href="<?= base_url(); ?>/resumen" class="alert-link">Resumen</a> del dia <span class="fst-italic fw-bold"><?= date("d-m-Y", strtotime($data['resumenAnterior']['datecreated'])); ?></span>
  </div>

  <div class="alert alert-light alert-dismissible fade show font-monospace" role="alert">
    <i class="bi bi-info-circle"></i>
    Recuerda que los <strong class="fst-italic">Pagos</strong> y <strong class="fst-italic">Préstamos</strong> realizados serán registrados con la misma <strong class="fst-italic">Fecha</strong> del resumen pendiente.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>