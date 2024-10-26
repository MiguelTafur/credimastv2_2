    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar" src="<?= media();?>/images/avatar.png" alt="Usuario">
        <div>
          <p class="app-sidebar__user-name"><?= $_SESSION['userData']['nombres']; ?></p>
          <p class="app-sidebar__user-designation">Ruta: <i><b><?= $_SESSION['ruta']; ?></b></i></p>
          <p class="app-sidebar__user-designation">Moneda: <i><b><?= 'BRL'.' ('.SMONEY.')';  ?></b></i></p>
        </div>
      </div>
      <ul class="app-menu">
        <!-- ADMIN -->
        <?php if(!empty($_SESSION['permisos'][MUSUARIOS]['r']) AND $_SESSION['idUser'] == 1) : ?>
        <li class="treeview">
          <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon bi bi-person-workspace"></i>
            <span class="app-menu__label">Admin</span>
            <i class="treeview-indicator bi bi-chevron-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a class="treeview-item" href="<?= base_url(); ?>/usuarios">
                <i class="icon bi bi-circle-fill"></i> Usuarios
              </a>
            </li>
            <li>
              <a class="treeview-item" href="<?= base_url(); ?>/roles">
                <i class="icon bi bi-circle-fill"></i> Roles
              </a>
            </li>
            <li>
              <a class="treeview-item" href="<?= base_url(); ?>/rutas">
                <i class="icon bi bi-circle-fill"></i> Rutas
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>
        <!-- CLIENTES -->
        <?php if(!empty($_SESSION['permisos'][MCLIENTES]['r'])) : ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/clientes">
            <i class="app-menu__icon bi bi-people-fill"></i>
            <span class="app-menu__label">Clientes</span>
          </a>
        </li>
        <?php endif; ?>
        <!-- PRESTAMOS -->
        <?php if(!empty($_SESSION['permisos'][MPRESTAMOS]['r'])) : ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/prestamos">
            <i class="app-menu__icon bi bi-cash-coin"></i>
            <span class="app-menu__label">Préstamos</span>
          </a>
        </li>
        <?php endif; ?>
        <!-- VENTAS -->
        <?php if(!empty($_SESSION['permisos'][MPRESTAMOS]['r'])) : ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/gastos">
            <i class="app-menu__icon bi bi-clipboard2-pulse"></i>
            <span class="app-menu__label">Gastos</span>
          </a>
        </li>
        <?php endif; ?>
        <!-- RESUMEN -->
        <?php if(!empty($_SESSION['permisos'][MRESUMEN]['r'])) : ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/resumen">
            <i class="app-menu__icon bi bi-file-earmark-diff"></i>
            <span class="app-menu__label">Resumen</span>
          </a>
        </li>
        <?php endif; ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/logout">
            <i class="app-menu__icon bi bi-box-arrow-right"></i>
            <span class="app-menu__label">Cerrar Sesión</span>
          </a>
        </li>
      </ul>

      <ul class="app-menu">
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/ajuda">
            <i class="app-menu__icon bi bi-info-circle"></i>
            <span class="app-menu__label">Ajuda</span>
          </a>
        </li>
      </ul>
    </aside>