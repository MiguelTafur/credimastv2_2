<?php 

class PagosModel extends Mysql
{
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdPago;
    PRIVATE $intIdRuta;

    public function __construct()
    {
        parent::__construct();
    }

    //TRAE LA SUMA DE LOS TODOS PAGAMENTOS DEL PRÉSTAMO
    public function sumaPagamentos(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT SUM(abono) as sumaPagos FROM pagos WHERE prestamoid = $this->intIdPrestamo";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE LA SUMA DE LOS TODOS PAGAMENTOS(CON LA FECHA ACTUAL) DEL PRÉSTAMO
    public function sumaPagamentosFechaActual()
    {
        $ruta = $_SESSION['idRuta'];
        $sql = "SELECT SUM(pa.abono) as sumaPagos FROM pagos pa LEFT OUTER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
                LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $ruta AND pr.status != 0 AND pa.datecreated = '" . NOWDATE . "'";
        $request = $this->select($sql);
        return $request;
    }

    public function selectPagamentos(int $idprestamo) 
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

    public function insertPago(int $idprestamo, int $pago, int $usuario)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intPago = $pago;
        $return = 0;

        //VALIDAR PAGAMENTOS REPETIDOS
        $sql = "SELECT * FROM pagos WHERE prestamoid = '{$this->intIdPrestamo}' AND datecreated = '" . NOWDATE . "'";
        $request = $this->select_all($sql);
        if(empty($request))
        {
            //TRAE EL SALDO DEL PRESTAMO
            $saldo = saldoPrestamo($idprestamo);

            //VALIDAR QUE EL PAGAMENTO NO SEA MAYOR AL SALDO DEL PRESTAMO
            if($saldo >= $this->intPago)
            {
                //INSERTAR PAGAMENTO
                $query_insert = "INSERT INTO pagos(prestamoid,abono,hora,datecreated) VALUES(?,?,?,?)";
                $arrData = array($this->intIdPrestamo,$this->intPago,NOWTIME,NOWDATE);
                $request_insert = $this->insert($query_insert,$arrData);
                $return = $request_insert;

                if($return > 0)
                {
                    //TRAE EL SALDO DEL PRESTAMO
                    $saldo = saldoPrestamo($idprestamo);
                    $estado = $saldo > 0 ? 1 : 2;

                    //ACTUALIZA EL STATUS DEL PRÉSTAMO SI EL ES SALDO ES 0
                    if($estado == 2){
                        $query_update = "UPDATE prestamos SET datefinal = ?, status = ? WHERE idprestamo = $this->intIdPrestamo";
                        $arrData = array(NOWDATE, $estado);
                        $request = $this->update($query_update,$arrData);
                        $return = $request;
                    }

                    //TRAE LA SUMA DE LOS PAGAMENTOS
                    $sumaPagamentos = $this->sumaPagamentosFechaActual()['sumaPagos'];

                    //ACTUALIZA LA COLUMNA "COBRADO" DE LA TABLA RESUMEN
                    setUpdateResumen($usuario, $sumaPagamentos, 2);
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
        
        return $return;
    }
}