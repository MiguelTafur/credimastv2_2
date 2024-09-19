
    <script>
        const base_url = "<?= base_url(); ?>";
        const smoney = "<?= SMONEY; ?>";
        /*const ruta = "<?= $_SESSION['idRuta']; ?>";*/
        const mmObj = window.matchMedia("(max-width: 959px)");
        const mmObj2 = window.matchMedia("(min-width: 960px)");
    </script>

    <!-- jquery js-->
    <script src="<?= media(); ?>/js/jquery-3.7.0.min.js"></script>
    <!-- select2 js-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- datatable js-->
    <script type="text/javascript" src="<?= media(); ?>/js/plugins/datatables.min.js"></script>
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