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

	//INSERTA EL PAGAMENTO
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
				$usuario = $_SESSION['idUser'];

				//VALIDA SI HAY UN RESUMEN, Si NO, LO CREA.
				setDelResumenActual('set');
				$request_pago = $this->model->insertPago($idPrestamo,$intMonto, $usuario);

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

	//TRAE LOS TODOS LOS PAGAMENTOS Y LOS DEVUELVE EN UNA ETIQUETA "<tr>"
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
							if($arrData[$i]['hora'] != '00:00:00') {
								$arrPagos .= '<td>'.date('H:i', strtotime($arrData[$i]['hora'])).'</td>';
							} else {
								$arrPagos .= '<td></td>';
							}
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

	//ELIMINAR PAGO
	public function delPago()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$intIdprestamo = intval($_POST['idPrestamo']);
				$intIdPago = intval($_POST['idPago']);
				$usuario = $_SESSION['idUser'];

				$requestDelete = $this->model->deletePago($intIdprestamo, $intIdPago);
				
				if($requestDelete)
				{
					//TRAE LA SUMA DE LOS PAGAMENTOS
                    $sumaPagamentos = $this->model->sumaPagamentosFechaActual()['sumaPagos'];

                    //ACTUALIZA LA COLUMNA "COBRADO" DE LA TABLA RESUMEN
                    setUpdateResumen($usuario, $sumaPagamentos, 2);

					//ELIMINA EL RESUMEN SI LA BASE, EL COBRADO, LAS VENTAS, Y LOS GASTOS ESTÁN NULLOS
					setDelResumenActual('del');

					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el pago.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Pago.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}
}