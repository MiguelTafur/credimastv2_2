<?php 

class ClientesModel extends Mysql
{
	PRIVATE $intIdUsuario;
	PRIVATE $strIdentificacion;
	PRIVATE $strNombre;
	PRIVATE $strApellido;
	PRIVATE $intTelefono;
	PRIVATE $strDireccion1;
	PRIVATE $strDireccion2;
	PRIVATE $intTipoId;
	PRIVATE $strFecha;
	PRIVATE $intIdRuta;

	public function __construct()
	{
		parent::__construct();
	}

	public function selectClientes()
	{
		$ruta = $_SESSION['idRuta'];
		$sql = "SELECT idpersona, identificacion, nombres, apellidos, telefono, direccion1, direccion2, status 
				FROM persona WHERE rolid = 7 AND codigoruta = $ruta  AND status != 0 ORDER BY nombres DESC";
		$request = $this->select_all($sql);
		return $request;
	}

	public function selectCliente(int $idpersona)
	{
		$this->intIdUsuario = $idpersona;

		$sql_pr = "SELECT idprestamo FROM prestamos WHERE personaid = $this->intIdUsuario";
		$request = $this->select($sql_pr);

		if(empty($request))
		{
			$sql = "SELECT idpersona,identificacion, nombres, apellidos, telefono, direccion1, direccion2, DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro 
					FROM persona WHERE idpersona = $this->intIdUsuario";
		} else {
			$sql = "SELECT pe.idpersona, pe.identificacion, pe.nombres, pe.apellidos, pe.telefono, pe.direccion1, pe.direccion2, pe.status, 
					DATE_FORMAT(pe.datecreated, '%d-%m-%Y') as fechaRegistro, COUNT(pr.personaid) as prestamos
					FROM persona pe LEFT OUTER JOIN prestamos pr ON(pe.idpersona = pr.personaid)
					WHERE pe.idpersona = $this->intIdUsuario";
		}
		
		$request = $this->select($sql);
		
		return $request;
	}

	public function insertCliente(string $identificacion, string $nombre, string $apellido, int $telefono, string $direccion1, string $direccion2, int $tipoid, int $ruta)
	{
		$this->strIdentificacion = $identificacion;
		$this->strNombre = $nombre;
		$this->strApellido = $apellido;
		$this->intTelefono = $telefono;
		$this->strDireccion1 = $direccion1;
		$this->strDireccion2 = $direccion2;
		$this->intTipoId = $tipoid;
		$this->intIdRuta = $ruta;
		$return = 0;

		$sql = "SELECT * FROM persona WHERE identificacion = '{$this->strIdentificacion}' AND codigoruta = $this->intIdRuta";
		$request = $this->select_all($sql);

		if(empty($request))
		{
			$query_insert = "INSERT INTO persona(identificacion,nombres,apellidos,telefono,direccion1,direccion2,rolid,codigoruta,datecreated)  VALUES(?,?,?,?,?,?,?,?,?)";
			$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strDireccion1, $this->strDireccion2,$this->intTipoId,$this->intIdRuta, NOWDATE);
			$request_insert = $this->insert($query_insert, $arrData);
			$return = $request_insert;
		}else{
			$return = "0";
		}
		return $return;
	}

	public function updateCliente(int $idUsuario, string $identificacion, string $nombre, string $apellido, int $telefono, string $direccion1, string $direccion2)
	{
		$this->intIdUsuario = $idUsuario;
		$this->strIdentificacion = $identificacion;
		$this->strNombre = $nombre;
		$this->strApellido = $apellido;
		$this->intTelefono = $telefono;
		$this->strDireccion1 = $direccion1;
		$this->strDireccion2 = $direccion2;

		$sql = "SELECT * FROM persona WHERE identificacion = '{$this->strIdentificacion}' AND idpersona != $this->intIdUsuario";
		$request = $this->select_all($sql);

		if(empty($request))
		{

			$sql = "UPDATE persona SET identificacion = ?, nombres = ?, apellidos = ?, telefono = ?, direccion1 = ?, direccion2 = ?  WHERE idpersona = $this->intIdUsuario";
			$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strDireccion1, $this->strDireccion2);
			$request = $this->update($sql, $arrData);
		}else{
			$request = "0";
		}
		return $request;
	}

	public function deleteCliente(int $idtipousuario)
	{
		$this->intIdUsuario = $idtipousuario;
		$ruta = $_SESSION['idRuta'];

		$sqlPr = "SELECT * FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
				  WHERE pe.codigoruta = $ruta AND pr.personaid = $this->intIdUsuario AND pr.status = 1";
		$requestPr = $this->select_all($sqlPr);

		if(empty($requestPr)){
			$sql = "UPDATE persona SET status = ? WHERE idpersona = $this->intIdUsuario";
			$arrData = array(0);
			$request = $this->update($sql, $arrData);
		}else{
			$request = "0";	
		}

		
		return $request;
	}

	/***** GRÁFICAS *****/
	//MENSUAL
	public function selectClientesMes(string $anio, string $mes)
	{
		$totalClientesMes = 0;
		$arrClientesDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaCliente = date_format($date, "Y-m-d");
		
			$sql = "SELECT DAY(datecreated) as dia FROM persona WHERE DATE(datecreated) = '$fechaCliente' AND codigoruta = $rutaId AND rolid = 7";
			$clienteDia = $this->select($sql);

			$sqlTotal = "SELECT COUNT(*) as total FROM persona WHERE DATE(datecreated) = '$fechaCliente' AND codigoruta = $rutaId AND status != 0 AND rolid = 7";
			$clienteDiaTotal = $this->select($sqlTotal);
			$clienteDiaTotal = $clienteDiaTotal['total'];

			$clienteDia['dia'] = $n_dia;
			$clienteDia['usuario'] = $clienteDiaTotal;
			$clienteDia['usuario'] = $clienteDia['usuario'] == "" ? 0 : $clienteDia['usuario'];
			$totalClientesMes += $clienteDiaTotal;
			array_push($arrClientesDias, $clienteDia);
			$n_dia++;

		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'numeroMes' => $mes, 'total' => $totalClientesMes, 'usuarios' => $arrClientesDias);
		return $arrData;
	}

	//INFORMACIÓN DE CADA PUNTO DE LA GRÁFICA
	public function datosGraficaPersona(string $fecha) 
	{
		$this->strFecha = $fecha;
		$rutaId = $_SESSION['idRuta'];

		$sql = "SELECT nombres, apellidos, DATE_FORMAT(datecreated, '%d-%m-%Y') as fecha 
				FROM persona 
				WHERE rolid = 7 AND datecreated = '{$this->strFecha}' AND codigoruta = $rutaId";
		$request = $this->select_all($sql);

		return $request;
	}

	//ANUAL
	public function selectUsuariosAnio(string $anio) {
		$arrMUsuarios = array();
		$arrMeses = Meses();
		$totalUsuarios = 0;
		$ruta = $_SESSION['idRuta'];

		for ($i=1; $i <= 12; $i++) {
			$arrData = array('anio' => '', 'no_mes' => '', 'mes' => '');
			$sql = "SELECT $anio AS anio, $i AS mes, COUNT(idpersona) AS total
					FROM persona 
					WHERE month(datecreated) = $i 
					AND year(datecreated) = $anio 
					AND status != 0 
					AND rolid = 7 
					AND codigoruta = $ruta
					GROUP BY month(datecreated)";
			$usuarioMes = $this->select($sql);
			$arrData['mes'] = $arrMeses[$i-1];

			if(empty($usuarioMes)){
				$arrData['anio'] = $anio;
				$arrData['no_mes'] = $i;
				$arrData['total'] = 0;
			}else{
				$arrData['anio'] = $usuarioMes['anio'];
				$arrData['no_mes'] = $usuarioMes['mes'];
				$arrData['total'] = $usuarioMes['total'];
				$totalUsuarios += $usuarioMes['total'];
			}
			array_push($arrMUsuarios, $arrData);
		}

		$arrUsuarios = array('totalUsuarios' => $totalUsuarios, 'anio' => $anio, 'meses' => $arrMUsuarios);
		return $arrUsuarios;

	}
	
}