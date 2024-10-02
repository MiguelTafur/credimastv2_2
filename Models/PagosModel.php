<?php 

class PagosModel extends Mysql
{
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdPago;
    PRIVATE $intPago;
    PRIVATE $strFecha;

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
    public function sumaPagamentosFechaActual(string $fecha)
    {
        $ruta = $_SESSION['idRuta'];
        $this->strFecha = $fecha;

        $sql = "SELECT SUM(pa.abono) as sumaPagos FROM pagos pa LEFT OUTER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
                LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $ruta AND pr.status != 0 AND pa.datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE TODOS LOS PAGAMENTOS DEL PRÉSTAMO
    public function selectPagamentos(int $idprestamo) 
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT idpago, abono, hora, datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    //TRAE SOLO UN PAGAMENTO DEL PRÉSTAMO
    public function selectUltimoPagamento(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT idpago, abono, hora, datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
        $request = $this->select($sql);
        return $request;
    }

    //REGSITRA UN PAGAMENTO
    public function insertPago(int $idprestamo, int $pago, int $usuario, string $fecha = NULL)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intPago = $pago;
        $this->strFecha = $fecha ?? NOWDATE;
        $return = 0;

        //VALIDAR PAGAMENTOS REPETIDOS
        $sql = "SELECT * FROM pagos WHERE prestamoid = '{$this->intIdPrestamo}' AND datecreated = '{$this->strFecha}'";
        $request = $this->select_all($sql);
        if(empty($request))
        {
            //TRAE EL SALDO DEL PRESTAMO
            $saldo = saldoPrestamo($idprestamo);

            //VALIDA QUE EL PAGAMENTO NO SEA MAYOR AL SALDO DEL PRESTAMO
            if($saldo >= $this->intPago)
            {
                //INSERTA EL PAGAMENTO
                $query_insert = "INSERT INTO pagos(prestamoid,abono,hora,datecreated) VALUES(?,?,?,?)";
                $arrData = array($this->intIdPrestamo,$this->intPago,NOWTIME,$this->strFecha);
                $request_insert = $this->insert($query_insert,$arrData);
                $return = $request_insert;

                if($return > 0)
                {
                    //TRAE EL SALDO DEL PRESTAMO
                    $saldo = saldoPrestamo($idprestamo);
                    $estado = $saldo > 0 ? 1 : 2;

                    //ACTUALIZA EL STATUS DEL PRÉSTAMO SI EL SALDO ES 0
                    if($estado == 2){
                        $query_update = "UPDATE prestamos SET datefinal = ?, status = ? WHERE idprestamo = $this->intIdPrestamo";
                        $arrData = array(NOWDATE, $estado);
                        $request = $this->update($query_update,$arrData);
                        $return = $request;
                    }

                    //TRAE LA SUMA DE LOS PAGAMENTOS DEL PRÉSTAMO
                    $sumaPagamentos = $this->sumaPagamentosFechaActual($this->strFecha)['sumaPagos'];

                    //ACTUALIZA LA COLUMNA "COBRADO" DE LA TABLA RESUMEN
                    setUpdateResumen($usuario, $sumaPagamentos, 2, $this->strFecha);
                }
            }else {
                //PAGAMENTO REPETIDO
                $return = '!';
            }
        }else {
            //ERROR AL REGISTRAR PAGAMENTO
            $return = '0';
        }
        return $return;
    }

    //ELIMINA EL PAGAMENTO
    public function deletePago(int $idprestamo, int $idpago, int $usuarios)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intIdPago = $idpago;
        $ruta = $_SESSION['idRuta'];

        //TRAE LA FECHA DEL PAGAMENTO
        $sql = "SELECT datecreated FROM pagos WHERE idpago = $this->intIdPago";
        $requestDate = $this->select($sql);

        //ELIMINA EL PAGAMENTO
        $query_delete = "DELETE FROM pagos WHERE idpago = $this->intIdPago";
        $request = $this->delete($query_delete);
        if(!empty($request)){
            //ACTUALIZA EL PRÉSTAMO
            $query_update = "UPDATE prestamos SET datefinal = ?, status = ? WHERE idprestamo = $this->intIdPrestamo";
            $arrData = array(NULL,1);
            $requestU = $this->update($query_update, $arrData);
            $return = $requestU;

            //TRAE LA SUMA DE LOS PAGAMENTOS DEL PRÉSTAMO
            $sumaPagamentos = $this->sumaPagamentosFechaActual($requestDate['datecreated'])['sumaPagos'];

            //ACTUALIZA LA COLUMNA "COBRADO" DE LA TABLA RESUMEN
            setUpdateResumen($usuario, $sumaPagamentos, 2, $requestDate['datecreated']);

        }else{
            //ERROR AL REGISTRAR EL PAGAMENTO
            $return = "0";
        }
        
        return $return;
    }
}