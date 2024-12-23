<?php 

class Prestamos extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

	public function Prestamos()
	{
		$data['page_tag'] = "Prestamos";
		$data['page_title'] = "PRESTAMOS";
		$data['page_name'] = "prestamos";
		$data['page_functions_js'] = "functions_prestamos.js";

		/*** GRÁFICAS VENTAS ***/ 
		$anio = date("Y");
		$mes = date("m");
		//MENSUAL
		$data['prestamosMDia'] = $this->model->selectPrestamosMes($anio,$mes);
		//ANUAL
		$data['prestamosAnio'] = $this->model->selectPrestamosAnio($anio);

		/*** GRÁFICAS COBRADO ***/
		//MENSUAL
		$data['cobradoMDia'] = $this->model->selectCobradoMes($anio,$mes);
		//ANUAL
		$data['cobradoAnio'] = $this->model->selectCobradoAnio($anio);
		
		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0
		$data['resumenAnterior'] = getResumenAnterior();
		$data['prestamo'] = 'activo';
		
		$this->views->getView($this,"prestamos",$data);
	}

	//TRAE TODOS LOS PRÉSTASMOS
	public function getPrestamos()
	{
		if($_SESSION['permisosMod']['r'])
		{
			$arrData = $this->model->selectPrestamos($_SESSION['idRuta']);

			$resumenAnterior = getResumenAnterior();
			$fecha = $resumenAnterior['datecreated'] ?? NOWDATE;
			$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

			for ($i=0; $i < count($arrData); $i++) {
				
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				$icono = '<div class="d-none" id="div-'.$arrData[$i]['idprestamo'].'"><i class="bi bi-circle-fill"></i></div>';

				//JUNTANDO EL NOMBRE Y EL NEGOCIO DEL CLIENTE
				$arrData[$i]['cliente'] = '<div class="d-flex gap-1">'.nombresApellidos($arrData[$i]['nombres'], $arrData[$i]['apellidos']) . ' ' . $icono . '</div>';

				//CREANDO Y ASIGNANDO A UNA VARIABLE EL PLAZO
				$arrData[$i]['intPlazo'] = $arrData[$i]['plazo'];

				//PAGADO
				$arrData[$i]['pagado'] = sumaPagamentosPrestamos($arrData[$i]['idprestamo']);

				//SALDO
				$arrData[$i]['saldo'] = saldoPrestamo($arrData[$i]['idprestamo']);

				//FORMATEANDO FECHA
				if($arrData[$i]['hora']){
					$arrData[$i]['datecreatedFormat'] = date("d/m/Y", strtotime($arrData[$i]['datecreated'])) . ' - ' . date("H:i", strtotime($arrData[$i]['hora']));	
				} else {
					$arrData[$i]['datecreatedFormat'] = date("d/m/Y", strtotime($arrData[$i]['datecreated']));
				}
				$arrData[$i]['fechavenceFormat'] = date("d/m/Y", strtotime($arrData[$i]['fechavence']));

				//CALCULANDO LOS ÚLTIMOS 4 DIAS ANTES DE VENCER EL PRÉSTRAMO
				if($arrData[$i]['fechavence'] != NULL)
				{
					$diasVencimiento4 = date("Y-m-d", strtotime('-4 day', strtotime($arrData[$i]['fechavence'])));
					$diasVencimiento3 = date("Y-m-d", strtotime('-3 day', strtotime($arrData[$i]['fechavence'])));
					$diasVencimiento2 = date("Y-m-d", strtotime('-2 day', strtotime($arrData[$i]['fechavence'])));
					$diasVencimiento1 = date("Y-m-d", strtotime('-1 day', strtotime($arrData[$i]['fechavence'])));
					
					if($diasVencimiento4 == NOWDATE || 
					$diasVencimiento3 == NOWDATE || 
					$diasVencimiento2 == NOWDATE || 
					$diasVencimiento1 == NOWDATE || 
					$arrData[$i]['fechavence'] == NOWDATE)
					{
					$arrData[$i]['diasVence'] = false;
					}else if($arrData[$i]['fechavence'] < NOWDATE)
					{
					$arrData[$i]['diasVence'] = "vencido";
					}else{
					$arrData[$i]['diasVence'] = true;
					}
				}

				//CALCULANDO LA PARCELA DEL PRESTAMO
				$parcela = $arrData[$i]['monto'] + ($arrData[$i]['monto'] * ($arrData[$i]['taza'] * 0.01));
				$parcela = $parcela / $arrData[$i]['plazo'];

				//CALCULANDO LAS PARCELAS PENDIENTES Y CANCELADAS


				$arrData[$i]['pendiente'] = round(($arrData[$i]['saldo']/$parcela), 0, PHP_ROUND_HALF_UP);
				$arrData[$i]['cancelado'] = round(($arrData[$i]['pagado']/$parcela), 0, PHP_ROUND_HALF_DOWN);

				/*** FORMATO ***/
				// DIARIO
				if($arrData[$i]['formato'] == 1)
				{
					$arrData[$i]['formato'] = 'Diário';
					if($arrData[$i]['plazo'] == 1)
					{
						$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Día';
					}else
					{
						$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Días';
					}
				}
				
				// SEMANAL
				//$diaPagamento = $dias[date("w", strtotime($arrData[$i]['datecreated']))];
				if($arrData[$i]['formato'] == 2)
				{
					//$arrData[$i]['formato'] = '<h6>Semanal <span class="badge text-bg-secondary">'.$diaPagamento.'</span></h6>';
					if($arrData[$i]['plazo'] == 1){
						$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Semana';
					}else
					{
						$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Semanas';
					}
					
					$arrData[$i]['diaPago'] = $dias[date("w", strtotime($arrData[$i]['datecreated']))];
				}

				// MENSUAL
				if($arrData[$i]['formato'] == 3)
				{
					$arrData[$i]['formato'] = 'Mensual';
					if($arrData[$i]['plazo'] == 1)
					{
						$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Mes';
					}else
					{
						$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Meses';
					}
				}

				//TRAYENDO TODOS LOS PAGAMENTOS
				$arrPagos = getUltimoPagamento($arrData[$i]['idprestamo']);
				$arrPagos = explode("|", $arrPagos);

				/**** MOSTRANDO LOS BOTONES SEGÚN LOS PARÁMETROS ****/
				//BOTÓN DE ELIMINAR
				if($arrPagos[0] == $fecha )
				{
					$btnAbono = '<div class="d-grid gap-2 d-block">
							<button class="btn btn-success btn-sm btn-block" onclick="fntDelInfoPago('.$arrPagos[1].', '.$arrData[$i]['idprestamo'].')" title="Eliminar pago">
							<p class="m-0 fs-6 font-monospace">'.$arrPagos[2].'</p>
							</button>
						</div>';
					//BOTÓN DE RENOVAR ELIMINAR						
					if($arrData[$i]['saldo'] == 0)
					{
						$btnAbono = '<div class="d-flex gap-2">
								<button class="btn btn-warning btn-sm" onclick="fntRenovar('.$arrData[$i]['idprestamo'].')" title="Eliminar pago">
									RENOVAR
								</button>
								<button class="btn btn-success btn-sm" onclick="fntDelInfoPago('.$arrPagos[1].', '.$arrData[$i]['idprestamo'].')" title="Eliminar pago">
									<p class="m-0 fs-6 font-monospace">'.$arrPagos[2].'</p>
								</button>
							</div>
						';	
					}
				//INPUT DEL PAGAMENTO
				} else {
					$btnAbono = '
						<div class="input-group">
							<input type="tel" class="form-control valid validNumber inputPago" name="txtPagoPrestamo" id="txtPagoPrestamo-'.$arrData[$i]['idprestamo'].'" placeholder="'.$parcela.'" aria-label="100" aria-describedby="button-addon2" onkeypress="return controlTag(event)">
							<button type="submit" class="btn btn-warning btn-sm" type="button" id="button-addon2" onclick="fntNewPagoPrestamo('.$arrData[$i]['idprestamo'].')">Pagar</button>
						</div>';
				}

				$arrData[$i]['pagamento'] = '
							<form onsubmit="return false;">
								<div style="width: 130px">
								'.$btnAbono.'	
								</div>
							</form>';

				//BOTONES DE ACCIÓN
				if($_SESSION['permisosMod']['r']){
					$btnView = '<button class="btn btn-secondary btn-sm" onClick="fntViewPagamentos('.$arrData[$i]['idprestamo'].')" title="Ver Pagamentos" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="bi bi-cash-stack me-0"></i></button>';
				}
				if($_SESSION['permisosMod']['u'])
				{
					if($arrData[$i]['datecreated'] == $fecha)
					{
						$btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditInfo('.$arrData[$i]['idprestamo'].', 2)" title="Editar Préstamo"><i class="bi bi-pencil-square me-0"></i></button>';
					}
				}
				if($_SESSION['permisosMod']['d'])
				{
					if($arrData[$i]['datecreated'] == $fecha)
					{
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idprestamo'].')" title="Eliminar Préstamo"><i class="bi bi-trash3-fill me-0"></i></button>';
					}
				}

				$arrData[$i]['options'] = '<div class="text-center d-flex gap-1">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}

			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	//TRAE LOS PRÉSTAMOS CON UNA FECHA DEFINIDA	
	public function getPrestamosFecha()
	{
		$ruta = $_SESSION['idRuta'];

		//VALIDA SI HAY UN RESUMEN CON EL ESTADO 0 Y DEVUELVE LA FECHA, Si NO, LO CREA.
		$fechaPrestamo = setDelResumenActual('set', $ruta)['datecreated'] ?? NOWDATE;

		$arrData = $this->model->selectPrestamosFecha($ruta, $fechaPrestamo);

		for ($i=0; $i < count($arrData); $i++) {

			$arrData[$i]['cliente'] = nombresApellidos($arrData[$i]['nombres'], $arrData[$i]['apellidos']);
			$arrData[$i]['hora'] = date('H:i', strtotime($arrData[$i]['hora']));
		}

		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
	}

	//TRAE UN PRÉSTASMO ESPECÍFICO
	public function getPrestamo()
	{
		if($_SESSION['permisosMod']['r'])
		{
			$idprestamo = intval($_POST['idPrestamo']);

			if($idprestamo > 0)
			{
				$arrData = $this->model->selectPrestamo($idprestamo);
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
	}

	//REGISTRAR PRÉSTAMO
	public function setPrestamo()
	{
		if($_POST){
			if(empty($_POST['txtMonto']) || empty($_POST['txtPlazo']) || empty($_POST['listFormato']))
			{
				$arrResponse = array("status" => false, "msg" => "Datos incorrectos.");
			}else{
				$idPrestamo = intval($_POST['idPrestamo']);
				$intClienteId = intval($_POST['listClientes']);
				$intMonto = intval($_POST['txtMonto']);
				$intTaza = intval($_POST['txtTaza']);
				$intPlazo = intval($_POST['txtPlazo']);
				$intFormato = intval($_POST['listFormato']);
				$strObservacion = strClean($_POST['txtObservacion']);
				$renovar = intval($_POST['renovar']);
				$cheked = isset($_POST['diasSemanales']) ?  1 : 0;
				$ruta = $_SESSION['idRuta'];
        		$usuario = $_SESSION['idUser'];
				$contadorPlazo = 0;
				$contador = 0;

				//VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
				$resumenAnterior = setDelResumenActual('set', $ruta);
				$fechaPrestamo = $resumenAnterior['datecreated'] ?? NOWDATE;

				//dep($resumenAnterior);exit;

				//Calculando el vencimiento del crédito
				$fechaEnSegundos = strtotime($fechaPrestamo);
				$dia = 86400;

				//DIARIO
				if($intFormato == 1)
				{
					while($contador < $intPlazo)
					{
						if(date("N", $fechaEnSegundos) == 6)// VALIDANDO EL DIA DOMINGO
						{
							$fechaEnSegundos += $dia;
						}
						if(date("N", $fechaEnSegundos) == 5 AND $cheked == 1)// VALIDANDO EL DIA SÁBADO
						{
							$fechaEnSegundos += $dia;
							if(date("N", $fechaEnSegundos) == 6)// VALIDANDO EL DIA DOMINGO
							{
								$fechaEnSegundos += $dia;
							}
						}
							$fechaEnSegundos += $dia;
							$contador += 1;
	
						$fechaFinal = date('Y-m-d' , ($fechaEnSegundos));
					}	
				}

				//SEMANAL
				if($intFormato == 2)
				{
					$contadorPlazo = $intPlazo * 6;
					
					while($contador <= $contadorPlazo)
					{
						if(date("N", $fechaEnSegundos) == 7)
						{
							$fechaEnSegundos += $dia;
						}else{
							$fechaEnSegundos += $dia;
							$contador += 1;
						}
						$fechaFinal = date('Y-m-d', ($fechaEnSegundos));
					}
					$fechaFinal = date("Y-m-d", strtotime($fechaFinal."- 1 days"));
				}

				//MES		
				if($intFormato == 3){
					$contadorPlazo = $intPlazo * 30;
					while($contador <= $contadorPlazo)
					{
						$fechaEnSegundos += $dia;
						$contador += 1;
					}	
					$fechaFinal = date('Y-m-d' , ($fechaEnSegundos));
				}	

				$request_prestamo = "";

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{			
					if($idPrestamo == 0)
					{
						$option = 1;
						if($_SESSION['permisosMod']['w']){
							$request_prestamo = $this->model->insertPrestamo($intClienteId, 
																			$intMonto,
																			$intTaza,
																			$intPlazo,
																			$intFormato,
																			$strObservacion,
																			$fechaPrestamo,
																			$fechaFinal,
																			$usuario,
																			$ruta);
						}

					} else {
						$option = 2;
						$request_prestamo = $this->model->updatePrestamo($idPrestamo,
																		$intMonto,
																		$intTaza,
																		$intPlazo,
																		$intFormato,
																		$strObservacion,
																		$fechaPrestamo,
																		$fechaFinal,
																		$ruta);
					}
					
					if($request_prestamo > 0)
					{
						/** GRÁFICAS DEL PRÉSTAMO**/
						$anio = date("Y");
						$mes = date("m");
						$prestamos = $this->model->selectPrestamosMes($anio,$mes);
						$prestamos2 = $this->model->selectPrestamosAnio($anio);
						$script = getFile("Template/Graficas/graficaPrestamosMes", $prestamos);
						$script2 = getFile("Template/Graficas/graficaPrestamosAnio", $prestamos2);
						/** DASHBOARD DEL PRÉSTAMO **/
						$valorActivo = valorActivoYEstimadoPrstamos()['valorActivo'];
						$cobradoEstimado = valorActivoYEstimadoPrstamos()['cobradoEstimado'];

						/** RESUMEN **/
						$arrResumen = getResumenActual($fechaPrestamo);
						/** DASHBOARD DEL RESUMEN **/
						$resumenAnterior = getResumenAnterior();
						$resumenActual = getResumenActual();
						$resumenActual1 = getResumenActual1();
						$cajaResumen = $resumenAnterior['total'] ?? $resumenActual['total'] ?? $resumenActual1['total'] ?? 0;
						$carteraResumen = valorActivoYEstimadoPrstamos()['valorActivo'] + $cajaResumen;
						$ultimosResumenes = getUltimosResumenes();

						$arrResponse = $option == 1 ? array('status' => true, 
															'msg' => 'Préstamo registrado.', 
															'resumen' => $arrResumen,
															'graficaMes' => $script,
															'graficaAnio' => $script2,
															'valorActivo' => $valorActivo,
															'cobradoEstimado' => $cobradoEstimado,
															'cajaResumen' => $cajaResumen,
															'carteraResumen' => $carteraResumen,
															'ultimosResumenes' => $ultimosResumenes)
													: array('status' => true, 
															'msg' => 'Préstamo actualizado.',
															'resumen' => $arrResumen,
															'graficaMes' => $script,
															'graficaAnio' => $script2,
															'valorActivo' => $valorActivo,
															'cobradoEstimado' => $cobradoEstimado);
					}else if($request_prestamo == '0')
					{
						$arrResponse = array('status' => false, 'msg' => 'Atencion! No es posible registrar el préstamo.');
					}else
					{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}	
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible registrar el Préstamo.');
				}
			}	
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	//ELIMINAR PRÉSTAMO
	public function delPrestamo()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$intIdprestamo = intval($_POST['idPrestamo']);
				$ruta = $_SESSION['idRuta'];

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{

					$requestDelete = $this->model->deletePrestamo($intIdprestamo, $ruta);
					if($requestDelete > 0)
					{
						$resumen = setDelResumenActual('del', $ruta);
						$status = is_array($resumen) ? false : true;
						if($status == true AND $resumen != NOWDATE)
						{
							$status = true;
						} else {
							$status = false;
						}	

						$anio = date("Y");
						$mes = date("m");
						$prestamos = $this->model->selectPrestamosMes($anio,$mes);
						$prestamos2 = $this->model->selectPrestamosAnio($anio);
						$script = getFile("Template/Graficas/graficaPrestamosMes", $prestamos);
						$script2 = getFile("Template/Graficas/graficaPrestamosAnio", $prestamos2);
						$valorActivo = valorActivoYEstimadoPrstamos()['valorActivo'];
						$cobradoEstimado = valorActivoYEstimadoPrstamos()['cobradoEstimado'];
						
						$arrResponse = array('status' => true, 
											 'msg' => 'Se ha eliminado el Préstamo.', 
											 'statusAnterior' => $status, 
											 'graficaMes' => $script,
											 'graficaAnio' => $script2,
											 'valorActivo' => $valorActivo,
											 'cobradoEstimado' => $cobradoEstimado);
					} else if($requestDelete == '0')
					{
						$arrResponse = array('status' => false, 'msg' => 'El Préstamo tiene pagamentos asociados.');
					}
					else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Préstamo.');
					}
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible eliminar el Préstamo.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	/** GRÁFICA **/
	//TRAE LOS PRÉSTAMOS DEPENDIENDO DE LA FECHA
	public function getDatosGraficaPrestamo()
	{
		if($_POST)
		{
			$fechaGrafica = $_POST['fecha'];
			$arrData = $this->model->datosGraficaPrestamo($fechaGrafica);
			$informacion_td = "";

			foreach($arrData as $prestamo)
			{
				$informacion_td .= "<tr>";
				$informacion_td .= '<td>'.$prestamo['nombres'].'</td>';
				$informacion_td .= '<td>'.$prestamo['monto'].'</td>';
				if($prestamo['hora'] != NULL) {
					$informacion_td .= '<td>'.date('H:i', strtotime($prestamo['hora'])).'</td>';
				} else {
					$informacion_td .= '<td><i class="bi bi-watch"></i></td>';
				}
				if($_SESSION['idRol'] == 1){$informacion_td .= '<td class="fst-italic">'.$prestamo['usuario'].'</td>';}
			}

			$informacion_td .= "</tr>";
			
			if($arrData)
			{
				$fecha = $arrData[0]['fecha'];
				$arrResponse = array('status' => true, 'data' => $informacion_td, 'fecha' => $fecha);	
			} else {
				$arrResponse = array('status' => false, 'msg' => 'Nenhum dado encontrado.');
			}

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	//BUSCADOR MENSUAL DE VENTAS
	public function prestamosMes()
	{
		if($_POST)
		{
			$grafica = "prestamosMes";
			$nFecha = str_replace(" ", "", $_POST['fecha']);
			$arrFecha = explode('-', $nFecha);
			$mes = $arrFecha[0];
			$anio = $arrFecha[1];
			$prestamos = $this->model->selectPrestamosMes($anio,$mes);
			$script = getFile("Template/Graficas/graficaPrestamosMes", $prestamos);
			echo $script;
			die();
		}
	}

	//BUSCADOR ANUAL DE VENTAS
	public function prestamosAnio()
	{
		if($_POST){
			$grafica = "prestamosAnio";
			$anio = intval($_POST['anio']);
			$prestamos = $this->model->selectPrestamosAnio($anio);
			$script = getFile("Template/Graficas/graficaPrestamosAnio",$prestamos);
			echo $script;
			die();
		}
	}

	//BUSCADOR DE RANGO DE FECHAS DE VENTAS
	public function getPrestamosD()
	{
		if($_POST)
		{
			$arrayFechas = explode("-", $_POST['fecha']);
			$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
			$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
			$ruta = $_SESSION['idRuta'];
			$detalles = '';
			$arrExplode = '';
			$totalPrestamos = 0;
			$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

			$_POST['prestamo'] == 'activo' ? $prestamosD = $this->model->selectPrestamosD($fechaI, $fechaF, $ruta)
											: $prestamosD = $this->model->selectPrestamosFinalizadosD($fechaI, $fechaF, $ruta);

			for ($i=0; $i < COUNT($prestamosD['prestamos']); $i++)
			{ 
				$arrExplode = explode("|",$prestamosD['prestamos'][$i]);/*CONVIRTIENDO STRING A UN ARRAY*/
				$fechaF = $dias[date('w', strtotime($arrExplode[0]))];/*FECHA FORMATEADA*/
				$detalles .= '<tr class="text-center">'; 
				$detalles .= '<td>
								<a 
									tabindex="0" role="button" 
									class="btn btn-secondary btn-sm" 
									data-bs-toggle="popover" 
									data-bs-placement="left" 
									data-bs-content="'.date('d-m-Y', strtotime($arrExplode[0])).'" 
									title="'.$fechaF.'">
									<i class="bi bi-calendar4-event me-0"></i>
								</a>
								</td>';
				$detalles .= '<td>'.$arrExplode[1].'</td>';/*VALOR*/
				/*INFO*/
				if($arrExplode[1] == 0)
				{
					$detalles .= '<td>
								<a style="cursor: not-allowed;opacity: 0.65;" tabindex="0" role="button" class="btn btn-secondary btn-sm">
									<i class="bi bi-info-circle me-0"></i>
								</a>
								</td>';	
				}else{
					$detalles .= '<td>
								<a 
									tabindex="0" role="button" 
									class="btn btn-secondary btn-sm" 
									data-bs-toggle="popover" 
									data-bs-placement="left" 
									data-bs-content="'.$arrExplode[2].'" 
									title="VENTA '."&nbsp;<div class='vr'></div>&nbsp;".' HORA '."&nbsp;<div class='vr'></div>&nbsp;".' USUARIO">
									<i class="bi bi-info-circle me-0"></i>
								</a>
								</td>';
				}
				//if($_SESSION['idRol'] == 1){$detalles .= '<td class="fst-italic">'.$arrExplode[4].'</td>';}/*USUARIO*/
				$detalles .= '</tr>';
				$totalPrestamos += $arrExplode[1];
			}
			
			$arrResponse = array('prestamosD' => $detalles, 'totalPrestamos' => $totalPrestamos);

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
	}

	//BUSCADOR MENSUAL DE COBRADO
	public function cobradoMes()
	{
		if($_POST)
		{
			$grafica = "cobradoMes";
			$nFecha = str_replace(" ", "", $_POST['fecha']);
			$arrFecha = explode('-', $nFecha);
			$mes = $arrFecha[0];
			$anio = $arrFecha[1];
			$cobrado = $this->model->selectCobradoMes($anio,$mes);
			$script = getFile("Template/Graficas/graficaCobradoMes", $cobrado);
			echo $script;
			die();
		}
	}

	//BUSCADOR ANUAL DE COBRADO
	public function cobradoAnio()
	{
		if($_POST){
			$grafica = "cobradoAnio";
			$anio = intval($_POST['anio']);
			$cobrado = $this->model->selectCobradoAnio($anio);
			$script = getFile("Template/Graficas/graficaCobradoAnio",$cobrado);
			echo $script;
			die();
		}
	}

	//BUSCADOR DE RANGO DE FECHAS DE COBRADO
	public function getCobradoD()
	{
		if($_POST)
		{
			$arrayFechas = explode("-", $_POST['fecha']);
			$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
			$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
			$ruta = $_SESSION['idRuta'];
			$detalles = '';
			$arrExplode = '';
			$totalCobrado = 0;
			$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

			$cobradoD = $this->model->selectCobradoD($fechaI, $fechaF, $ruta);

			for ($i=0; $i < COUNT($cobradoD['cobrado']); $i++)
			{ 
				$arrExplode = explode("|",$cobradoD['cobrado'][$i]);
				$fechaF = $dias[date('w', strtotime($arrExplode[0]))];
				$detalles .= '<tr class="text-center">'; 
				$detalles .= '<td>
								<a 
									tabindex="0" role="button" 
									class="btn btn-secondary btn-sm" 
									data-bs-toggle="popover" 
									data-bs-placement="left" 
									data-bs-content="'.date('d-m-Y', strtotime($arrExplode[0])).'" 
									title="'.$fechaF.'">
									<i class="bi bi-calendar4-event me-0"></i>
								</a>
								</td>';
				$detalles .= '<td>'.$arrExplode[1].'</td>';
				if($arrExplode[1] == 0)
				{
					$detalles .= '<td>
								<a style="cursor: not-allowed;opacity: 0.65;" tabindex="0" role="button" class="btn btn-secondary btn-sm">
									<i class="bi bi-info-circle me-0"></i>
								</a>
								</td>';	
				}else{
					$detalles .= '<td>
								<a 
									tabindex="0" role="button" 
									class="btn btn-secondary btn-sm" 
									data-bs-toggle="popover" 
									data-bs-placement="left" 
									data-bs-content="'.$arrExplode[2].'" 
									title="COBRADO '."&nbsp;<div class='vr'></div>&nbsp;".' HORA '."&nbsp;<div class='vr'></div>&nbsp;".' USUARIO">
									<i class="bi bi-info-circle me-0"></i>
								</a>
								</td>';
				}
				$detalles .= '</tr>';
				$totalCobrado += $arrExplode[1];
			}
			
			$arrResponse = array('cobradoD' => $detalles, 'totalCobrado' => $totalCobrado);

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
	}

	public function accion()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$ruta = $_POST['idRuta'];

				$requestAccion = $this->model->accionPagos($ruta);
				if($requestAccion > 0)
				{	
					$arrResponse = array('status' => true, 'msg' => 'Consulta realizada.', 'request' => $requestAccion);
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al consutar.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	public function accionPrestamos()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$ruta = $_POST['idRuta'];

				$requestAccion = $this->model->accionPrestamos($ruta);
				if($requestAccion > 0)
				{	
					$arrResponse = array('status' => true, 'msg' => 'Consulta realizada.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al consutar.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	public function accionPrestamosUsuario()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$ruta = $_POST['idRuta'];

				$requestAccion = $this->model->accionPrestamosUsuario($ruta);
				if($requestAccion > 0)
				{	
					$arrResponse = array('status' => true, 'msg' => 'Consulta realizada.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al consutar.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	/**** PRÉSTAMOS FINALIZADOS ****/
	public function finalizados()
    {
		$data['page_tag'] = "Prestamos Finalizados";
		$data['page_title'] = "PRESTAMOS FINALIZADOS";
		$data['page_name'] = "prestamos finalizados";
		$data['page_functions_js'] = "functions_prestamos.js";

		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0
		$data['resumenAnterior'] = getResumenAnterior();

		//TRAE LOS PRESTAMOS FINALIZADOS
		$data['prestamosFinalizados'] = $this->model->prestamosFinalizados($_SESSION['idRuta']);
		$data['prestamo'] = 'finalizado';
		//dep($data['prestamosFinalizados']);exit;
		
		$this->views->getView($this,"finalizados",$data);
    }
}