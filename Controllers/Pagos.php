<?php

class Pagos extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

	//INSERTA EL PAGAMENTO
    public function setPago()
    {
        if($_POST)
		{
			if(empty($_POST['pagoPrestamo']))
			{
				$arrResponse = array("status" => false, "msg" => "Debes ingresar un valor.");
			}else
			{
				$idPrestamo = intval(($_POST['idPrestamo']));
				$intMonto = intval($_POST['pagoPrestamo']);
				$usuario = $_SESSION['idUser'];
				$ruta = $_SESSION['idRuta'];

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{
					//VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
					$fechaResumen = setDelResumenActual('set', $ruta)['datecreated'] ?? NULL;

					$request_pago = $this->model->insertPago($idPrestamo, $intMonto, $usuario, $ruta, $fechaResumen);

					if($request_pago > 0)
					{
						$valorActivo = valorActivoYEstimadoPrstamos()['valorActivo'];
						$cobradoEstimado = valorActivoYEstimadoPrstamos()['cobradoEstimado'];

						/*** GRÁFICAS ***/
						$anio = date("Y");
						$mes = date("m");
						$cobradoMes = $this->model->selectCobradoMes($anio,$mes);
						$cobradoAnio = $this->model->selectCobradoAnio($anio);
						$scriptMes = getFile("Template/Graficas/graficaCobradoMes", $cobradoMes);
						$scriptAnio = getFile("Template/Graficas/graficaCobradoAnio",$cobradoAnio);

						$arrResponse = array('status' => true,
											'msg' => 'Datos guardados correctamente.',
											'valorActivo' => $valorActivo,
											'cobradoEstimado' => $cobradoEstimado, 
											'graficaMes' => $scriptMes,
											'graficaAnio' => $scriptAnio);

					}else if($request_pago == '0')
					{
						$arrResponse = array("status" => false, "msg" => "Pago ya realizado.", );
					}else if($request_pago == '!')
					{
						$arrResponse = array("status" => false, "msg" => "El pago ingresado no puede ser mayor al saldo.");
					}else
					{
						$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
					}
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible registrar el Pago.');
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
    }

	//INSERTA UNO O VARIOS PAGAMENTOS
	public function setPayAll()
	{
		if($_POST){
			//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
			$estadoResumen = getResumenActual1($_SESSION['idRuta'])['status'] ?? 0;
			
			if($estadoResumen === 0)
			{
				
				$datos = json_decode($_POST['datos'], true);
				$codigoPrestamo = intval($datos['id']);
				$intMonto = intval($datos['pago']);
				$ruta = $_SESSION['idRuta'];

				//VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
				$fechaResumen = setDelResumenActual('set', $ruta)['datecreated'] ?? NULL;

				if($intMonto == 0){
					$arrResponse = array("status" => false, "msg" => "El valor no puede ser 0.");
				} else {
					$request_pago = $this->model->insertPago($codigoPrestamo, $intMonto, $_SESSION['idUser'], $ruta, $fechaResumen);
					if($request_pago > 0)
					{
						$valorActivo = valorActivoYEstimadoPrstamos()['valorActivo'];
						$cobradoEstimado = valorActivoYEstimadoPrstamos()['cobradoEstimado'];

						/*** GRÁFICAS ***/
						$anio = date("Y");
						$mes = date("m");
						$cobradoMes = $this->model->selectCobradoMes($anio,$mes);
						$cobradoAnio = $this->model->selectCobradoAnio($anio);
						$scriptMes = getFile("Template/Graficas/graficaCobradoMes", $cobradoMes);
						$scriptAnio = getFile("Template/Graficas/graficaCobradoAnio",$cobradoAnio);

						$arrResponse = array('status' => true,
											'msg' => 'Datos guardados correctamente.',
											'valorActivo' => $valorActivo,
											'cobradoEstimado' => $cobradoEstimado,
											'graficaMes' => $scriptMes,
											'graficaAnio' => $scriptAnio);

					}else if($request_pago == '0')
					{
						$arrResponse = array("status" => false, "msg" => "Pago ya realizado.");
					}else if($request_pago == '!')
					{
						$arrResponse = array("status" => false, "msg" => "El pago ingresado no puede ser mayor al saldo.");
					}else
					{
						$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
					}				
				}

				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);

			} else {
				$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible registrar el Pago.');
			}
		}
	} 

	//TRAE LOS TODOS LOS PAGAMENTOS Y LOS DEVUELVE EN UNA ETIQUETA "<tr>"
	public function getPagos()
	{
		if($_SESSION['permisosMod']['r']){
			$idprestamo = intval($_POST['idPrestamo']);
			if($idprestamo > 0)
			{
				$arrData = $this->model->selectPagamentos($idprestamo);

				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Sin pagamentos.');
				}else{
					$cliente = nombresApellidos($arrData[0]['nombres'], $arrData[0]['apellidos']);
					$arrPagos = "";
					$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
					for ($i=0; $i < count($arrData); $i++)
					{
						$dia = $dias[date('w', strtotime($arrData[$i]['datecreated']))];
						$arrPagos .= '
						<tr class="text-center">';
						$arrPagos .= '<td>
										<a
										tabindex="0" role="button"
										class="btn btn-secondary btn-sm"
										data-bs-toggle="popover"
										data-bs-placement="left"
										data-bs-content="'.date('d-m-Y', strtotime($arrData[$i]['datecreated'])).'"
										title="'.$dia.'">
										<i class="bi bi-calendar4-event me-0"></i>
										</a>
									</td>';
						$arrPagos .= '<td>'.$arrData[$i]['abono'].'</td>';
						if($arrData[$i]['hora'] != NULL) {
							$arrPagos .= '<td>'.date('H:i', strtotime($arrData[$i]['hora'])).'</td>';
						} else {
							$arrPagos .= '<td><i class="bi bi-watch"></i></td>';
						}
						$arrPagos .= '<td class="fst-italic">'.$arrData[$i]['personaid'].'</td>';
						$arrPagos .= '</tr>';
					}
					$arrResponse = array('status' => true, 'pagos' => $arrPagos, 'cliente' => $cliente);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	//ELIMINAR PAGO
	public function delPago()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$intIdprestamo = intval($_POST['idPrestamo']);
				$intIdPago = intval($_POST['idPago']);
				$usuario = $_SESSION['idUser'];
				$ruta = $_SESSION['idRuta'];

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{

					$requestDelete = $this->model->deletePago($intIdprestamo, $intIdPago, $ruta);

					if($requestDelete)
					{
						//ELIMINA EL RESUMEN SI LA BASE, EL COBRADO, LAS VENTAS, Y LOS GASTOS ESTÁN NULLOS
						$resumen = setDelResumenActual('del', $ruta);
						$status = is_array($resumen) ? false : true;
						if($status == true AND $resumen != NOWDATE)
						{
							$status = true;
						} else {
							$status = false;
						}

						$valorActivo = valorActivoYEstimadoPrstamos()['valorActivo'];
						$cobradoEstimado = valorActivoYEstimadoPrstamos()['cobradoEstimado'];
						/*** GRÁFICAS ***/
						$anio = date("Y");
						$mes = date("m");
						$cobradoMes = $this->model->selectCobradoMes($anio,$mes);
						$cobradoAnio = $this->model->selectCobradoAnio($anio);
						$scriptMes = getFile("Template/Graficas/graficaCobradoMes", $cobradoMes);
						$scriptAnio = getFile("Template/Graficas/graficaCobradoAnio",$cobradoAnio);

						$arrResponse = array('status' => true,
						'msg' => 'Se ha eliminado el pago.',
						'statusAnterior' => $status,
						'valorActivo' => $valorActivo,
						'cobradoEstimado' => $cobradoEstimado,
						'graficaMes' => $scriptMes,
						'graficaAnio' => $scriptAnio);
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Pago.');
					}
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible eliminar el Pago.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	//TRAE LOS PAGOS DEPENDIENDO DE LA FECHA
	public function getDatosGraficaCobrado()
	{
		if($_POST)
		{
			$fechaGrafica = $_POST['fecha'];
			$arrData = $this->model->selectPagamentosFecha($fechaGrafica,$_SESSION['idRuta']);
			$informacion_td = "";

			//dep($arrData);exit;

			foreach($arrData as $cobrado)
			{
				$informacion_td .= "<tr>";
				$informacion_td .= '<td>'.nombresApellidos($cobrado['nombres'], $cobrado['apellidos']).'</td>';
				$informacion_td .= '<td>'.$cobrado['abono'].'</td>';
				if($cobrado['hora'] != NULL) {
					$informacion_td .= '<td>'.date('H:i', strtotime($cobrado['hora'])).'</td>';
				} else {
					$informacion_td .= '<td><i class="bi bi-watch"></i></td>';
				}
				if($_SESSION['idRol'] == 1){$informacion_td .= '<td class="fst-italic">'.$cobrado['usuario'].'</td>';}
			}

			$informacion_td .= "</tr>";
			
			if($arrData)
			{
				$fecha = date("d/m/Y", strtotime($fechaGrafica));
			 	$arrResponse = array('status' => true, 'data' => $informacion_td, 'fecha' => $fecha);	
			} else {
				$arrResponse = array('status' => false, 'msg' => 'Nenhum dado encontrado.');
			}

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}