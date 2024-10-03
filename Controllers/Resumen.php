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

		//TRAE EL RESUMEN CON ESTADO 0
		$data['resumenAnterior'] = $this->model->selectResumenAnterior($_SESSION['idRuta']);

		if(empty($data['resumenAnterior']))
		{
			$this->views->getView($this,"resumen",$data);
		} else {
			$this->views->getView($this,"resumenAnterior",$data);
		}
	}
}