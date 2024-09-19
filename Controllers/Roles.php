<?php 

	class Roles extends Controllers{
		public function __construct()
		{
			session_start();
			parent::__construct();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
			}
			getPermisos(MUSUARIOS);
		}

		public function Roles()
		{
			if(empty($_SESSION['permisosMod']['r']) || $_SESSION['idUser'] !== 1){
				header("Location: ".base_url().'/prestamos');
			}
			$data['page_id'] = 3;
			$data['page_tag'] = "Roles Usuario";
			$data['page_name'] = "rol_usuario";
			$data['page_title'] = "Roles Usuario";
			//$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
			$data['page_functions_js'] = "functions_roles.js";
			$this->views->getView($this,"roles",$data);
		}

        public function getRoles()
		{
			if($_SESSION['permisosMod']['r']){
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				$arrData = $this->model->selectRoles();

				for ($i=0; $i < count($arrData); $i++) {

					if($arrData[$i]['status'] == 1)
					{
						$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
					}else{
						$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
					}

					if($_SESSION['permisosMod']['u']){
						$btnView = '<button class="btn btn-secondary btn-sm me-1" onclick="fntPermisos('.$arrData[$i]['idrol'].')" title="Permisos"><i class="bi bi-key me-0"></i></button>';
						$btnEdit = '<button class="btn btn-warning btn-sm me-1" onclick="fntEditRol('.$arrData[$i]['idrol'].')" title="Editar"><i class="bi bi-pencil-square me-0"></i></button>';
					}
					if($_SESSION['permisosMod']['d']){
						$btnDelete = '<button class="btn btn-danger btn-sm" onclick="fntDelRol('.$arrData[$i]['idrol'].')" title="Eliminar"><i class="bi bi-trash3-fill me-0"></i></button>';
					}

					$arrData[$i]['options'] = '<div class="text-center d-flex justify-content-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function getRol()
		{
			$idrol = $_POST['idRol'];
			if($_SESSION['permisosMod']['r'])
			{
				$intIdrol = intval(strClean($idrol));
				if($intIdrol > 0)
				{
					$arrData = $this->model->selectRol($intIdrol);
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

		public function getSelectRoles()
		{
			$htmlOptions = "";
			$arrData = $this->model->selectRoles();
			if(count($arrData) > 0)
			{
				for ($i=0; $i < count($arrData); $i++) { 
					if($arrData[$i]['status'] == 1){
						$htmlOptions .= '<option></option>';
						$htmlOptions .= '<option value="'.$arrData[$i]['idrol'].'">'.$arrData[$i]['nombrerol'].'</option>';
					}
				}
			}
			echo $htmlOptions;
			die();
		}

		public function setRol()
		{
			if($_SESSION['permisosMod']['w'])
			{
				$intIdrol = intval($_POST['idRol']);
				$strRol =  strClean($_POST['txtNombre']);
				$strDescipcion = strClean($_POST['txtDescripcion']);
				$intStatus = intval($_POST['listStatus']);

				if($intIdrol == 0)
				{
					//Crear
					$request_rol = $this->model->insertRol($strRol, $strDescipcion,$intStatus);
					$option = 1;
				}else{
					//Actualizar
					$request_rol = $this->model->updateRol($intIdrol, $strRol, $strDescipcion, $intStatus);
					$option = 2;
				}

				if($request_rol > 0 )
				{
					if($option == 1)
					{
						$arrResponse = array('status' => true, 'msg' => 'Datos registrados correctamente.');
					}else{
						$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
					}
				}else if($request_rol == '0'){
					
					$arrResponse = array('status' => false, 'msg' => '¡Atención! El Rol ya existe.');
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function delRol()
		{
			if($_POST)
			{
				if($_SESSION['permisosMod']['d'])
				{
					$intIdrol = intval($_POST['idRol']);
					$requestDelete = $this->model->deleteRol($intIdrol);
					if($requestDelete > 0)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el Rol');
					}else if($requestDelete == '0'){
						$arrResponse = array('status' => false, 'msg' => 'No es posible eliminar un Rol asociado a usuarios.');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Rol.');
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

    }