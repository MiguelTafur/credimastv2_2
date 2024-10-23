<?php 

	class UsuariosModel extends Mysql
	{
		PRIVATE $intIdUsuario;
		PRIVATE $strNombre;
		PRIVATE $strEmail;
		PRIVATE $intTipoId;
		PRIVATE $intStatus;
		PRIVATE $intRuta;

		public function __construct()
		{
			parent::__construct();
		}	

		public function selectUsuarios()
		{
			$whereAdmin = "";
			if($_SESSION['idUser'] != 1){
				$ruta = $_SESSION['idRuta'];
				$whereAdmin = " and p.idpersona != 1 and p.codigoruta = $ruta";
			}
			$sql = "SELECT  p.idpersona, 
							p.identificacion, 
							p.nombres, 
							p.codigoruta, 
							p.telefono, 
							p.email_user, 
							p.codigoruta, 
							p.status, 
							r.idrol, 
							r.nombrerol 
					FROM persona p INNER JOIN rol r ON p.rolid = r.idrol 
					WHERE p.status != 0 and (p.rolid = 1 || p.rolid = 3) " . $whereAdmin . " ORDER BY p.nombres DESC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectUsuario(int $idpersona)
		{
			$this->intIdUsuario = $idpersona;
			$sql = "SELECT  p.idpersona, 
							p.nombres,  
							p.email_user, 
							r.idrol, 
							r.nombrerol, 
							p.status, 
							p.codigoruta,
							DATE_FORMAT(p.datecreated, '%d-%m-%Y') as fechaRegistro 
					FROM persona p INNER JOIN rol r ON p.rolid = r.idrol 
					WHERE p.idpersona = $this->intIdUsuario";
			$request = $this->select($sql);
			return $request;
		}

		public function selectRutas()
		{
			$sql = "SELECT * from ruta";
			$request = $this->select_all($sql);
			return $request;
		}

		public function insertUsuario(string $nombre, string $email, int $tipoid, int $status, int $ruta)
		{
			$this->strNombre = $nombre;
			$this->strEmail = $email;
			$this->intTipoId = $tipoid;
			$this->intStatus = $status;
			$this->intRuta = $ruta;
			$return = 0;

			$sql = "SELECT * FROM persona WHERE codigoRuta = $this->intRuta AND email_user = '{$this->strEmail}'";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				$query_insert = "INSERT INTO persona(nombres,email_user,rolid,codigoruta,status, datecreated)  VALUES(?,?,?,?,?,?)";
				$arrData = array($this->strNombre,$this->strEmail,$this->intTipoId,$this->intRuta,$this->intStatus, NOWDATE);
				$request_insert = $this->insert($query_insert, $arrData);
				$return = $request_insert;
			}else{
				$return = "0";
			}
			return $return;
		}

		public function updateUsuario(int $idUsuario, string $nombre, string $email, int $tipoid, int $status)
		{
			$this->intIdUsuario = $idUsuario;
			$this->strNombre = $nombre;
			$this->strEmail = $email;
			$this->intTipoId = $tipoid;
			$this->intStatus = $status;

			$sql = "SELECT * FROM persona WHERE (email_user = '{$this->strEmail}' AND idpersona != $this->intIdUsuario)";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				$sql = "UPDATE persona SET nombres = ?, email_user = ?, rolid = ?, status = ? WHERE idpersona = $this->intIdUsuario";
				$arrData = array($this->strNombre,$this->strEmail,$this->intTipoId,$this->intStatus);
				$request = $this->update($sql, $arrData);
			}else{
				$request = "0";
			}
			return $request;
		}

		public function deleteUsuario(int $idtipousuario)
		{
			$this->intIdUsuario = $idtipousuario;
			$sql = "UPDATE persona SET status = ? WHERE idpersona = $this->intIdUsuario";
			$arrData = array(0);
			$request = $this->update($sql, $arrData);
			return $request;
		}

		public function selectDatePagoPrestamo()
		{
			$ruta = $_SESSION['idRuta'];
			$fecha_actual = date("Y-m-d");

			$sqlR = "SELECT datecreated FROM resumen WHERE codigoruta = $ruta AND datecreated != '$fecha_actual' ORDER BY datecreated DESC";
			$requestR = $this->select($sqlR);

			//dep($requestR);exit;

			// $sql = "SELECT * FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
			// 		WHERE (pr.pagoid != '' AND pr.datepago != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)";
			$sql = "SELECT pa.datecreated as fechaPago FROM prestamos pr 
						INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
						INNER JOIN pagos pa ON(pr.idprestamo = pa.prestamoid)
						WHERE (pa.datecreated != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)
						ORDER BY pa.datecreated desc";
			$request = $this->select($sql);

			//dep($request);exit;

			if(!empty($request) && ($request['fechaPago'] > $requestR['datecreated']))
			{
				return $request;
			}else{
				return 2;
			}
		}
	}
 ?>