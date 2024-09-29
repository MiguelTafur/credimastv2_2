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
		/*$data['resumen'] = $this->model->selectResumen();
		$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
		$fechaPagamento = $data['pagamentos'] == 2 ? NULL : $data['pagamentos'];
		
		if($data['pagamentos'] != 2)
		{
			$data['prestamos'] = $this->model->selectPrestamos2($fechaPagamento);
			//dep($data['prestamos']);exit();
		}*/
		$data['page_functions_js'] = "functions_prestamos.js";
		$this->views->getView($this,"prestamos",$data);
	}

	//TRAE TODOS LOS PRÉSTASMOS
	public function getPrestamos()
	{
		if($_SESSION['permisosMod']['r'])
		{
			$arrData = $this->model->selectPrestamos();

			for ($i=0; $i < count($arrData); $i++) {
				
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';

				//JUNTANDO EL NOMBRE Y EL NEGOCIO DEL CLIENTE
				$arrData[$i]['cliente'] = nombresApellidos($arrData[$i]['nombres'], $arrData[$i]['apellidos']);

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

				//MOSTRANDO LOS BOTONES SEGÚN LOS PARÁMETROS
				if($arrPagos[0] == NOWDATE )
				{
					$btnAbono = '<div class="d-grid gap-2 d-block">
							<button class="btn btn-success btn-sm btn-block" onclick="fntDelInfoPago('.$arrPagos[1].', '.$arrData[$i]['idprestamo'].')" title="Eliminar pago">
							<p class="m-0 fs-6 font-monospace">'.$arrPagos[2].'</p>
							</button>
						</div>';
					if($arrData[$i]['saldo'] == 0)
					{
						$btnAbono = '<div class="d-flex gap-2">
								<button class="btn btn-warning btn-sm" onclick="fntRenovar('.$arrPagos[1].')" title="Eliminar pago">
									RENOVAR
								</button>
								<button class="btn btn-success btn-sm" onclick="fntDelInfoPago('.$arrPagos[1].', '.$arrData[$i]['idprestamo'].')" title="Eliminar pago">
									<p class="m-0 fs-6 font-monospace">'.$arrPagos[2].'</p>
								</button>
							</div>
						';	
					}
				} else {
					$btnAbono = '
						<div class="input-group">
							<input type="text" class="form-control valid validNumber" name="txtPagoPrestamo" id="txtPagoPrestamo-'.$arrData[$i]['idprestamo'].'" placeholder="'.$parcela.'" aria-label="100" aria-describedby="button-addon2" onkeypress="return controlTag(event)">
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
					if($arrData[$i]['datecreated'] == NOWDATE)
					{
						$btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditInfo('.$arrData[$i]['idprestamo'].')" title="Editar Préstamo"><i class="bi bi-pencil-square me-0"></i></button>';
					}
				}
				if($_SESSION['permisosMod']['d'])
				{
					if($arrData[$i]['datecreated'] == NOWDATE)
					{
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idprestamo'].')" title="Eliminar Préstamo"><i class="bi bi-trash3-fill me-0"></i></button>';
					}
				}

				$arrData[$i]['options'] = '<div class="text-center d-flex gap-1">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}

			/*for ($i=0; $i < count($arrData); $i++)
			{ 
				//dep($arrData);exit;
				$fecha_actual = date('Y-m-d');
				$btnView = '';
				$btnDelete = '';
				$btnAbono = '';
				$taza = ($arrData[$i]['taza'] * 0.01);
				$subtotal = ($arrData[$i]['monto'] * $taza);
				$total = ($arrData[$i]['monto'] + $subtotal);
				$parcela = ($total/$arrData[$i]['plazo']);
				$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

				$arrData[$i]['monto'] = '<strong>'.$arrData[$i]['monto'].'</strong>';
				$arrData[$i]['pa'] = (' <strong>'.$parcela.' x '.$arrData[$i]['plazo'].'</strong>');

				if($arrData[$i]['pago'] != 0 && $arrData[$i]['datepago'] == $fecha_actual && $arrData[$i]['status'] == 2)
				{
					$btnAbono = '<p class="text-danger h5">
									<button class="btn btn-success btn-sm" onclick="fntRenovarPrestamo('.$arrData[$i]['idprestamo'].', '."".')">RENOVAR</button> &nbsp;&nbsp;
									<button class="btn btn-danger btn-sm" onclick="fntDelPago('.$arrData[$i]['pagoid'].')" title="Eliminar pago">
									'.$arrData[$i]['pago'].'
									</button>
								</p>';

				}else if($arrData[$i]['pago'] != 0 && $arrData[$i]['datepago'] == $fecha_actual && $arrData[$i]['pagoid'] != NULL){
					$btnAbono = '<button class="btn btn-success btn-sm" onclick="fntDelPago('.$arrData[$i]['pagoid'].')" title="Eliminar pago">
									'.$arrData[$i]['pago'].'
								</button>';
				}else{
					$btnAbono = '<div class="text-center divPagoPrestamo">
									<input type="tel" class="inpPago '.$arrData[$i]['idprestamo'].' my-1" id="'.$arrData[$i]['idprestamo'].'" style="width: 73px; height: 35px; padding: 5px" placeholder="'.$arrData[$i]['parcela'].'" onkeypress="return controlTag(event)">
									<button id="btn-'.$arrData[$i]['idprestamo'].'" class="btn btn-secondary btn-sm pagoPrestamo" title="Agregar Pago" onclick="fntPagoPrestamo('.$arrData[$i]['idprestamo'].')"><i class="fas fa-hand-holding-usd"></i> Pagar
									</button>
								</div>';
				}

				$arrData[$i]['pagamento'] = '<div id="div-'.$arrData[$i]['idprestamo'].'" class="text-center">
												'.$btnAbono.' 
												<button class="btn btn-success btn-sm d-none" onclick="fntDelPago('.$arrData[$i]['pagoid'].')" id="btn2-'.$arrData[$i]['idprestamo'].'" title="Eliminar pago">
													'.$arrData[$i]['pago'].';
												</button>
											</div>';

				

				$arrData[$i]['nombres'] = '<strong>'.strtok(strtoupper($arrData[$i]['nombres']), " ").'</strong> <i>'.$arrData[$i]['apellidos'].'</i>';

				$arrData[$i]['taza'] = $arrData[$i]['taza'].' '.'%';

				$arrData[$i]['total'] = '<span id="tot-'.$arrData[$i]['idprestamo'].'" class="font-weight-bold font-italic text-danger">'.$arrData[$i]['total'].'</span>';				

				if($_SESSION['permisosMod']['w'])
				{
					$btnView = '<button class="btn btn-info " onclick="fntViewPrestamo('.$arrData[$i]['idprestamo'].')" title="Ver Prestamo"><i class="far fa-eye"></i></button>&nbsp;&nbsp;';
				}

				if($_SESSION['permisosMod']['d'])
				{
					if($arrData[$i]['datecreated'] == $fecha_actual)
					{
						$btnDelete = '<button class="btn btn-danger " onclick="fntDelPrestamo('.$arrData[$i]['idprestamo'].')" title="Eliminar Prestamo"><i class="far fa-trash-alt"></i></button>';
					}
				}

				if($arrData[$i]['fechavence'] != NULL)
				{
					$diasVencimiento4 = date("Y-m-d", strtotime('-4 day', strtotime($arrData[$i]['fechavence'])));
					$diasVencimiento3 = date("Y-m-d", strtotime('-3 day', strtotime($arrData[$i]['fechavence'])));
					$diasVencimiento2 = date("Y-m-d", strtotime('-2 day', strtotime($arrData[$i]['fechavence'])));
					$diasVencimiento1 = date("Y-m-d", strtotime('-1 day', strtotime($arrData[$i]['fechavence'])));

					//$arrData[$i]['diasVence'] = $diasVencimiento4;
					
						if($diasVencimiento4 == $fecha_actual || 
						$diasVencimiento3 == $fecha_actual || 
						$diasVencimiento2 == $fecha_actual || 
						$diasVencimiento1 == $fecha_actual || 
						$arrData[$i]['fechavence'] == $fecha_actual)
						{
						$arrData[$i]['diasVence'] = false;
						}else if($arrData[$i]['fechavence'] < $fecha_actual)
						{
						$arrData[$i]['diasVence'] = "vencido";
						}else{
						$arrData[$i]['diasVence'] = true;
					}
				}

				$arrData[$i]['options'] = '<div class="text-center d-flex justify-content-center">'.$btnView.' '.$btnDelete.'</div>';
			}*/

			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	//TRAE PRÉSTASMOS ESPECÍFICO
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
				/*if(!empty($_POST['inputClienteRenovar'])){
					$intClienteId = intval($_POST['inputClienteRenovar']);
				}
				if(!empty($_POST['listClientId'])){
					$intClienteId = intval($_POST['listClientes']);
				}*/
				$idPrestamo = intval($_POST['idPrestamo']);
				$intClienteId = intval($_POST['listClientes']);
				$intMonto = intval($_POST['txtMonto']);
				$intTaza = intval($_POST['txtTaza']);
				$intPlazo = intval($_POST['txtPlazo']);
				$intFormato = intval($_POST['listFormato']);
				$strObservacion = strClean($_POST['txtObservacion']);
				$fecha_actual = NOWDATE;
				$cheked = isset($_POST['diasSemanales']) ?  1 : 0;
				$contadorPlazo = 0;
				$contador = 0;

				/*
				if(!empty($_POST['fechaAnterior']))
				{
					$fecha_actual = $_POST['fechaAnterior'];
				}else{
					$fecha_actual = NOWDATE;
				}
				*/

				//Calculando el vencimiento del crédito
				$fechaEnSegundos = strtotime($fecha_actual);
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

				//dep($fechaFinal);exit;

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
																		$fecha_actual,
																		$fechaFinal);
					}
				} else {
					$option = 2;
					$request_prestamo = $this->model->updatePrestamo($idPrestamo,
																	$intMonto,
																	$intTaza,
																	$intPlazo,
																	$intFormato,
																	$strObservacion,
																	$fechaFinal);
				}
				
				if($request_prestamo > 0)
				{
					$arrResponse = array('status' => true, 'msg' => 'Préstamo registrado.');
				}else if($request_prestamo == '0')
				{
					$arrResponse = array('status' => false, 'msg' => 'Atencion! Error al registrar el préstamo.');
				}else
				{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
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

				/*
				$arrDataP = $this->model->selectDatePagoPrestamo();

				$fecha = "";

				if($arrDataP == 2){
					$fecha = date("Y-m-d");
				}else{
					$fecha = $arrDataP;					
				}
				*/

				$requestDelete = $this->model->deletePrestamo($intIdprestamo);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el Préstamo.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Préstamo.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}
}