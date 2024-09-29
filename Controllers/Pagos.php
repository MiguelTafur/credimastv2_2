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

	public function getPagos()
	{
		if($_SESSION['permisosMod']['r']){
			$idprestamo = intval($_POST['idPrestamo']);
			if($idprestamo > 0)
			{
				$arrData = $this->model->selectPagamentos($idprestamo);
		
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Sin pagamentos.');
				}else{
					$arrPagos = "";
					for ($i=0; $i < count($arrData); $i++)
					{ 
						$fechaF = date("d-m-Y", strtotime($arrData[$i]['datecreated']));
						/*$dia = $dias[date('w', strtotime($arrData[$i]['datecreated']))];*/
						$arrPagos .= '
						<tr class="text-center">';
							$arrPagos .= '<td>'.$fechaF.'</td>';
							$arrPagos .= '<td>'.$arrData[$i]['hora'].'</td>';
							$arrPagos .= '<td>'.$arrData[$i]['abono'].'</td>';
							$arrPagos .= '</tr>';
					}
					$arrResponse = array('status' => true, 'pagos' => $arrPagos);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}
}