<?php 

class PagosModel extends Mysql
{
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdPago;

    public function __construct()
    {
        parent::__construct();
    }

    public function sumaPagamentos(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT SUM(abono) as sumaPagos FROM pagos WHERE prestamoid = $this->intIdPrestamo";
        $request = $this->select($sql);
        return $request;
    }

    public function selectPagamentos($idprestamo) 
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT idpago, abono, hora, datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectUltimoPagamento(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT idpago, abono, hora, datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
        $request = $this->select($sql);
        return $request;
    }

    public function insertPago(int $idprestamo, int $pago)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intPago = $pago;
        $return = 0;

        //VALIDAR PAGAMENTOS REPETIDOS
        $sql = "SELECT * FROM pagos WHERE prestamoid = '{$this->intIdPrestamo}' AND datecreated = '" . NOWDATE . "'";
        $request = $this->select_all($sql);

        if(empty($request))
        {
            //VALIDAR QUE EL PAGAMENTO NO SEA MAYOR AL TOTAL DEL PRESTAMO
            $saldo = saldoPrestamo($idprestamo);

            if($saldo >= $this->intPago)
            {
                //INSERTAR PAGAMENTO
                $query_insert = "INSERT INTO pagos(prestamoid,abono,hora,datecreated) VALUES(?,?,?,?)";
                $arrData = array($this->intIdPrestamo,$this->intPago,NOWTIME,NOWDATE);
                $request_insert = $this->insert($query_insert,$arrData);
                $return = $request_insert;

                if($return > 0)
                {
                    //ACTUALIZANDO EL STATUS DEL PRÉSTAMO SI EL ESALDO ES 0
                    $saldo = saldoPrestamo($idprestamo);
                    $estado = $saldo > 0 ? 1 : 2;

                    if($estado == 2){
                        $query_update = "UPDATE prestamos SET datefinal = ?, status = ? WHERE idprestamo = $this->intIdPrestamo";
                        $arrData = array(NOWDATE, $estado);
                        $request = $this->update($query_update,$arrData);
                        $return = $request;
                    }
                }
            }else {
                $return = '!';
            }
        }else {
            $return = '0';
        }
        return $return;
    }

    public function deletePago(int $idprestamo, int $idpago)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intIdPago = $idpago;
        $ruta = $_SESSION['idRuta'];
        $fecha_actual = date("Y-m-d");

        $sqlR = "SELECT * FROM resumen WHERE idruta = $ruta AND datecreated = '{$fecha_actual}'";
        $request = $this->select_all($sqlR);
        if(empty($request))
        {
            $sql = "DELETE FROM pagos WHERE idpago = $this->intIdPago";
            $request = $this->delete($sql);
            if(!empty($request)){
                $sqlU = "UPDATE prestamos SET datefinal = ?, status = ? WHERE idprestamo = $this->intIdPrestamo";
                $arrData = array(NULL,1);
                $requestU = $this->update($sqlU, $arrData);
                $return = $requestU;
            }else{
                $return = "0";
            }
        }else{
            $return = "0";
        }
        
        return $return;
    }
}