<?php 

	class LoginModel extends Mysql
	{
		private $strRuta;
		private $intCodigo;
		private $intIdUsuario;
		private $strUsuario;

		public function __construct()
		{
			parent::__construct();
		}	

		public function loginUser(string $ruta, int $codigo, string $usuario)
		{
			$this->strRuta = $ruta;
			$this->intCodigo = $codigo;
			$this->strUsuario = $usuario;
			$sql = "SELECT pe.idpersona, 
                            pe.rolid,
                            pe.status, 
							ru.idruta,
                            ru.codigo, 
                            ru.nombre 
					FROM persona pe INNER JOIN ruta ru ON(pe.codigoruta = ru.idruta) 
					WHERE ru.nombre = '$this->strRuta' AND pe.email_user = '$this->strUsuario' AND pe.codigoruta = $this->intCodigo AND pe.status != 0";
			$request = $this->select($sql);
			return $request;
		}

        public function sessionLogin(int $iduser)
		{
			$this->intIdUsuario = $iduser;
			//BUSCAR ROLE
			$sql = "SELECT p.idpersona,
						  p.identificacion,
						  p.nombres,
						  p.apellidos,
						  p.telefono,
						  p.email_user,
						  r.idrol,
                          r.nombrerol,
						  p.status
					FROM persona p 
					INNER JOIN rol r
					ON p.rolid = r.idrol
					WHERE p.idpersona = $this->intIdUsuario";
			$request = $this->select($sql);
			$_SESSION['userData'] = $request;
			return $request;
		}
    }