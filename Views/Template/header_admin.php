<!DOCTYPE html>
<html lang="es">
  <head>
    <meta name="description" content="Control de usuarios y préstamos">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="author" content="MIGUEL TAFUR">
    <meta name="theme-color" content="#d9a300">
    <link rel="shortcut icon" href="<?= media();?>/images/CM.png">
    <title><?= $data['page_tag'] ?></title>
    
    <!-- datatables css-->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/datatables.min.css">
    <!-- sweetalert css-->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/sweetalert.min.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- select2 css-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/main.css">
    <!-- style CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/style.css">
  </head>
  <body class="app sidebar-mini">
    <div id="divLoading">
      <div>
        <!-- <img src="<?= media(); ?>/images/loading.svg" alt="Loading"> -->
        <div class="spinner-grow"></div>
      </div>
    </div>
    <!-- Navbar-->
    <header class="app-header">
      <a class="app-header__logo" href="<?= base_url(); ?>/prestamos">
        <img src="<?= media();?>/images/imgCM2.png" alt="CREDIMAST" class="m-2"  height="40px">
      </a>
      <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
        <!-- User Menu-->
        <li class="dropdown">
          <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
            <i class="bi bi-person fs-4"></i>
          </a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            <li>
              <a class="dropdown-item" href="<?= base_url(); ?>/logout">
                <i class="bi bi-box-arrow-right me-2 fs-5"></i> Salir
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </header>

    <?php require_once("nav_admin.php"); ?> 