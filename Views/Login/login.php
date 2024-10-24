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
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media();?>/css/main.css">
    <!-- style CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media();?>/css/style.css">
    <title><?= $data['page_tag']; ?></title>
  </head>
  <body >

    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content">
      <div class="logo">
        <img src="<?= media();?>/images/cm3.png" alt="CREDIMAST" width="300" height="420">
         <!-- <h1>CREDIMAST</h1> -->
      </div>
      <div class="login-box">
        <div id="divLoading">
          <div>
            <!-- <img src="<?= media(); ?>/images/loading.svg" alt="Loading"> -->
            <div class="spinner-grow"></div>
          </div>
        </div>
        <form class="login-form" id="formLogin" name="formLogin">
          <h3 class="login-head">
            <i class="bi bi-person me-2"></i>INICIAR SESIÓN
          </h3>
          <div class="mb-3">
            <label for="txtEmail" class="form-label">USUARIO</label>
            <input class="form-control valid validEmail" id="txtEmail" name="txtEmail" type="email" placeholder="Email" autocomplete="username" required autofocus>
          </div>
          <div class="mb-3">
            <label for="txtCodigo" class="form-label">CÓDIGO</label>
            <input class="form-control valid validNumber" id="txtCodigo" name="txtCodigo" type="password" placeholder="Código" required autocomplete="current-password" onkeypress="return controlTag(event)">
          </div>
          <div class="mb-3">
          <label for="txtRuta" class="form-label">RUTA</label>
          <input class="form-control" type="text" id="txtRuta" name="txtRuta" placeholder="Ruta" required>
          </div>
          <div class="mb-3 btn-container d-grid">
            <button class="btn btn-warning btn-block"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i>INICIAR SESIÓN</button>
          </div>
        </form>
      </div>
    </section>

    <script>
        const base_url = "<?= base_url(); ?>";
    </script>

    <!-- jquery js-->
    <script src="<?= media(); ?>/js/jquery-3.7.0.min.js"></script>
    <!-- sweetalert js-->
    <script type="text/javascript" src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
    <!-- main js-->
    <script src="<?= media(); ?>/js/main.js"></script>
    <!-- admin js-->
    <script src="<?= media(); ?>/js/functions_admin.js"></script>
    <!-- page js-->
    <script src="<?= media();?>/js/<?= $data['page_functions_js']; ?>"></script>

  </body>
</html>