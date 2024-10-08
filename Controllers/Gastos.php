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
						$idresumen = setDelResumenActual('set', $ruta)['idresumen'];
						if($option === 1){
							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.', 'idresumen' => $idresumen);
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.', 'idresumen' => $idresumen);
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
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

}