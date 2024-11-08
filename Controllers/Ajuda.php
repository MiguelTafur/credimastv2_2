<?php 

	class Ajuda extends Controllers{
		public function __construct()
		{
			session_start();
			parent::__construct();
		}

		public function ajuda()
		{
			$data['page_tag'] = "Clientes";
			$data['page_title'] = "CLIENTES";
			$data['page_name'] = "clientes";
			$this->views->getView($this,"ajuda",$data);
		}

		public function prestamos()
		{
			$data['page_tag'] = "Préstamos";
			$data['page_title'] = "PRÉASTAMOS";
			$data['page_name'] = "préstamos";
			$this->views->getView($this,"prestamos",$data);
		}

		public function resumen()
		{
			$data['page_tag'] = "Resumen";
			$data['page_title'] = "RESUMEN";
			$data['page_name'] = "resumen";
			$this->views->getView($this,"resumen",$data);
		}

		public function gastos()
		{
			$data['page_tag'] = "Gastos";
			$data['page_title'] = "GASTOS";
			$data['page_name'] = "gastos";
			$this->views->getView($this,"gastos",$data);
		}

	}
 ?>