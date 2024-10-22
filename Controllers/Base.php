<?php 

class Base extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

    //REGISTRAR BASE
	public function setBaseResumenAnterior()
    {
        if($_POST)
        {
            $intValor = intval($_POST['base']);
            $ruta = $_SESSION['idRuta'];
            $usuario = $_SESSION['idUser'];
            $request_user = "";

            //VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
            $fechaBase = setDelResumenActual('set', $ruta)['datecreated'] ?? NOWDATE;

            if($_SESSION['permisosMod']['w']){
                $request_user = $this->model->insertBase($usuario,$ruta,$intValor,$fechaBase);
            }

            if($request_user > 0)
            {
                /** DASHBOARD DEL RESUMEN **/
                $cajaResumen = getResumenAnterior1();
                $carteraResumen = valorActivoYEstimadoPrstamos()['valorActivo'] + $cajaResumen;
                $ultimosResumenes = getUltimosResumenes();

                $arrResponse = array('status' => true, 
                                     'msg' => 'Datos guardados correctamente.', 
                                     'idbase' => $request_user,
                                     'cajaResumen' => $cajaResumen,
									 'carteraResumen' => $carteraResumen,
									 'ultimosResumenes' => $ultimosResumenes);
            } else if($request_user == '0'){
                $arrResponse = array("status" => false, "msg" => 'Base ya ingresada.');
            }else{
                $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
            }

            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    //TRAE LA BASE
    public function getBase()
    {
        if($_POST)
        {
            $ruta = intval($_POST['idRuta']);

            $base = $this->model->selectBase($ruta);
            
            $arrResponse = array('status' => true, 'base' => $base);

            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    //TRAE LA BASE
    public function setBaseUpdate()
    {
        if($_POST)
        {
            $usuario = $_SESSION['idUser'];
            $ruta = $_SESSION['idRuta'];
            $monto = intval($_POST['txtValor']);

            //VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
            $estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

            if($estadoResumen === 0)
            {
                //VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
                $fechaBase = setDelResumenActual('set', $ruta)['datecreated'] ?? NOWDATE;

                //ACTUALIZA LA BASE
                $base = $this->model->updateBase($usuario, $ruta, $monto, $fechaBase);

                //TRAE LOS DATOS DEL RESUMEN
                $arrResumen = getResumenActual($fechaBase);

                //TRAE LOS DATOS DEL DASHBOARD DEL RESUMEN
                $cajaResumen = $arrResumen['total'];
                $carteraResumen = valorActivoYEstimadoPrstamos()['valorActivo'] + $cajaResumen;
                $ultimosResumenes = getUltimosResumenes();

                $arrResponse = array('status' => true, 
                                     'msg' => 'Base actualizada',
                                     'resumen' => $arrResumen,
                                     'cajaResumen' => $cajaResumen,
									 'carteraResumen' => $carteraResumen,
									 'ultimosResumenes' => $ultimosResumenes);

                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible editar la Base.');
            }
        }
        die();
    }

    //ELIMINAR BASE
	public function delBase()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$intIdBase = intval($_POST['idBase']);
                $ruta = $_SESSION['idRuta'];

				//VERIFICANDO SI HAY UN RESUMEN CON EL ESTADO 1
				$estadoResumen = getResumenActual1($ruta)['status'] ?? 0;

				if($estadoResumen === 0)
				{	
					$requestDelete = $this->model->deleteBase($intIdBase, $ruta);
					if($requestDelete > 0)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la Base.');
					} 
					else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar la Base.');
					}
				} else {
					$arrResponse = array('status' => false, 'msg' => 'Resumen finalizado. No es posible eliminar la Base.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}
}   