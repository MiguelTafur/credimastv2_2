<?php 

class ClientesModel extends Mysql
{
	PRIVATE $intIdUsuario;
	PRIVATE $strIdentificacion;
	PRIVATE $strNombre;
	PRIVATE $strApellido;
	PRIVATE $intTelefono;
	PRIVATE $strDireccion;
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
		$sql = "SELECT idpersona, identificacion, nombres, apellidos, telefono, direccion, status FROM persona WHERE rolid = 7 AND codigoruta = $ruta  AND status != 0 ORDER BY nombres ASC";
		$request = $this->select_all($sql);
		return $request;
	}
}