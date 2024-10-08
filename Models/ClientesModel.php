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
	PRIVATE $intStatus;
	PRIVATE $intIdRuta;

	public function __construct()
	{
		parent::__construct();
	}

	public function selectClientes()
	{
		$ruta = $_SESSION['idRuta'];
		$sql = "SELECT idpersona, identificacion, nombres, apellidos, telefono, direccion1, direccion2, status 
				FROM persona WHERE rolid = 7 AND codigoruta = $ruta  AND status != 0 ORDER BY nombres ASC";
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

		$sql = "SELECT * FROM persona WHERE identificacion = '{$this->strIdentificacion}'";
		$request = $this->select_all($sql);

		if(empty($request))
		{
			$query_insert = "INSERT INTO persona(identificacion,nombres,apellidos,telefono,direccion1,direccion2,rolid,codigoruta)  VALUES(?,?,?,?,?,?,?,?)";
			$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strDireccion1, $this->strDireccion2,$this->intTipoId,$this->intIdRuta);
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
	
}