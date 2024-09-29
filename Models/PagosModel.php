<?php 

class PagosModel extends Mysql
{
    PRIVATE $intIdPrestamo;
    PRIVATE $intPago;
    PRIVATE $intStatus;
    

    public function __construct()
    {
        parent::__construct();
    }

    public function sumaPagamentos(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT SUM(abono) as sumaPagos FROM pagos WHERE prestamoid = '{$this->intIdPrestamo}'";
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

            if($saldo > $this->intPago)
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
                        $arrData = array(NOWDATE, $this->intStatus);
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
}