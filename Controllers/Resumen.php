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

		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0
		$data['resumenAnterior'] = $this->model->selectResumenAnterior($_SESSION['idRuta']);

		//TRAE EL RESUMEN ANTERIOR CON ESTADO 0 Y LA FECHA ACTUAL
		$data['resumenActual'] = $this->model->selectResumenActual($_SESSION['idRuta']);

		//TRAE EL RESUMEN CON LA FECHA ACTUAL Y EL STATUS 1
		$data['resumenCerrado'] = $this->model->selectResumenCerrado($_SESSION['idRuta'], NOWDATE);

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
}