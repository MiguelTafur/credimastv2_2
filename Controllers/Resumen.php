<?php 

class Resumen extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

	public function Resumen()
	{
		$data['page_tag'] = "resumen";
		$data['page_title'] = "RESUMEN";
		$data['page_name'] = "Resumen";
		$data['page_functions_js'] = "functions_resumen.js";

		// $data['prueba'] = $this->model->accion($_SESSION['idRuta']);
		// dep($data['prueba']);exit;

		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0
		$data['resumenAnterior'] = $this->model->selectResumenAnterior($_SESSION['idRuta']);

		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0 Y LA FECHA ACTUAL
		$data['resumenActual'] = $this->model->selectResumenActual($_SESSION['idRuta']);

		//TRAE EL RESUMEN CON LA FECHA ACTUAL Y EL STATUS 1
		$data['resumenCerrado'] = $this->model->selectResumenActual1($_SESSION['idRuta']);

		//TRAE EL ÚLTIMO RESUMEN CON STATUS 1
		$data['resumenUltimo'] = $this->model->selectResumenUltimo($_SESSION['idRuta']);

		$this->views->getView($this,"resumen",$data);
	}

	public function setResumen()
	{
		if($_POST)
		{
			if(!isset($_POST['idResumen']) || empty($_POST['idResumen']))
			{
				$arrResponse = array("status" => false, "msg" => "No hay datos para registrar el Resumen.");
			} else {
				$idResumen = intval($_POST['idResumen']);
				$status = intval($_POST['status']);

				$request_resumen = $this->model->statusResumen($idResumen, $status);

				if($request_resumen > 0)
				{
					$arrResponse = $status == 1 ? array('status' => true, 'msg' => 'Resumen registrado.')
									     		: array('status' => true, 'msg' => 'Corregir Resumen.');
				}else
				{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
				}	
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getResumenUltimo()
	{
		if($_POST)
		{
			$ruta = $_POST['idRuta'];

			$base = $this->model->selectResumenUltimo($ruta);
			
			$arrResponse = array('status' => true, 'base' => $base);
			
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
	}

	//BUSCADOR DE RANGO DE FECHAS
	public function getResumenD()
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

			$resumenD = $this->model->selectResumenD($fechaI, $fechaF, $ruta);
			//dep($resumenD);exit;

			foreach ($resumenD as $resumen) {
				$cobrado = $resumen['cobrado'] ?? 0;
				$ventas = $resumen['ventas'] ?? 0;
				$gastos = $resumen['gastos'] ?? 0;

				$base = getBaseActualAnterior($resumen['datecreated']) != 0 ? getBaseActualAnterior($resumen['datecreated'])['actual'] : $resumen['base'];

				$basePopover = getBaseActualAnterior($resumen['datecreated']) == 0 
											? $resumen['base']
                                    	  	: '<button 
                                             class="btn btn-link btn-sm link-warning link-underline-opacity-0" 
                                             style="font-size: inherit;"
                                             data-bs-toggle="popover" 
                                             data-bs-placement="left" 
                                             data-bs-content=" Anterior: '.round(getBaseActualAnterior($resumen['datecreated'])['anterior'], 0). 
												"  &nbsp;<div class='vr'></div>&nbsp;" . ' '.getBaseActualAnterior($resumen['datecreated'])['horaAnterior'].'
												'.getBaseActualAnterior($resumen['datecreated'])['usuarioAnterior'].'" 
                                             title="BASE MODIFICADA">
                                             ' . getBaseActualAnterior($resumen['datecreated'])['actual'] . ' 
                                             </button>';

				$cobradoPopover = $cobrado == 0 
											? $cobrado
                                    	  : '<button 
                                             class="btn btn-link btn-sm link-warning link-underline-opacity-0" 
                                             style="font-size: inherit;"
                                             data-bs-toggle="popover" 
                                             data-bs-placement="left" 
                                             data-bs-content="'.getFormatCobrado($resumen['datecreated']).'" 
                                             title="COBRADO '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO">
                                             '.round($cobrado, 0).'
                                             </button>';

				$ventasPopover = $ventas == 0 ? $ventas
										: '<button 
											class="btn btn-link btn-sm link-warning link-underline-opacity-0" 
											style="font-size: inherit;"
											data-bs-toggle="popover" 
											data-bs-placement="left" 
											data-bs-content="'.getFormatPrestamos($resumen['datecreated']).'" 
											title="VENTA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO">
											'.round($ventas, 0).'
											</button>';

				$gastosPopover = $gastos == 0 ? $gastos
										: '<button 
											class="btn btn-link btn-sm link-warning link-underline-opacity-0" 
											style="font-size: inherit;"
											data-bs-toggle="popover" 
											data-bs-placement="left" 
											data-bs-content="'.getFormatGastos($resumen['datecreated']).'" 
											title="GASTO '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' HORA '  ."&nbsp;<div class='vr'></div>&nbsp;"  .' USUARIO">
											'.round($gastos, 0).'
											</button>';

				$detalles .= '<tr class="text-center">'; 
				$detalles .= '<td>'.$resumen['datecreated'].'</td>';
				$detalles .= '<td>'.$basePopover.'</td>';
				$detalles .= '<td>'.$cobradoPopover.'</td>';
				$detalles .= '<td>'.$ventasPopover.'</td>';
				$detalles .= '<td>'.$gastosPopover.'</td>';
				$detalles .= '<td>'.$resumen['total'] ?? '0'.'</td>';
					
				$detalles .= '</tr>';
			}

			/*for ($i=0; $i < COUNT($resumenD); $i++)
			{ 
				$detalles .= '<tr class="text-center">'; 
				$detalles .= '<td>';
					$resumenD[$i]['datecreated'];
				$detalles .= '</td>';
				$detalles .= '<td>';
					$resumenD[$i]['base'];
				$detalles .= '</td>';
				$detalles .= '<td>';
					$resumenD[$i]['cobrado'];
				$detalles .= '</td>';
				$detalles .= '<td>';
					$resumenD[$i]['ventas'];
				$detalles .= '</td>';
				$detalles .= '<td>';
					$resumenD[$i]['gastos'];
				$detalles .= '</td>';
				$detalles .= '<td>';
					$resumenD[$i]['total'];
				$detalles .= '</td>';
				$detalles .= '</tr>';
			}*/
			
			$arrResponse = array('resumenD' => $detalles);

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
	}
}