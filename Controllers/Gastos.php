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
				
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';

				if($_SESSION['permisosMod']['r']){
					$btnView = '<button class="btn btn-secondary btn-sm me-1" onClick="fntViewInfo('.$arrData[$i]['idgasto'].')" title="Ver Gasto"><i class="bi bi-person-vcard-fill me-0"></i></button>';
				}
				if($_SESSION['permisosMod']['u']){
					$btnEdit = '<button class="btn btn-warning btn-sm me-1" onClick="fntEditInfo('.$arrData[$i]['idgasto'].')" title="Editar Gasto"><i class="bi bi-pencil-square me-0"></i></button>';
				}
				if($_SESSION['permisosMod']['d']){
					$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idgasto'].')" title="Eliminar Gasto"><i class="bi bi-trash3-fill me-0"></i></button>';
				}

				$arrData[$i]['options'] = '<div class="text-center d-flex justify-content-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
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
                $request_user = "";

                if($idGasto === 0)
                {
                    $option = 1;
                    if($_SESSION['permisosMod']['w']){
						//VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
						$fechaGasto = setDelResumenActual('set')['datecreated'] ?? NOWDATE;
                        $request_user = $this->model->insertGasto($_SESSION['idUser'],$strNombre,$intValor, $fechaGasto, $_SESSION['idRuta']);
                    }
                }else{
                        $option = 2;
                        if($_SESSION['permisosMod']['u']){
                        $request_user = $this->model->updateGasto($idGasto,$intValor,$strNombre);
                        }
                    }

                if($request_user > 0)
                {
                    if($option === 1){
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                    }else{
                        $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                    }
                }else{
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }	
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

}