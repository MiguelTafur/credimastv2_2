<?php 

	class Ajuda extends Controllers{
		public function __construct()
		{
			session_start();
			parent::__construct();
		}

		public function ajuda()
		{
			$data['page_tag'] = "Ajuda";
			$data['page_title'] = "AJUDA";
			$data['page_name'] = "ajuda";
			$this->views->getView($this,"ajuda",$data);
		}

	}
 ?>