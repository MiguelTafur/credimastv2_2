<?php

class PagosModel extends Mysql
{
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdPago;
    PRIVATE $intIdRuta;
    PRIVATE $intIdUsuario;
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

    //TRAE LA SUMA DE TODOS LOS PAGAMENTOS(CON LA FECHA ACTUAL O DE TODAS LAS FECHAS) DE LOS PRÉSTAMO
    public function sumaPagamentos2(string $fecha = NULL, int $ruta)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $whereFecha = "";
        $whereStatus2 = " AND pr.status = 1";

        if($this->strFecha != NULL)
        {
            $whereFecha = " AND pa.datecreated = " . "'{$this->strFecha}'";
            $whereStatus2 = " AND pr.status != 0";
        }

        $sql = "SELECT SUM(pa.abono) as sumaPagos FROM pagos pa
                LEFT OUTER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
                WHERE pr.codigoruta = $this->intIdRuta " . $whereStatus2 . $whereFecha;
        $request = $this->select($sql);
        return $request;
    }

    //TRAE TODOS LOS PAGAMENTOS DEL PRÉSTAMO
    public function selectPagamentos(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;
        $sql = "SELECT  pa.idpago, 
                        (SELECT nombres FROM persona WHERE idpersona = pa.personaid) as personaid, 
                        pa.abono, 
                        pa.hora, 
                        pa.datecreated,
                        pe.nombres,
                        pe.apellidos
                FROM pagos pa 
                LEFT OUTER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
                LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona)
                WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    //TRAE TODOS LOS PAGAMENTOS DE LA FECHA CORRESPONDIENTE
    public function selectPagamentosFecha(string $fecha, int $ruta)
    {
        $this->strFecha = $fecha;
        $this->intIdRuta = $ruta;

        $sql = "SELECT pe.nombres, pe.apellidos, pa.abono, pa.hora, (SELECT nombres FROM persona WHERE idpersona = pa.personaid) as usuario FROM pagos pa
                LEFT OUTER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
                LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona)
                WHERE pa.datecreated = '{$this->strFecha}' AND pr.codigoruta = $this->intIdRuta";
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
    public function insertPago(int $idprestamo, int $pago, int $usuario, int $ruta, string $fecha = NULL)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intIdUsuario = $usuario;
        $this->intIdRuta = $ruta;
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
                $query_insert = "INSERT INTO pagos(prestamoid,personaid,abono,hora,datecreated) VALUES(?,?,?,?,?)";
                $arrData = array($this->intIdPrestamo,$this->intIdUsuario,$this->intPago,NOWTIME,$this->strFecha);
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
                        $arrData = array($this->strFecha, $estado);
                        $request = $this->update($query_update,$arrData);
                    }

                    //TRAE LA SUMA DE LOS PAGAMENTOS DEL PRÉSTAMO
                    $sumaPagamentos = $this->sumaPagamentos2($this->strFecha, $this->intIdRuta)['sumaPagos'];

                    //ACTUALIZA LA COLUMNA "COBRADO" DE LA TABLA RESUMEN
                    setUpdateResumen($this->intIdRuta, $sumaPagamentos, 2, $this->strFecha);

                    //ACTUALIZA LA COLUMNA "BASE" DE LA TABLA RESUMEN
                    $base = getBase($this->strFecha);
					if($base == NULL)
					{
						$monto = getResumenAnterior1();
						setBase($monto);
                        setUpdateResumen($this->intIdRuta, $monto, 1, $this->strFecha);
					}
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
    public function deletePago(int $idprestamo, int $idpago, int $ruta)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intIdPago = $idpago;
        $this->intIdRuta = $ruta;

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
            $sumaPagamentos = $this->sumaPagamentos2($requestDate['datecreated'], $this->intIdRuta)['sumaPagos'];

            //ACTUALIZA LA COLUMNA "COBRADO" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $sumaPagamentos, 2, $requestDate['datecreated']);

        }else{
            //ERROR AL REGISTRAR EL PAGAMENTO
            $return = "0";
        }

        return $return;
    }

    /*** GRÁFICAS ***/
    //MENSUAL COBRADO
    public function selectCobradoMes(int $anio, int $mes)
	{
		$totalCobradoMes = 0;
		$arrVentasDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaVenta = date_format($date, "Y-m-d");
			$sql = "SELECT DAY(datecreated) as dia, cobrado FROM resumen WHERE DATE(datecreated) = '$fechaVenta' AND codigoruta = $rutaId";
			$ventaDia = $this->select($sql);

			$sqlTotal = "SELECT SUM(cobrado) as total FROM resumen WHERE DATE(datecreated) = '$fechaVenta' AND codigoruta = $rutaId";
			$cobradoDiaTotal = $this->select($sqlTotal);
			$cobradoDiaTotal = $cobradoDiaTotal['total'];

			$ventaDia['dia'] = $n_dia;
			$ventaDia['cobrado'] = $cobradoDiaTotal;
			$ventaDia['cobrado'] = $ventaDia['cobrado'] == "" ? 0 : $ventaDia['cobrado'];
			$totalCobradoMes += $cobradoDiaTotal;
			array_push($arrVentasDias, $ventaDia);
			$n_dia++;

		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'numeroMes' => $mes, 'total' => $totalCobradoMes, 'totalCobrado' => $arrVentasDias);
		return $arrData;
	}

    //ANUAL COBRADO
    public function selectCobradoAnio(string $anio) {
		$arrMCobrado = array();
		$arrMeses = Meses();
		$totalCobrado = 0;
		$ruta = $_SESSION['idRuta'];

		for ($i=1; $i <= 12; $i++) {
			$arrData = array('anio' => '', 'no_mes' => '', 'mes' => '');
			$sql = "SELECT $anio AS anio, $i AS mes, sum(cobrado) AS cobrado FROM resumen
					WHERE month(datecreated) = $i AND year(datecreated) = $anio AND codigoruta = $ruta
					GROUP BY month(datecreated)";
			$cobradoMes = $this->select($sql);
			$arrData['mes'] = $arrMeses[$i-1];

			if(empty($cobradoMes)){
				$arrData['anio'] = $anio;
				$arrData['no_mes'] = $i;
				$arrData['cobrado'] = 0;
			}else{
				$arrData['anio'] = $cobradoMes['anio'];
				$arrData['no_mes'] = $cobradoMes['mes'];
				$arrData['cobrado'] = $cobradoMes['cobrado'];
				$totalCobrado += $cobradoMes['cobrado'];
			}
			array_push($arrMCobrado, $arrData);
		}

		$arrCobrado = array('totalCobrado' => $totalCobrado, 'anio' => $anio, 'meses' => $arrMCobrado);
		return $arrCobrado;
	}
}