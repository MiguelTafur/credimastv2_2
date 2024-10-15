<?php 

class Gastos extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

    public function Gastos()
	{
		$data['page_tag'] = "gastos";
		$data['page_title'] = "GASTOS";
		$data['page_name'] = "Gastos";
		$data['page_functions_js'] = "functions_gastos.js";

		/*** GRÁFICAS ***/ 
		$anio = date("Y");
		$mes = date("m");

		//MENSUAL
		$data['gastosMDia'] = $this->model->selectGastosMes($anio,$mes);

		//ANUAL
		$data['gastosAnio'] = $this->model->selectGastosAnio($anio);

		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0
		$data['resumenAnterior'] = getResumenAnterior();

		$this->views->getView($this,"gastos",$data);
	}

    //TRAE TODOS LOS GASTOS
    public function getGastos()
	{
		if($_SESSION['permisosMod']['r']){
			$arrData = $this->model->selectGastos($_SESSION['idRuta']);
			for ($i=0; $i < count($arrData); $i++) {
				
				$btnEdit = '';
				$btnDelete = '';

				$fecha = getResumenAnterior()['datecreated'] ?? NOWDATE;

				if($arrData[$i]['datecreated'] == $fecha)
				{
					if($_SESSION['permisosMod']['u']){
						$btnEdit = '<button class="btn btn-warning btn-sm me-1" onClick="fntEditInfo('.$arrData[$i]['idgasto'].')" title="Editar Gasto"><i class="bi bi-pencil-square me-0"></i></button>';
					}
				} else {
					if($_SESSION['permisosMod']['u']){
						$btnEdit = '<button class="btn btn-warning btn-sm me-1" title="Editar Gasto" disabled><i class="bi bi-pencil-square me-0"></i></button>';
					}
				}

				if($arrData[$i]['datecreated'] == $fecha)
				{
					if($_SESSION['permisosMod']['d']){
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idgasto'].')" title="Eliminar Gasto"><i class="bi bi-trash3-fill me-0"></i></button>';
					}
				} else {
					if($_SESSION['permisosMod']['d']){
						$btnDelete = '<button class="btn btn-danger btn-sm" title="Eliminar Gasto" disabled><i class="bi bi-trash3-fill me-0"></i></button>';
					}
				}

				$arrData[$i]['datecreated'] = date("d/m/Y", strtotime($arrData[$i]['datecreated'])) . ' - ' . date("H:i", strtotime($arrData[$i]['hora']));

				$arrData[$i]['options'] = '<div class="text-center d-flex justify-content-center">'.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	//TRAE UN GASTO ESPECÍFICO
	public function getGasto()
	{
		if($_SESSION['permisosMod']['r'])
		{
			$idgasto = intval($_POST['idGasto']);

			if($idgasto > 0)
			{
				$arrData = $this->model->selectGasto($idgasto);
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

	//REGISTRAR GASTO
	public function setGastos()
    {
        if($_POST)
        {
            if(empty($_POST['txtNombre']) || empty($_POST['txtValor']))
            {
                $arrResponse = array("status" => false, "msg" => "Error de datos.");
            }else{
                $idGasto = intval($_POST['idGasto']);
                $strNombre =  ucwords(strClean($_POST['txtNombre']));
                $intValor = intval($_POST['txtValor']);
				$ruta = $_SESSION['idRuta'];
        		$usuario = $_SESSION['idUser'];
                $request_user = "";

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{			
					//VALIDA SI HAY UN RESUMEN CON EL ESTADO 0 Y DEVUELVE LA FECHA, Si NO, LO CREA.
					$fechaGasto = setDelResumenActual('set', $ruta)['datecreated'] ?? NOWDATE;

					if($idGasto === 0)
					{
						$option = 1;
						if($_SESSION['permisosMod']['w']){
							$request_user = $this->model->insertGasto($usuario,$ruta,$strNombre,$intValor,$fechaGasto);
						}
					}else{
							$option = 2;
							if($_SESSION['permisosMod']['u']){
							$request_user = $this->model->updateGasto($idGasto,$strNombre,$intValor,$ruta,$fechaGasto);
							}
						}

					if($request_user > 0)
					{
						$arrResumen = getResumenActual($fechaGasto);

						if($option === 1){
							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.', 'resumen' => $arrResumen);
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.', 'resumen' => $arrResumen);
						}
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible registrar el Gasto.');
				}
            }	
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

	//ELIMINAR GASTO
	public function delGasto()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$intIdGasto = intval($_POST['idGasto']);
				$ruta = $_SESSION['idRuta'];

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{	

					$requestDelete = $this->model->deleteGasto($intIdGasto, $ruta);
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
						
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el Préstamo.', 'statusAnterior' => $status);
					} else if($requestDelete == '0')
					{
						$arrResponse = array('status' => false, 'msg' => 'El Préstamo tiene pagamentos asociados.');
					}
					else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Préstamo.');
					}
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible eliminar el Gasto.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	/** GRÁFICA **/
	//TRAE LOS GASTOS DEPENDIENDO DE LA FECHA
	public function getDatosGraficaGasto()
	{
		if($_POST)
		{
			$fechaGrafica = $_POST['fecha'];
			$arrData = $this->model->datosGraficaGasto($fechaGrafica);
			$informacion_td = "";

			foreach($arrData as $gasto)
			{
				$informacion_td .= "<tr>";
				$informacion_td .= '<td>'.$gasto['nombre'].'</td>';
				$informacion_td .= '<td>'.$gasto['monto'].'</td>';
				if($gasto['hora'] != NULL) {
					$informacion_td .= '<td>'.date('H:i', strtotime($gasto['hora'])).'</td>';
				} else {
					$informacion_td .= '<td>--:--</td>';
				}
				if($_SESSION['idRol'] == 1){$informacion_td .= '<td class="fst-italic">'.$gasto['nombres'].'</td>';}
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

	//BUSCADOR MENSUAL
	public function gastosMes()
	{
		if($_POST)
		{
			$grafica = "gastosMes";
			$nFecha = str_replace(" ", "", $_POST['fecha']);
			$arrFecha = explode('-', $nFecha);
			$mes = $arrFecha[0];
			$anio = $arrFecha[1];
			$gastos = $this->model->selectGastosMes($anio,$mes);
			$script = getFile("Template/Graficas/graficaGastosMes", $gastos);
			echo $script;
			die();
		}
	}

	//BUSCADOR ANUAL
	public function gastosAnio(){
		if($_POST){
			$grafica = "gastosAnio";
			$anio = intval($_POST['anio']);
			$gastos = $this->model->selectGastosAnio($anio);
			$script = getFile("Template/Graficas/graficaGastosAnio",$gastos);
			echo $script;
			die();
		}
	}

	//BUSCADOR DE RANGO DE FECHAS
	public function getGastosD()
	{
		if($_POST)
		{
			$arrayFechas = explode("-", $_POST['fecha']);
			$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
			$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
			$ruta = $_SESSION['idRuta'];
			$detalles = '';
			$arrExplode = '';
			$totalGastos = 0;
			$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

			$gastosD = $this->model->selectGastosD($fechaI, $fechaF, $ruta);

			for ($i=0; $i < COUNT($gastosD['gastos']); $i++)
			{ 
				$arrExplode = explode("|",$gastosD['gastos'][$i]);/*CONVIRTIENDO STRING A UN ARRAY*/
				$fechaF = $dias[date('w', strtotime($arrExplode[0]))];/*FECHA FORMATEADA*/
				$detalles .= '<tr class="text-center">'; 
				if($_SESSION['idRol'] == 1){$detalles .= '<td>'.$arrExplode[3].'</td>';}/*USUARIO*/
				// $detalles .= '<td>'.$dias[date('w', strtotime($arrExplode[0]))].'</td>';
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
									title="HORA / GASTOS">
									<i class="bi bi-info-circle me-0"></i>
								</a>
								</td>';
				}
				$detalles .= '</tr>';
				$totalGastos += $arrExplode[1];
			}
			
			$arrResponse = array('gastosD' => $detalles, 'totalGastos' => $totalGastos);

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

			//$cliente = forClientesPagos($fecha_actual);
		}
	}

}