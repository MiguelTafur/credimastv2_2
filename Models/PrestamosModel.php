<?php 

class PrestamosModel extends Mysql
{
    PRIVATE $intIdRuta;
    PRIVATE $intIdUsuario;
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdCliente;
    PRIVATE $intMonto;
    PRIVATE $intFormato;
    PRIVATE $intPlazo;
    PRIVATE $intTaza;
    PRIVATE $strObservacion;
    PRIVATE $strFecha;
    PRIVATE $strFecha2;
    PRIVATE $strVence;

    public function __construct()
    {
        parent::__construct();
    }

    //TRAE TODOS LOS PRÉSTAMOS
    public function selectPrestamos(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $resumenAnterior = getResumenAnterior()['datecreated'] ?? NOWDATE;

        $sql = "SELECT 
                    pr.idprestamo, 
                    pr.personaid,
                    pe.nombres,
                    pe.apellidos,
                    pr.monto,
                    pr.formato,
                    pr.taza,
                    pr.plazo,
                    pr.hora,
                    pr.datecreated,
                    pr.fechavence,
                    pr.datefinal,
                    pr.status
                FROM prestamos pr 
                INNER JOIN persona pe 
                ON (pr.personaid = pe.idpersona)
                WHERE (pe.codigoruta = $this->intIdRuta and pr.status = 1) or (pe.codigoruta = $this->intIdRuta AND pr.status = 2 and pr.datefinal = '" . $resumenAnterior . "') ORDER BY pr.datecreated ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    //TRAE TODOS LOS PRÉSTAMOS DEPENDIENDO DE LA FECHA
    public function selectPrestamosFecha(int $ruta, string $fecha = NULL)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $whereFecha = "";
        $whereStatus2 = " AND pr.status = 1";

        if($this->strFecha != NULL)
        {
            $whereFecha = " AND pr.datecreated = " . "'{$this->strFecha}'";
            $whereStatus2 = " AND pr.status != 0";
        }

        $sql = "SELECT pe.nombres, 
                        pe.apellidos, 
                        (SELECT nombres FROM persona WHERE idpersona = pr.usuarioid) as usuario, 
                        pr.monto, 
                        pr.formato, 
                        pr.plazo, 
                        pr.taza, 
                        pr.hora
                FROM prestamos pr 
                LEFT OUTER JOIN persona pe 
                ON (pr.personaid = pe.idpersona)
                WHERE pr.codigoruta = $this->intIdRuta " . $whereStatus2 . $whereFecha. " ORDER BY pr.hora DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    //TRAE UN PRÉSTAMO EN ESPECÍFICO
    public function selectPrestamo(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;

        $sql = "SELECT pr.idprestamo, 
                        pr.personaid,
                        pr.monto,
                        pr.formato,
                        pr.plazo,
                        pr.taza,
                        pr.observacion,
                        pr.datecreated,
                        pe.nombres,
                        pe.apellidos
                FROM prestamos pr LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona) 
                WHERE pr.idprestamo = $this->intIdPrestamo";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE LA SUMA DE LOS PRÉSTAMOS SEGÚN FECHA
    public function sumaPrestamos(int $ruta, string $fecha)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $sql = "SELECT SUM(monto) as sumaPrestamos FROM prestamos WHERE codigoruta = $this->intIdRuta AND status != 0 AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //REGISTRA EL PRÉSTAMO
    public function insertPrestamo(int $cliente, int $monto, int $taza, int $plazo, int $formato, string $observacion, string $fecha, string $vence, int $usuario, int $ruta)
    {
        $this->intIdCliente = $cliente;
        $this->intIdUsuario = $usuario;
        $this->intIdRuta = $ruta;
        $this->intMonto = $monto;
        $this->intTaza = $taza;
        $this->intFormato = $formato;
        $this->intPlazo = $plazo;
        $this->strObservacion = $observacion;
        $this->strFecha = $fecha;
        $this->strVence = $vence;
        
        $return = 0;

        //INSERTA EL PRESTAMO
        $query_insert = "INSERT INTO prestamos(personaid,codigoruta,usuarioid,monto,formato,plazo,taza,observacion,hora,datecreated,fechavence) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        $arrData = array($this->intIdCliente,
                        $this->intIdRuta,
                        $this->intIdUsuario,
                        $this->intMonto,
                        $this->intFormato,
                        $this->intPlazo,
                        $this->intTaza,
                        $this->strObservacion,
                        NOWTIME,
                        $this->strFecha,
                        $this->strVence);
        $request_insert = $this->insert($query_insert,$arrData);

        if(!empty($request_insert))
        {
            //TRAE LA SUMA DE LOS PRESTAMOS
            $sumaPrestamos = $this->sumaPrestamos($this->intIdRuta, $this->strFecha)['sumaPrestamos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            $updateResumen = setUpdateResumen($this->intIdRuta, $sumaPrestamos, 3, $this->strFecha);

            $return = $updateResumen;

        }else {
            $return = "0";    
        }

        return $return;
    }

    //ACTUALIZA UN PRÉSTAMOS
    public function updatePrestamo(int $idprestamo ,int $monto, int $taza, int $plazo, int $formato, string $observacion, string $fechaprestamo, string $vence, int $ruta)      
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intIdRuta = $ruta;
        $this->intMonto = $monto;
        $this->intTaza = $taza;
        $this->intFormato = $formato;
        $this->intPlazo = $plazo;
        $this->strObservacion = $observacion;
        $this->strFecha = $fechaprestamo;
        $this->strVence = $vence;

        $sql = "UPDATE prestamos SET monto = ?, taza = ?, plazo = ?, formato = ?, observacion = ?, fechavence = ? WHERE idprestamo = $this->intIdPrestamo";
        $arrData = array($this->intMonto,$this->intTaza,$this->intPlazo,$this->intFormato,$this->strObservacion,$this->strVence);
        $request = $this->update($sql, $arrData);

        if(!empty($request))
        {
            //TRAE LA SUMA DE LOS PRESTAMOS
            $sumaPrestamos = $this->sumaPrestamos($this->intIdRuta, $this->strFecha)['sumaPrestamos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $sumaPrestamos, 3, $this->strFecha);
        }

        return $request;
    }

    //ELIMINA EL PRÉSTAMO
    public function deletePrestamo(int $idprestamo, int $ruta)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intIdRuta = $ruta;
        $return = 0;

        //TRAE LA FECHA
        $fechaPrestamo = $this->selectPrestamo($this->intIdPrestamo)['datecreated'];

        //VERIFICA SI HAY PAGAMENTOS ASOCIADOS AL PRÉSTAMO
        $pagamento = getUltimoPagamento($idprestamo);
        $pagamento = explode("|", $pagamento);

        if(empty($pagamento[1]))
        {
            $sql = "DELETE FROM prestamos WHERE idprestamo = $this->intIdPrestamo";
            $request = $this->delete($sql);

            if(!empty($request))
            {
                //TRAE LA SUMA DE LOS PRESTAMOS
                $sumaPrestamos = $this->sumaPrestamos($this->intIdRuta, $fechaPrestamo)['sumaPrestamos'];

                //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
                setUpdateResumen($this->intIdRuta, $sumaPrestamos, 3, $fechaPrestamo);
            } 

            $return = $request;
        } else {
            $return = '0';
        }
        return $return;
    }

    /***** GRÁFICAS *****/
	//MENSUAL
	public function selectPrestamosMes(string $anio, string $mes)
	{
		$totalPrestamosMes = 0;
		$arrPrestamosDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaPrestamo = date_format($date, "Y-m-d");
		
			$sql = "SELECT DAY(datecreated) as dia FROM prestamos WHERE DATE(datecreated) = '{$fechaPrestamo}' AND codigoruta = $rutaId";
			$prestamoDia = $this->select($sql);

			$sqlTotal = "SELECT SUM(monto) as total FROM prestamos WHERE DATE(datecreated) = '{$fechaPrestamo}' AND codigoruta = $rutaId";
			$prestamoDiaTotal = $this->select($sqlTotal);
			$prestamoDiaTotal = $prestamoDiaTotal['total'];

			$prestamoDia['dia'] = $n_dia;
			$prestamoDia['prestamo'] = $prestamoDiaTotal;
			$prestamoDia['prestamo'] = $prestamoDia['prestamo'] == "" ? 0 : $prestamoDia['prestamo'];
			$totalPrestamosMes += $prestamoDiaTotal;
			array_push($arrPrestamosDias, $prestamoDia);
			$n_dia++;

		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'numeroMes' => $mes, 'total' => $totalPrestamosMes, 'prestamos' => $arrPrestamosDias);
		return $arrData;
	}

    //INFORMACIÓN DE CADA PUNTO DE LA GRÁFICA DE PRÉSTAMOS
	public function datosGraficaPrestamo(string $fecha) 
	{
		$this->strFecha = $fecha;
		$rutaId = $_SESSION['idRuta'];

		$sql = "SELECT (SELECT nombres FROM persona WHERE idpersona = pr.usuarioid) as usuario, pr.monto, pe.nombres, pr.hora, DATE_FORMAT(pr.datecreated, '%d-%m-%Y') as fecha 
				FROM prestamos pr LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona) 
				WHERE pr.datecreated = '{$this->strFecha}' AND pr.codigoruta = $rutaId";
		$request = $this->select_all($sql);

		return $request;
	}

    //ANUAL
	public function selectPrestamosAnio(string $anio) 
    {
		$arrMPrestamos = array();
		$arrMeses = Meses();
		$totalPrestamos = 0;
		$ruta = $_SESSION['idRuta'];

		for ($i=1; $i <= 12; $i++) {
			$arrData = array('anio' => '', 'no_mes' => '', 'mes' => '');
			$sql = "SELECT $anio AS anio, $i AS mes, SUM(monto) AS total
					FROM prestamos 
					WHERE month(datecreated) = $i 
					AND year(datecreated) = $anio  
					AND codigoruta = $ruta
					GROUP BY month(datecreated)";
			$prestamoMes = $this->select($sql);
			$arrData['mes'] = $arrMeses[$i-1];

			if(empty($prestamoMes)){
				$arrData['anio'] = $anio;
				$arrData['no_mes'] = $i;
				$arrData['total'] = 0;
			}else{
				$arrData['anio'] = $prestamoMes['anio'];
				$arrData['no_mes'] = $prestamoMes['mes'];
				$arrData['total'] = $prestamoMes['total'];
				$totalPrestamos += $prestamoMes['total'];
			}
			array_push($arrMPrestamos, $arrData);
		}

		$arrUsuarios = array('totalPrestamos' => $totalPrestamos, 'anio' => $anio, 'meses' => $arrMPrestamos);
		return $arrUsuarios;

	}

    //BUSCADOR DE RANGO DE FECHAS
    public function selectPrestamosD(string $fechaI, string $fechaF, int $ruta, string $tipo)
	{
		$this->strFecha = $fechaI;
		$this->strFecha2 = $fechaF;
		$this->intIdRuta = $ruta;
		$arrDatos = array();

        $estado = $tipo == 'finalizado' ? $estado = " AND pr.status = 2 " : ' '; 

		$sql = "SELECT (SELECT nombres FROM persona WHERE idpersona = pr.usuarioid) as usuario, SUM(pr.monto) AS monto, pr.hora, pr.datecreated FROM prestamos pr 
                LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona)
                WHERE pr.datecreated BETWEEN '{$this->strFecha}' AND '{$this->strFecha2}' AND pr.codigoruta = $ruta" . $estado . "GROUP BY pr.datecreated";
		$request = $this->select_all($sql);

		foreach ($request as $prestamos)
		{
			$prestamosD = $prestamos['datecreated'];
			$prestamosD .= " | ";
			$prestamosD .= $prestamos['monto'];
			$prestamosD .= " | ";
			$prestamosD .= getFormatPrestamos($prestamos['datecreated']);
            $prestamosD .= " | ";
			$prestamosD .= $prestamos['hora'];
            $prestamosD .= " | ";
			$prestamosD .= $prestamos['usuario'];
			array_push($arrDatos, $prestamosD);
		}

		$arrData = array("prestamos" => $arrDatos);

		return $arrData;

	}



    //ACTUALIZA LA COLUMNA PERSONAID DE LA TABLA PAGOS
    public function accionPagos(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $sql = "SELECT * FROM prestamos WHERE codigoruta = $this->intIdRuta AND status != 0";
        $request = $this->select_all($sql);
        
        foreach ($request as $prestamo) {
            $idprestamo = $prestamo['idprestamo'];
            $usuarioId = $prestamo['usuarioid'];
            $sql2 = "SELECT * FROM pagos WHERE prestamoid = $idprestamo";
            $request2 = $this->select_all($sql2);
            foreach ($request2 as $pago) {
                $query_update = "UPDATE pagos SET personaid = ? WHERE prestamoid = $idprestamo";
                $arrData = array($usuarioId);
                $this->update($query_update, $arrData);
            }   
        }
    }

    //ACTUALIZA LA COLUMNA CODIGORUTA DE LA TABLA PRESTAMOS
    public function accionPrestamos(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $sql = "SELECT * FROM persona WHERE codigoruta = $this->intIdRuta AND rolid = 7";
        $request = $this->select_all($sql);
        
        foreach ($request as $persona) {
            $idpersona = $persona['idpersona'];
            $sql2 = "SELECT * FROM prestamos WHERE personaid = $idpersona";
            $request2 = $this->select_all($sql2);
            for ($i=0; $i < COUNT($request2); $i++) { 
                $query_update = "UPDATE prestamos SET codigoruta = ? WHERE personaid = $idpersona";
                $arrData = array($this->intIdRuta);
                $this->update($query_update, $arrData);
            }
        }
    }

    //ACTUALIZA LA COLUMNA USUARIO CON EL ROL ADMINISTRADOR Y FUNCOINARIOS(1, 3)
    public function accionPrestamosUsuario(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $sql = "SELECT * FROM persona WHERE codigoruta = $this->intIdRuta AND rolid != 7";
        $request = $this->select($sql);

        $idpersona = $request['idpersona'];

        $sql2 = "SELECT * FROM prestamos WHERE codigoruta = $this->intIdRuta AND status != 0";
        $request2 = $this->select_all($sql2);
        for ($i=0; $i < COUNT($request2); $i++) { 
            $query_update = "UPDATE prestamos SET usuarioid = ? WHERE codigoruta = $this->intIdRuta";
            $arrData = array($idpersona);
            $this->update($query_update, $arrData);
        }
    }

    //TRAE LOS PRÉSTAMOS FINALIZADOS
    public function prestamosFinalizados(int $ruta)
	{
		$this->intIdRuta = $ruta;
        $contador = 0;
        $arrDatos = array();
		$sql = "SELECT
					idprestamo,
					(SELECT nombres FROM persona WHERE idpersona = personaid) as nombre,
					(SELECT apellidos FROM persona WHERE idpersona = personaid) as negocio,
					(SELECT nombres FROM persona WHERE idpersona = usuarioid) as usuario,
					monto,
					formato,
					taza,
					plazo,
					datecreated,
					datefinal
				FROM prestamos
				WHERE status = 2 AND codigoruta = $this->intIdRuta ORDER BY datefinal DESC LIMIT 8";
		$request = $this->select_all($sql);

		return $request;
	}
}