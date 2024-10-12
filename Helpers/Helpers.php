<?php 

	//Retorla la url del proyecto
	function base_url()
	{
		return BASE_URL;
	}
    //Retorla la url de Assets
    function media()
    {
        return BASE_URL."/Assets";
    }
    function headerAdmin($data="")
    {
        $view_header = "Views/Template/header_admin.php";
        require_once ($view_header);
    }
    function footerAdmin($data="")
    {
        $view_footer = "Views/Template/footer_admin.php";
        require_once ($view_footer);        
    }
    function resumenAnterior($data="")
    {
        $view_resumen = "Views/Template/alertaResumenAnterior.php";
        require_once ($view_resumen);        
    }

    function alertaActualizacion($data="")
    {
        $view_resumen = "Views/Template/alertaActualizacion.php";
        require_once ($view_resumen);        
    }
	//Muestra información formateada
	function dep($data)
    {
        $format  = print_r('<pre>');
        $format .= print_r($data);
        $format .= print_r('</pre>');
        return $format;
    }
    function getModal(string $nameModal, $data)
    {
        $view_modal = "Views/Template/Modals/{$nameModal}.php";
        require_once $view_modal;        
    }

    function getFile(string $url, $data)
    {
        ob_start();
        require_once("Views/{$url}.php");
        $file = ob_get_clean();
        return $file;
    }

    function getPermisos(int $idmodulo)
    {
        require_once("Models/PermisosModel.php");
        $objPermisos = new PermisosModel();
        $idrol = $_SESSION['userData']['idrol'];
        $arrPermisos = $objPermisos->permisosModulo($idrol);
        $permisos = '';
        $permisosMod = '';

        if(count($arrPermisos) > 0){
            $permisos = $arrPermisos;
            $permisosMod = isset($arrPermisos[$idmodulo]) ? $arrPermisos[$idmodulo] : "";
        }
        $_SESSION['permisos'] = $permisos;
        $_SESSION['permisosMod'] = $permisosMod;
    }

    function sessionUser(int $idpersona){
        require_once ("Models/LoginModel.php");
        $objLogin = new LoginModel();
        $request = $objLogin->sessionLogin($idpersona);
        return $request;
    }

    //UNIR NOMBRES Y APELLIDOS
    function nombresApellidos(string $nombre, string $apellido) 
    {
        $cliente = '<strong>'.strtok(strtoupper($nombre), " ").'</strong> <i>'.$apellido.'</i>';
        return $cliente;
    }

    /**** RESUMEN ****/
    //TRAE EL RESUMEN CON EL ESTADO 0 Y CON LA FECHA ACTUAL DIFERENTE
    function getResumenAnterior()
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->selectResumenAnterior($_SESSION['idRuta']);
        return $request;
    }

    //TRAE EL RESUMEN CON EL ESTADO 0 Y CON LA FECHA ACTUAL
    function getResumenActual(string $fecha = NULL)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->selectResumenActual($_SESSION['idRuta'], $fecha);
        return $request;
    }

    //TRAE EL RESUMEN CON EL ESTADO 1 Y CON LA FECHA ACTUAL
    function getResumenActual1()
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->selectResumenActual1($_SESSION['idRuta']);
        return $request;
    }

    //INSERTA O ELIMINA EL RESUMEN
    function setDelResumenActual(string $tipo, int $ruta)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();

        //VERIFICA SI HAY UN RESUMEN ANTERIOR CREADO
        $request = $objResumen->selectResumenAnterior($ruta);

        //SI HAY UN RESUMEN GUARDA LA FECHA EN LA VARIABLE
        $request = $request['datecreated'] ?? NULL;

        //TRAE EL RESUMEN CON FECHA DETERMINADA
        $request = $objResumen->selectResumenActual($ruta, $request);
        if($tipo == 'set')
        {
            if(empty($request))
            {
                //INSERTA EL RESUMEN
                $estadoResumen = getResumenActual1($ruta)['status'] ?? 0;
                if($estadoResumen == 0){
                    setResumen($_SESSION['idUser'], $ruta);
                    return true;
                }
            } else {
                return $request;
            }
        } else if($tipo == 'del') {
            // VERIFICA SI LA BASE, EL COBRADO, LAS VENTAS Y LOS GASTOS ESTÁN VACÍOS
            if($request['base'] == NULL AND $request['cobrado'] == NULL AND $request['ventas'] == NULL AND $request['gastos'] == NULL)
            {
                //ELIMINA EL RESUMEN
                deleteResumenActual($request['idresumen']);
                return $request['datecreated'];
            } else {
                return $request;
            }
        }
    }

    //INSERTA EL RESUMEN
    function setResumen(int $idpersona, int $ruta)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->insertResumen($idpersona, $ruta);
        return $request;
    }

    //ACTUALIZA EL RESUMEN
    function setUpdateResumen(int $ruta, $valor, int $tipo, string $fecha)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->updateResumen($ruta, $valor, $tipo, $fecha);
        return $request;
    }

    //ELIMINA EL RESUMEN
    function deleteResumenActual($idresumen)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->deleteResumen($idresumen);
        return $request;
    }

    /**** PRESTAMOS ****/
    //CALCULA EL TOTAL DEL PRESTAMO
    function valorTotalPrestamo(int $idprestamo)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectPrestamo($idprestamo);
        $total = $request['monto'] + ($request['monto'] * ($request['taza'] * 0.01));
        return $total;
    }

    //TRAE LA SUMA DE TODOS LOS PRÉSTAMOS
    /*function sumaPrestamos(int $idruta)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->sumaPrestamos($idruta);
        return $request;
    }*/

    //CALCULA EL SALDO DEL PRÉSTAMO
    function saldoPrestamo(int $idprestamo)
    {
        $pagamentos = sumaPagamentosPrestamos($idprestamo);
        $totalPrestamo = valorTotalPrestamo($idprestamo);
        $saldo = $totalPrestamo - $pagamentos;
        return $saldo;
    }

    /**** PAGOS ****/
    //TRAE LA SUMA DE TODOS LOS PAGAMENTOS DEL PRÉSTAMO
    function sumaPagamentosPrestamos(int $idprestamo)
    {
        require_once("Models/PagosModel.php");
        $objPrestamos = new PagosModel();
        $request = $objPrestamos->sumaPagamentos($idprestamo);
        return $request['sumaPagos'];
    }

    //TRAE UN ARRAY CON EL ÚLTIMO PAGAMENTO DEL PRÉSTAMO
    function getUltimoPagamento(int $idprestamo)
    {
        require_once("Models/PagosModel.php");
        $objPrestamos = new PagosModel();
        $request = $objPrestamos->selectUltimoPagamento($idprestamo);
        $fechaPago = '';
        $idPago = '';
        $abono = '';
        if(!empty($request)) {
            $fechaPago = $request['datecreated'];
            $idPago = $request['idpago'];
            $abono = $request['abono'];
        }
        return $fechaPago.'|'.$idPago.'|'.$abono;
    }

    //Fecha formateada en linea recta
    function fechaInline(string $fecha) 
    {
        $fechaFormateada = explode("-", $fecha);
        $fechaFormateada = '<div class="d-flex justify-content-center">'.'<div>'.$fechaFormateada[0].'</div>-<div>'.$fechaFormateada[1].'</div>-<div>'.$fechaFormateada[2].'</div></div>';

        return $fechaFormateada;
    }

    //Elimina exceso de espacios entre palabras
    function strClean($strCadena){
        $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena);
        $string = trim($string); //Elimina espacios en blanco al inicio y al final
        $string = stripslashes($string); // Elimina las \ invertidas
        $string = str_ireplace("<script>","",$string);
        $string = str_ireplace("</script>","",$string);
        $string = str_ireplace("<script src>","",$string);
        $string = str_ireplace("<script type=>","",$string);
        $string = str_ireplace("SELECT * FROM","",$string);
        $string = str_ireplace("DELETE FROM","",$string);
        $string = str_ireplace("INSERT INTO","",$string);
        $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
        $string = str_ireplace("DROP TABLE","",$string);
        $string = str_ireplace("OR '1'='1","",$string);
        $string = str_ireplace('OR "1"="1"',"",$string);
        $string = str_ireplace('OR ´1´=´1´',"",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("LIKE '","",$string);
        $string = str_ireplace('LIKE "',"",$string);
        $string = str_ireplace("LIKE ´","",$string);
        $string = str_ireplace("OR 'a'='a","",$string);
        $string = str_ireplace('OR "a"="a',"",$string);
        $string = str_ireplace("OR ´a´=´a","",$string);
        $string = str_ireplace("OR ´a´=´a","",$string);
        $string = str_ireplace("--","",$string);
        $string = str_ireplace("^","",$string);
        $string = str_ireplace("[","",$string);
        $string = str_ireplace("]","",$string);
        $string = str_ireplace("==","",$string);
        return $string;
    }

    function Meses()
    {
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $meses;
    }