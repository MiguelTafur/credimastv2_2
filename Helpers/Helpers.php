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

    function resumenOk($data="")
    {
        $view_resumen = "Views/Template/alertaResumenOk.php";
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
    //SI HAY MAS DE UNA BASE, TRAE LOS DATOS
    function getBaseActualAnterior(string $fecha = NULL)
    {
        require_once("Models/BaseModel.php");
        $objBase = new BaseModel();
        $request = $objBase->selectBaseActualAnterior($_SESSION['idRuta'], $fecha);

        if(COUNT($request) > 1)
        {   
            for ($i=0; $i < COUNT($request) ; $i++) { 
                $baseAnterior = $request[0]['monto'];
                $usuarioAnterior = $request[0]['personaid'];
                $horaAnterior = date('H:i', strtotime($request[0]['hora']));
                $baseActual = $request[1]['monto'];
                $usuarioActual = $request[1]['personaid'];
                $horaActual = date('H:i', strtotime($request[1]['hora']));
                $idBaseActual = $request[1]['idbase'];
            }
    
            $request = array('anterior' => $baseAnterior,
                             'usuarioAnterior' => $usuarioAnterior,
                             'horaAnterior' => $horaAnterior, 
                             'actual' => $baseActual, 
                             'idBaseActual' => $idBaseActual,
                             'usuarioActual' => $usuarioActual,
                             'horaActual' => $horaActual);
    
            return $request;
        } else {
            return 0;
        }
    }

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

    //TRAE EL ÚLTIMO RESUMEN REGISTRADO
    function getResumenAnterior1()
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->selectResumenUltimo($_SESSION['idRuta']);
        return $request;
    }

    //TRAE LOS ÚLTIMOS RESUMENES Y DEVUELVE UN STRING
    function getUltimosResumenes()
    {
        require_once("Models/ResumenModel.php");
        $objResumen = new ResumenModel();
        $request = $objResumen->selectUltimosResumen($_SESSION['idRuta']);
        $resumenes = '';

        foreach ($request as $resumen) 
        {
            $getCobrado = getFormatCobrado($resumen['datecreated']);
            $getVentas = getFormatPrestamos($resumen['datecreated']);
            $getGastos = getFormatGastos($resumen['datecreated']);
            $hora = $resumen['hora'] != NULL ? date("H:i", strtotime($resumen['hora'])) : " <i class='bi bi-watch'></i>";
           
            $basePopover = getBaseActualAnterior($resumen['datecreated']) == 0 
											? $resumen['base']
                                    	  	: '<a
                                             tabindex="0"
                                             role="button" 
                                             class="btn btn-link btn-sm link-warning link-underline-opacity-0 p-0" 
                                             style="font-size: inherit;"
                                             data-bs-toggle="popover" 
                                             data-bs-placement="left" 
                                             data-bs-content=" BASE '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO '."<hr class='my-2'>".'
											 	Anterior: '.getBaseActualAnterior($resumen['datecreated'])['anterior']. 
												"  &nbsp;<div class='vr'></div>&nbsp;" . ' '.
												getBaseActualAnterior($resumen['datecreated'])['horaAnterior']. 
												"  &nbsp;<div class='vr'></div>&nbsp;".'
												'.getBaseActualAnterior($resumen['datecreated'])['usuarioAnterior'].' <br>
												Actual: '.getBaseActualAnterior($resumen['datecreated'])['actual'].
												"  &nbsp;<div class='vr'></div>&nbsp;" . ' '.
												getBaseActualAnterior($resumen['datecreated'])['horaActual'].
												"  &nbsp;<div class='vr'></div>&nbsp;" . ' 
												'.getBaseActualAnterior($resumen['datecreated'])['usuarioActual'].'" 
                                             title="BASE MODIFICADA">
                                             ' . getBaseActualAnterior($resumen['datecreated'])['actual'] . ' 
                                             </a>';

            $cobrado = $resumen['cobrado'] == 0 ? round($resumen['cobrado'], 0) 
                                                : '<a
                                                    tabindex="0"
                                                    role="button" 
                                                    class="btn btn-link btn-sm link-warning link-underline-opacity-0 p-0" 
                                                    style="font-size: inherit;"
                                                    data-bs-toggle="popover" 
                                                    data-bs-placement="left" 
                                                    data-bs-content="'.$getCobrado.'" 
                                                    title="COBRADO '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO '.'">
                                                    '.round($resumen['cobrado'], 0).'
                                                    </a>';
            $ventas = $resumen['ventas'] == 0 ? round($resumen['ventas'], 0) 
                                              : '<a
                                                 tabindex="0"
                                                 role="button" 
                                                 class="btn btn-link btn-sm link-warning link-underline-opacity-0 p-0" 
                                                 style="font-size: inherit;"
                                                 data-bs-toggle="popover" 
                                                 data-bs-placement="left" 
                                                 data-bs-content="'.$getVentas.'" 
                                                 title="VENTA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO '.'">
                                                 '.round($resumen['ventas'], 0).'
                                                 </a>';
            $gastos = $resumen['gastos'] == 0 ? round($resumen['gastos'], 0)
                                              : '<a
                                                 tabindex="0"
                                                 role="button" 
                                                 class="btn btn-link btn-sm link-warning link-underline-opacity-0 p-0" 
                                                 style="font-size: inherit;" 
                                                 data-bs-toggle="popover" 
                                                 data-bs-placement="left" 
                                                 data-bs-content="'.$getGastos.'" 
                                                 title="GASTO '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO '.'">
                                                 '.round($resumen['gastos'], 0).'
                                                 </a>';
            $resumenes .= '<tr>';
            $resumenes .= '<td>';
            $resumenes .= fechaInline(date("d-m-Y", strtotime($resumen['datecreated'])));
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= $basePopover;
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= $cobrado;
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= '<span>'.$ventas.'</span>';
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= '<span>'.$gastos.'</span>';
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= '<span>'.$resumen['total'].'</span>';
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= '<span>'.$hora.'</span>';
            $resumenes .= '</td>';
            $resumenes .= '<td>';
            $resumenes .= '<span class="fst-italic fw-normal">'.$resumen['personaid'].'</span>';
            $resumenes .= '</td>';
            $resumenes .= '</tr>';   
        }

        return $resumenes;
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
    /**** FIN RESUMEN ****/

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

    //CALCULA EL SALDO DEL PRÉSTAMO
    function saldoPrestamo(int $idprestamo)
    {
        $pagamentos = sumaPagamentosPrestamos($idprestamo);
        $totalPrestamo = valorTotalPrestamo($idprestamo);
        $saldo = $totalPrestamo - $pagamentos;
        return $saldo;
    }

    //TRAE EL CLIENTE Y EL MONTO DE LOS PRÉSTAMOS DEPENDIENDO DE LA FECHA
    //DEVUELVE UN STRING CON EL NOMBRE Y EL MONTO
    function getFormatPrestamos(string $fecha, string $tipo = NULL)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $ruta = $_SESSION['idRuta'];
        $request = $objPrestamos->selectPrestamosFecha($ruta, $fecha, $tipo);
        if(is_array($request))
        {
            $prestamo = "";
            for ($i=0; $i < count($request); $i++) {
                $hora = $request[$i]['hora'] != NULL ? date('H:i', strtotime($request[$i]['hora'])): " <i class='bi bi-watch'></i>";
                $prestamo .= nombresApellidos($request[$i]['nombres'], $request[$i]['apellidos']) . ': ' . $request[$i]['monto'] . "  &nbsp;<div class='vr'></div>&nbsp;  " . $hora . "  &nbsp;<div class='vr'></div>&nbsp;  <i>" .  $request[$i]['usuario'] . '</i><br>';
            }
            return $prestamo;
        }
    }

    //CALCULA EL VALOR ACTIVO Y EL COBRADO ESTIMADO
    function valorActivoYEstimadoPrstamos()
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $ruta = $_SESSION['idRuta'];
        $request = $objPrestamos->selectPrestamosFecha($ruta);

        $sumaPrestamos = 0;
        $sumaParcelas = 0;
        if(!empty($request)){
            foreach ($request as $prestamo) {
                $totalPrestamo = $prestamo['monto'] + ($prestamo['monto'] * ($prestamo['taza'] * 0.01));
                $sumaPrestamos += $totalPrestamo;
                if($prestamo['formato'] == 1) {
                $sumaParcelas += $totalPrestamo / $prestamo['plazo'];
                }
            } 
        }

        $pagamentos = totalPagamentosPrestamos($ruta);
        $sumaPrestamos -= $pagamentos;

        $arrData = array('valorActivo' => $sumaPrestamos, 'cobradoEstimado' => round($sumaParcelas, 0, PHP_ROUND_HALF_UP));

        return $arrData;
    }

    /**** PAGOS ****/
    //TRAE LA SUMA DE TODOS LOS PAGOS DEL PRÉSTAMO
    function sumaPagamentosPrestamos(int $idprestamo)
    {
        require_once("Models/PagosModel.php");
        $objPrestamos = new PagosModel();
        $request = $objPrestamos->sumaPagamentos($idprestamo);
        return $request['sumaPagos'];
    }

    //TRAE LA SUMA DE TODOS LOS PAGOS DE LOS PRÉSTAMOS
    function totalPagamentosPrestamos(int $ruta)
    {
        require_once("Models/PagosModel.php");
        $objPrestamos = new PagosModel();
        $request = $objPrestamos->sumaPagamentos2(NULL, $ruta);
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

    //TRAE UN ARRAY CON TODOS LOS PAGOS DE LOS PRÉSTAMOS CON UNA FECHA ESPECÍFICA Y DEVUELVE UN STRING
    function getFormatCobrado(string $fecha)
    {
        require_once("Models/PagosModel.php");
        $ruta = $_SESSION['idRuta'];
        $objPagos = new PagosModel();
        $request = $objPagos->selectPagamentosFecha($fecha, $ruta);

        $pagos = "";
        for ($i=0; $i < count($request); $i++) {
            $hora = $request[$i]['hora'] != NULL ? date('H:i', strtotime($request[$i]['hora'])) : " <i class='bi bi-watch'></i>";
            $pagos .= nombresApellidos($request[$i]['nombres'], $request[$i]['apellidos']) . ': ' . $request[$i]['abono'] . "  &nbsp;<div class='vr'></div>&nbsp;  " . $hora . "  &nbsp;<div class='vr'></div>&nbsp;  <i>" . $request[$i]['usuario'] . '</i><br>';
        }
        return $pagos;
    }

    //TRAE UN ARRAY CON TODOS LOS PAGOS DEL PRÉSTAMO
    function getPagosPrestamo(int $idprestamo)
    {
        require_once("Models/PagosModel.php");
        $ruta = $_SESSION['idRuta'];
        $objPagos = new PagosModel();
        $request = $objPagos->selectPagamentos($idprestamo);
        $cobrado = '';
        
        foreach ($request as $pago) {
            $hora = $pago['hora'] != NULL ? date("H:i", strtotime($pago['hora'])) : " <i class='bi bi-watch'></i>";

            $cobrado .= date("d-m-Y", strtotime($pago['datecreated'])) . "&nbsp;&nbsp;<div class='vr'></div>&nbsp;&nbsp;" .  
                        $pago['abono'] . "&nbsp;&nbsp;<div class='vr'></div>&nbsp;&nbsp;" .
                        $hora . "&nbsp;&nbsp;<div class='vr'></div>&nbsp;&nbsp;" .
                        $pago['personaid'] .
                        '<br>';     
        }

        return $cobrado;
    }

    /**** GASTOS ****/
    //TRAE EL NOMBRE Y EL MONTO DE LOS GASTOS DEPENDIENDO DE LA FECHA
    function getFormatGastos(string $fecha)
    {
        require_once("Models/GastosModel.php");
        $objGastos = new GastosModel();
        $ruta = $_SESSION['idRuta'];
        $request = $objGastos->selectGastosFecha($ruta, $fecha);
        if(is_array($request))
        {
            $gasto = "";
            for ($i=0; $i < count($request); $i++) {
                $hora = $request[$i]['hora'] != NULL ? date('H:i', strtotime($request[$i]['hora'])) . "  &nbsp;<div class='vr'></div>&nbsp;  " : " <i class='bi bi-watch'></i>  &nbsp;<div class='vr'></div>&nbsp;  ";
                $gasto .= strtoupper($request[$i]['nombre']) . ': ' . $request[$i]['monto'] . "  &nbsp;<div class='vr'></div>&nbsp;  <i>" .$hora . $request[$i]['usuario'] . '</i><br>';
            }
            return $gasto;
        }
    }

    /**** BASE ****/
    //TRAE LA BASE SI ESTÁ CREADA
    function getBase(string $fecha = NULL)
    {
        require_once("Models/BaseModel.php");
        $objBase = new BaseModel();
        $request = $objBase->selectBaseActualAnterior($_SESSION['idRuta'], $fecha);
        return $request;
    }

    //INSERTA LA BASE
    function setBase(int $monto)
    {
        require_once("Models/BaseModel.php");
        $objBase = new BaseModel();
        $request = $objBase->insertBase($_SESSION['idUser'], $_SESSION['idRuta'], $monto, NOWDATE);
        return $request;
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