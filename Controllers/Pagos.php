<?php 

class Pagos extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

    public function setPago()
    {
        if($_POST)
		{
			if(empty($_POST['pagoPrestamo']))
			{
				$arrResponse = array("status" => false, "msg" => "Debes ingresar un valor.");
			}else
			{
				$idPrestamo = intval(($_POST['idPrestamo']));
				$intMonto = intval($_POST['pagoPrestamo']);
                /*
				if(!empty($_POST['fechaAnterior']))
				{
					$fecha_actual = $_POST['fechaAnterior'];
				}else{
					$fecha_actual = date("Y-m-d");
				}
                */

				$request_pago = $this->model->insertPago($idPrestamo,$intMonto);

                if($request_pago > 0)
                {
                    $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');

                }else if($request_pago == '0')
                {
                    $arrResponse = array("status" => false, "msg" => "Pago ya realizado.");	
                }else if($request_pago == '!')
                {
                    $arrResponse = array("status" => false, "msg" => "El pago ingresado no puede ser mayor al saldo.");	
                }else
                {
                    $arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
                }

			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
    }
}