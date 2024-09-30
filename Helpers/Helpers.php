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

    //Envío de correos
    function sendEmail($data,$template)
    {
        $asunto = $data['asunto'];
        $emailDestino = $data['email'];
        $empresa = NOMBRE_REMITENTE;
        $remitente = EMAIL_REMITENTE;
        //ENVÍO DE CORREO
        $de = "MIME-Version: 1.0\r\n";
        $de.= "Content-type: text/html; charset=UTF-8\r\n";
        $de.= "From: {$empresa} <{$remitente}>\r\n";
        ob_start();
        require_once("Views/Template/Email/".$template.".php");
        $mensaje = ob_get_clean();
        $send = mail($emailDestino, $asunto, $mensaje, $de);
        return $send;
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

    function sessionStart(){
        session_start();
        $inactive = 300;
        if(isset($_SESSION['timeout'])){
            $session_in = time() - $_SESSION['inicio'];
            if($session_in > $inactive){
                header("Location: ".BASE_URL."/logout");
            }
        }else{
            header("Location: ".BASE_URL."/logout");
        }
    }

    //UNIR NOMBRES Y APELLIDOS
    function nombresApellidos(string $nombre, string $apellido) 
    {
        $cliente = '<strong>'.strtok(strtoupper($nombre), " ").'</strong> <i>'.$apellido.'</i>';
        return $cliente;
    }

    //CALCULA EL TOTAL DEL PRESTAMO
    function valorTotalPrestamo(int $idprestamo)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectPrestamo($idprestamo);
        $total = $request['monto'] + ($request['monto'] * ($request['taza'] * 0.01));
        return $total;
    }

    //CALCULA EL SALDO DEL PRÉSTAMO
    function saldoPrestamo(int $idprestamo)
    {
        $pagamentos = sumaPagamentosPrestamos($idprestamo);
        $totalPrestamo = valorTotalPrestamo($idprestamo);
        $saldo = $totalPrestamo - $pagamentos;
        return $saldo;
    }

    //VERIFICA SE HAY UN RESUMEN CON LA FECHA ACTUAL
    function getResumenActual(int $ruta)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->selectResumenActual($ruta);
        return $request;
    }

    //INSERTA EL RESUMEN
    function setResumen(int $idpersona)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->insertResumen($idpersona);
        return $request;
    }

    //ACTUALIZA EL RESUMEN
    function setUpdateResumen(int $idpersona, $valor, int $tipo)
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->updateResumen($idpersona, $valor, $tipo);
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

    //TRAE LA SUMA DE TODOS LOS PRÉSTAMO
    function sumaPrestamos(int $idruta)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->sumaPrestamos($idruta);
        return $request;
    }

    //TRAE LA SUMA DE TODOS LOS PAGAMENTOS DEPENDIENDO DEL PRÉSTAMO
    function sumaPagamentosPrestamos(int $idprestamo)
    {
        require_once("Models/PagosModel.php");
        $objPrestamos = new PagosModel();
        $request = $objPrestamos->sumaPagamentos($idprestamo);
        return $request['sumaPagos'];
    }

    //TRAE UN ARRAY CON TODOS LOS PAGAMENTOS DEPENDIENDO DEL PRÉSTAMO
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