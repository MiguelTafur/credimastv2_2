<?php 

class Usuarios extends Controllers{
	public function __construct()
	{
		session_start();
		parent::__construct();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MUSUARIOS);
	}

	public function Usuarios()
	{
		if(empty($_SESSION['permisosMod']['r']) || $_SESSION['idUser'] !== 1){
			header("Location: ".base_url().'/prestamos');
		}
		$data['page_tag'] = "Usuarios";
		$data['page_title'] = "USUARIOS";
		$data['page_name'] = "usuarios";
		//$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
		$data['page_functions_js'] = "functions_usuarios.js";
		$this->views->getView($this,"usuarios",$data);
	}

	public function getRutas()
	{
		if($_SESSION['permisosMod']['r']){
			$htmlOptions = "";
			$arrData = $this->model->selectRutas();
			if(count($arrData) > 0){
				for ($i=0; $i < count($arrData); $i++) { 
					$htmlOptions .= '<option></option>';
					$htmlOptions .= '<option value="'.$arrData[$i]['idruta'].'">'.$arrData[$i]['nombre'].'</option>';
				}
			}
			echo $htmlOptions;
		}	
		die();
	}
	
	public function getUsuarios()
	{
		if($_SESSION['permisosMod']['r']){
			$arrData = $this->model->selectUsuarios();

			for ($i=0; $i < count($arrData); $i++) {

				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';

				if($arrData[$i]['status'] == 1)
				{
					$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
				}

				if($_SESSION['permisosMod']['r']){
					$btnView = '<button class="btn btn-secondary btn-sm me-1" onclick="fntViewUsuario('.$arrData[$i]['idpersona'].')" title="Ver usuario">
									<i class="bi bi-person-vcard-fill me-0"></i>
								</button>'; 
				}
				if($_SESSION['permisosMod']['u']){
					if($_SESSION['idUser'] == 1 && $_SESSION['userData']['idrol'] == 1 ||
					  ($_SESSION['userData']['idrol'] == 1 && $arrData[$i]['idrol'] != 1)){
						$btnEdit = '<button class="btn btn-warning btn-sm me-1" onclick="fntEditUsuario('.$arrData[$i]['idpersona'].')" title="Editar usuario"><i class="bi bi-pencil-square me-0"></i></button>';
					}else{
						$btnEdit = '<button class="btn btn-secondary btn-sm me-1" disabled><i class="bi bi-pencil-square me-0"></i></button>';
					}
				}
				if($_SESSION['permisosMod']['d']){
					if($_SESSION['idUser'] == 1 && $_SESSION['userData']['idrol'] == 1 ||
						($_SESSION['userData']['idrol'] == 1 && $arrData[$i]['idrol'] != 1) and
					    ($_SESSION['userData']['idpersona'] != $arrData[$i]['idpersona'])){

						$btnDelete = '<button class="btn btn-danger btn-sm me-1" onclick="fntDelUsuario('.$arrData[$i]['idpersona'].')" title="Eliminar usuario"><i class="bi bi-trash3-fill me-0"></i></button>';
					}else{
						$btnDelete = '<button class="btn btn-secondary btn-sm" disabled><i class="bi bi-trash3-fill me-0"></i></button>';
					}
				}

				$arrData[$i]['options'] = '<div class="text-center d-flex justify-content-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getUsuario()
	{
		if($_SESSION['permisosMod']['r']){
			$idusuario = intval($_POST['idPersona']);
			if($idusuario > 0)
			{
				$arrData = $this->model->selectUsuario($idusuario);
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function setUsuario()
	{
		if($_POST)
		{
			if(empty($_POST['txtNombre']) || empty($_POST['txtEmail']) || empty($_POST['listRolid']) || empty($_POST['listStatus']) || empty($_POST['listRuta']))
			{
				$arrResponse = array("status" => false, "msg" => "Datos incorrectos.");
			}else{
				$idUsuario = intval($_POST['idUsuario']);
				$strNombre =  ucwords(strClean($_POST['txtNombre']));
				$strEmail = strtolower(strClean($_POST['txtEmail']));
				$intTipoId = intval($_POST['listRolid']);
				$intStatus = intval($_POST['listStatus']);
				$intRuta = intval($_POST['listRuta']);
				$request_user = "";

				if($idUsuario == 0)
				{
					$option = 1;
					
					if($_SESSION['permisosMod']['w']){
						$request_user = $this->model->insertUsuario($strNombre,$strEmail,$intTipoId,$intStatus,$intRuta);
					}
				}else{
					$option = 2;
					if($_SESSION['permisosMod']['u']){
						$request_user = $this->model->updateUsuario($idUsuario,$strNombre,$strEmail,$intTipoId,$intStatus);
					}
				}

				if($request_user > 0)
				{
					if($option == 1){
						$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
					}else{
						$arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
					}
				}else if($request_user == '0'){
					$arrResponse = array('status' => false, 'msg' => 'Atencion! Email ya existe, ingrese otro diferente.');
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
				}
			}	
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function delUsuario()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){
				$intIdpersona = intval($_POST['idPersona']);
				$requestDelete = $this->model->deleteUsuario($intIdpersona);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el usuario.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el usuario.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}
}   