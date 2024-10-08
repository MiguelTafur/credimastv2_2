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
	public function setBase()
    {
        if($_POST)
        {
            if($_POST['txtValor'] < 0)
            {
                $arrResponse = array("status" => false, "msg" => "No puede registrar base negativa.");
            }else{
                $idBase = intval($_POST['idBase']);
                $intValor = intval($_POST['txtValor']);
                $strObservacion =  ucwords(strClean($_POST['txtObservacion']));
				$ruta = $_SESSION['idRuta'];
        		$usuario = $_SESSION['idUser'];
                $request_user = "";

				//VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
				$fechaBase = setDelResumenActual('set', $ruta)['datecreated'] ?? NOWDATE;

                if($idBase === 0)
                {
                    $option = 1;
                    if($_SESSION['permisosMod']['w']){
                        $request_user = $this->model->insertBase($usuario,$ruta,$intValor,$strObservacion,$fechaBase);
                    }
                }else{
                        $option = 2;
                        if($_SESSION['permisosMod']['u']){
                        $request_user = $this->model->updateGasto($idBase,$strObservacion,$intValor,$ruta,$fechaBase);
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
                } else if($request_user == '0'){
                    $arrResponse = array("status" => false, "msg" => 'Base ya ingresada.');
                }else{
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }	
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    //REGISTRAR BASE
	public function setBaseResumenAnterior()
    {
        if($_POST)
        {
            $intValor = intval($_POST['base']);
            $strObservacion = '';
            $ruta = $_SESSION['idRuta'];
            $usuario = $_SESSION['idUser'];
            $request_user = "";

            //VALIDA SI HAY UN RESUMEN Y DEVUELVE LA FECHA, Si NO, LO CREA.
            $fechaBase = setDelResumenActual('set', $ruta)['datecreated'] ?? NOWDATE;

            if($_SESSION['permisosMod']['w']){
                $request_user = $this->model->insertBase($usuario,$ruta,$intValor,$strObservacion,$fechaBase);
            }

            if($request_user > 0)
            {
                $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
            } else if($request_user == '0'){
                $arrResponse = array("status" => false, "msg" => 'Base ya ingresada.');
            }else{
                $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
            }

            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getBase()
    {
        if($_POST)
        {
            $ruta = $_POST['idRuta'];

            $base = $this->model->selectBase($ruta);
            
            $arrResponse = array('status' => true, 'base' => $base);

            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}