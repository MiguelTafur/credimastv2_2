<?php 

class GastosModel extends Mysql
{
    PRIVATE $intIdGasto;
    PRIVATE $intIdRuta;
    PRIVATE $strFecha;
    PRIVATE $strNombre;
    PRIVATE $intMonto;
    PRIVATE $intIdUsuario;

    public function __construct()
    {
        parent::__construct();
    }	

    //TRAE LOS GASTOS DE SU RESPECTIVA RUTA
    public function selectGastos(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT * FROM gastos WHERE codigoruta = $this->intIdRuta AND nombre != '' ORDER BY datecreated DESC LIMIT 100";
        $request = $this->select_all($sql);

        return $request;
    }

    //TRAE LOS GASTOS DE SU RESPECTIVA FECHA Y RUTA
    public function selectGastosFecha(int $ruta, string $fecha)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $sql = "SELECT * FROM gastos WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $request = $this->select_all($sql);

        return $request;
    }

    //TRAE UN SOLO GASTO
    public function selectGasto(int $idgasto)
    {
        $this->intIdGasto = $idgasto;

        $sql = "SELECT * FROM gastos WHERE idgasto = $this->intIdGasto";
        $request = $this->select($sql);

        return $request;
    }

    //TRAE LA SUMA DE LOS GASTOS SEGÚN FECHA
    public function sumaGastos(int $ruta, string $fecha)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $sql = "SELECT SUM(monto) as sumaGastos FROM gastos WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //REGISTRAR GASTOS
    public function insertGasto(int $usuario, int $ruta, string $nombre, int $valor, string $fecha)
    {
        $this->intIdUsuario = $usuario;
        $this->strNombre = $nombre;
        $this->intMonto = $valor;
        $this->strFecha = $fecha;
        $this->intIdRuta = $ruta;
        $return = 0;

        $query_insert = "INSERT INTO gastos(personaid, codigoruta, nombre, monto, hora, datecreated) VALUES(?,?,?,?,?,?)";
        $arrData = array($this->intIdUsuario, $this->intIdRuta,$this->strNombre, $this->intMonto, NOWTIME, $this->strFecha);
        $request_insert = $this->insert($query_insert, $arrData);
        $return = $request_insert;

        if(!empty($request_insert))
        {
            //TRAE LA SUMA DE LOS Gastos
            $sumaGastos = $this->sumaGastos($this->intIdRuta, $this->strFecha)['sumaGastos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            $updateResumen = setUpdateResumen($this->intIdRuta, $sumaGastos, 4, $this->strFecha);

            $return = $updateResumen;
        } else {
            $return = '0';
        }   

        return $return;
    }

    public function updateGasto(int $idgasto, string $nombre,  int $monto, int $ruta, string $fecha)
    {
        $this->intIdGasto = $idgasto;
        $this->strNombre = $nombre;
        $this->intMonto = $monto;
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $query_update = "UPDATE gastos SET nombre = ?, monto = ? WHERE idgasto = $this->intIdGasto";
        $arrData = array($this->strNombre, $this->intMonto);
        $request = $this->update($query_update, $arrData);

        if(!empty($request))
        {
            //TRAE LA SUMA DE LOS GASTOS
            $sumaGastos = $this->sumaGastos($this->intIdRuta, $this->strFecha)['sumaGastos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $sumaGastos, 4, $this->strFecha);
        }

        return $request;
    }

    //ELIMINA GASTO
    public function deleteGasto(int $idgasto, int $ruta)
    {
        $this->intIdGasto = $idgasto;
        $this->intIdRuta = $ruta;
        $return = 0;
        
        //TRAE LA FECHA
        $fechaGasto = $this->selectGasto($this->intIdGasto)['datecreated'];

        //ELIMINA EL GASTO
        $query_delete = "DELETE FROM gastos WHERE idgasto = $this->intIdGasto";
        $request = $this->delete($query_delete);

        if(!empty($request))
        {
            //TRAE LA SUMA
            $sumaGastos = $this->sumaGastos($this->intIdRuta, $fechaGasto)['sumaGastos'];

            //ACTUALIZA LA COLUMNA "gastos" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $sumaGastos, 4, $fechaGasto);
        } 

        $return = $request;
        
        return $return;
    }

    /***** GRÁFICAS *****/
	//MENSUAL
	public function selectGastosMes(string $anio, string $mes)
	{
		$totalGastosMes = 0;
		$arrGastosDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaGasto = date_format($date, "Y-m-d");
		
			$sql = "SELECT DAY(datecreated) as dia FROM gastos WHERE DATE(datecreated) = '{$fechaGasto}' AND codigoruta = $rutaId";
			$gastoDia = $this->select($sql);

			$sqlTotal = "SELECT SUM(monto) as total FROM gastos WHERE DATE(datecreated) = '{$fechaGasto}' AND codigoruta = $rutaId";
			$gastoDiaTotal = $this->select($sqlTotal);
			$gastoDiaTotal = $gastoDiaTotal['total'];

			$gastoDia['dia'] = $n_dia;
			$gastoDia['gasto'] = $gastoDiaTotal;
			$gastoDia['gasto'] = $gastoDia['gasto'] == "" ? 0 : $gastoDia['gasto'];
			$totalGastosMes += $gastoDiaTotal;
			array_push($arrGastosDias, $gastoDia);
			$n_dia++;

		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'numeroMes' => $mes, 'total' => $totalGastosMes, 'gastos' => $arrGastosDias);
		return $arrData;
	}

    //INFORMACIÓN DE CADA PUNTO DE LA GRÁFICA
	public function datosGraficaGasto(string $fecha) 
	{
		$this->strFecha = $fecha;
		$rutaId = $_SESSION['idRuta'];

		$sql = "SELECT ga.nombre, ga.monto, pe.nombres, ga.hora, DATE_FORMAT(ga.datecreated, '%d-%m-%Y') as fecha 
				FROM gastos ga LEFT OUTER JOIN persona pe ON(ga.personaid = pe.idpersona) 
				WHERE ga.datecreated = '{$this->strFecha}' AND ga.codigoruta = $rutaId";
		$request = $this->select_all($sql);

		return $request;
	}

    //ANUAL
	public function selectGastosAnio(string $anio) {
		$arrMGastos = array();
		$arrMeses = Meses();
		$totalGastos = 0;
		$ruta = $_SESSION['idRuta'];

		for ($i=1; $i <= 12; $i++) {
			$arrData = array('anio' => '', 'no_mes' => '', 'mes' => '');
			$sql = "SELECT $anio AS anio, $i AS mes, SUM(monto) AS total
					FROM gastos 
					WHERE month(datecreated) = $i 
					AND year(datecreated) = $anio  
					AND codigoruta = $ruta
					GROUP BY month(datecreated)";
			$gastoMes = $this->select($sql);
			$arrData['mes'] = $arrMeses[$i-1];

			if(empty($gastoMes)){
				$arrData['anio'] = $anio;
				$arrData['no_mes'] = $i;
				$arrData['total'] = 0;
			}else{
				$arrData['anio'] = $gastoMes['anio'];
				$arrData['no_mes'] = $gastoMes['mes'];
				$arrData['total'] = $gastoMes['total'];
				$totalGastos += $gastoMes['total'];
			}
			array_push($arrMGastos, $arrData);
		}

		$arrUsuarios = array('totalGastos' => $totalGastos, 'anio' => $anio, 'meses' => $arrMGastos);
		return $arrUsuarios;

	}

}

 ?>