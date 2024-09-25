<?php 

class PrestamosModel extends Mysql
{
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdCliente;
    PRIVATE $intMonto;
    PRIVATE $intFormato;
    PRIVATE $intPlazo;
    PRIVATE $intTaza;
    PRIVATE $strObservacion;
    PRIVATE $intStatus;
    PRIVATE $strFecha;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectPrestamos()
    {
        $ruta = $_SESSION['idRuta'];
        $sql = "SELECT 
                    pr.idprestamo, 
                    pr.personaid,
                    pe.nombres,
                    pe.apellidos,
                    pr.monto,
                    pr.formato,
                    pr.taza,
                    pr.plazo,
                    pr.datecreated,
                    pr.fechavence,
                    pr.status
                FROM prestamos pr 
                INNER JOIN persona pe 
                ON (pr.personaid = pe.idpersona)
                WHERE (pe.codigoruta = $ruta and pr.status = 1) or (pe.codigoruta = $ruta AND pr.status = 2 and pr.datefinal = " . NOWDATE . ") ORDER BY pr.datecreated";
        $request = $this->select_all($sql);
        return $request;
    }

    public function insertPrestamo(int $cliente,int $monto, int $taza, int $plazo, int $formato, string $observacion, string $fecha/*, string $vence*/)
		{
            $this->intIdCliente = $cliente;
			$this->intMonto = $monto;
			$this->intTaza = $taza;
			$this->intFormato = $formato;
            $this->intPlazo = $plazo;
			$this->strObservacion = $observacion;
			$this->strFecha = $fecha;
			//$this->strVence = $vence;
			$ruta = $_SESSION['idRuta'];
			$return = 0;

			$sql = "SELECT idresumen FROM resumen WHERE codigoruta = $ruta AND datecreated = '{$this->strFecha}'";
			$requestR = $this->select($sql);
			if(empty($requestR))
			{
				$query_insert = "INSERT INTO prestamos(personaid,monto,formato,plazo,taza,observacion,datecreated) VALUES(?,?,?,?,?,?,?)";
				$arrData = array($this->intIdCliente,
								$this->intMonto,
								$this->intFormato,
								$this->intPlazo,
								$this->intTaza,
								$this->strObservacion,
								$this->strFecha/*,
								$this->strVence*/);
				$request_insert = $this->insert($query_insert,$arrData);

				$return = $request_insert;
			}else{
				$return = "0";
			}
			return $return;
		}
}