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
}