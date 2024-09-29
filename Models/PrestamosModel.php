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

    //TRAE TODOS LOS PRÉSTAMOS
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
                    pr.datefinal,
                    pr.status
                FROM prestamos pr 
                INNER JOIN persona pe 
                ON (pr.personaid = pe.idpersona)
                WHERE (pe.codigoruta = $ruta and pr.status = 1) or (pe.codigoruta = $ruta AND pr.status = 2 and pr.datefinal = '" . NOWDATE . "') ORDER BY pr.datecreated ASC";
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
                        pe.nombres,
                        pe.apellidos
                FROM prestamos pr LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona) 
                WHERE pr.idprestamo = $this->intIdPrestamo";
        $request = $this->select($sql);
        return $request;
    }

    //REGISTRA UN PRÉSTAMO
    public function insertPrestamo(int $cliente, int $monto, int $taza, int $plazo, int $formato, string $observacion, string $fecha, string $vence)
    {
        $this->intIdCliente = $cliente;
        $this->intMonto = $monto;
        $this->intTaza = $taza;
        $this->intFormato = $formato;
        $this->intPlazo = $plazo;
        $this->strObservacion = $observacion;
        $this->strFecha = $fecha;
        $this->strVence = $vence;
        $ruta = $_SESSION['idRuta'];
        $return = 0;

        $sql = "SELECT idresumen FROM resumen WHERE codigoruta = $ruta AND datecreated = '{$this->strFecha}'";
        $requestR = $this->select($sql);
        if(empty($requestR))
        {
            $query_insert = "INSERT INTO prestamos(personaid,monto,formato,plazo,taza,observacion,datecreated,fechavence) VALUES(?,?,?,?,?,?,?,?)";
            $arrData = array($this->intIdCliente,
                            $this->intMonto,
                            $this->intFormato,
                            $this->intPlazo,
                            $this->intTaza,
                            $this->strObservacion,
                            $this->strFecha,
                            $this->strVence);
            $request_insert = $this->insert($query_insert,$arrData);

            $return = $request_insert;
        }else{
            $return = "0";
        }
        return $return;
    }

    //ACTUALIZA UN PRÉSTAMOS
    public function updatePrestamo(int $idprestamo ,int $monto, int $taza, int $plazo, int $formato, string $observacion, string $vence)
    {
        $this->intIdPrestamo = $idprestamo;
        $this->intMonto = $monto;
        $this->intTaza = $taza;
        $this->intFormato = $formato;
        $this->intPlazo = $plazo;
        $this->strObservacion = $observacion;
        $this->strFecha = $vence;

        $sql = "UPDATE prestamos SET monto = ?, taza = ?, plazo = ?, formato = ?, observacion = ?, fechavence = ? WHERE idprestamo = $this->intIdPrestamo";
        $arrData = array($this->intMonto,$this->intTaza,$this->intPlazo,$this->intFormato,$this->strObservacion,$this->strFecha);
        $request = $this->update($sql, $arrData);

        return $request;
    }

    //ELIMINA UN PRÉSTAMOS
    public function deletePrestamo(int $idprestamo)
    {
        $this->intIdPrestamo = $idprestamo;

        $sql = "UPDATE prestamos SET status = ? WHERE idprestamo = $this->intIdPrestamo";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);

        /*
        $sqlP = "SELECT * FROM pagos WHERE prestamoid = $this->intIdPrestamo";
        $requestP = $this->select($sqlP);
        if(!empty($requestP))
        {
            $sqlPD = "DELETE FROM pagos where prestamoid = $this->intIdPrestamo";
            $requestPD = $this->delete($sqlPD);
        }else{
            $requestPD = true;
        }
        
        $return = $requestPD;
        */

        return $request;
    }
}