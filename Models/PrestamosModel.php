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
    PRIVATE $strVence;

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
                    pr.hora,
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

    //TRAE LA SUMA DE LOS PRÉSTAMOS
    public function sumaPrestamos(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $sql = "SELECT SUM(pr.monto) as sumaPrestamos FROM prestamos pr LEFT OUTER JOIN persona pe ON(pr.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $this->intIdRuta AND pr.status != 0 AND pr.datecreated = '" . NOWDATE . "'";
        $request = $this->select($sql);
        return $request;
    }

    //REGISTRA EL PRÉSTAMO
    public function insertPrestamo(int $cliente, int $monto, int $taza, int $plazo, int $formato, string $observacion, string $fecha, string $vence, int $usuario, int $ruta)
    {
        $this->intIdCliente = $cliente;
        $this->intMonto = $monto;
        $this->intTaza = $taza;
        $this->intFormato = $formato;
        $this->intPlazo = $plazo;
        $this->strObservacion = $observacion;
        $this->strFecha = $fecha;
        $this->strVence = $vence;
        
        $return = 0;

        //TRAE LOS DATOS DEL RESUMEN ACTUAL
        $selectResumen = getResumenActual($ruta);
        if(empty($selectResumen))
        {
            //INSERTA EL RESUMEN
            setResumen($usuario);
        }

        //INSERTA EL PRESTAMO
        $query_insert = "INSERT INTO prestamos(personaid,monto,formato,plazo,taza,observacion,hora,datecreated,fechavence) VALUES(?,?,?,?,?,?,?,?,?)";
        $arrData = array($this->intIdCliente,
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
            $sumaPrestamos = $this->sumaPrestamos($ruta)['sumaPrestamos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            $updateResumen = setUpdateResumen($usuario, $sumaPrestamos, 3);

            $return = $updateResumen;

        }else {
            $return = "0";    
        }

        return $return;
    }

    //ACTUALIZA UN PRÉSTAMOS
    public function updatePrestamo(int $idprestamo ,int $monto, int $taza, int $plazo, int $formato, string $observacion, string $vence, int $usuario, int $ruta)      
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

        if(!empty($request))
        {
            //TRAE LA SUMA DE LOS PRESTAMOS
            $sumaPrestamos = $this->sumaPrestamos($ruta)['sumaPrestamos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            setUpdateResumen($usuario, $sumaPrestamos, 3);
        }

        return $request;
    }

    //ELIMINA EL PRÉSTAMO
    public function deletePrestamo(int $idprestamo, int $usuario, int $ruta)
    {
        $this->intIdPrestamo = $idprestamo;
        $return = 0;

        $pagamento = getUltimoPagamento($idprestamo);
        $pagamento = explode("|", $pagamento);

        if(empty($pagamento[1]))
        {
            $sql = "UPDATE prestamos SET status = ? WHERE idprestamo = $this->intIdPrestamo";
            $arrData = array(0);
            $request = $this->update($sql, $arrData);

            if(!empty($request))
            {
                //TRAE LA SUMA DE LOS PRESTAMOS
                $sumaPrestamos = $this->sumaPrestamos($ruta)['sumaPrestamos'];

                //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
                setUpdateResumen($usuario, $sumaPrestamos, 3);

                //TRAE LOS DATOS DEL RESUMEN ACTUAL
                $resumen = getResumenActual($ruta);

                // VERIFICA SI LA BASE, EL COBRADO, LAS VENTAS Y LOS GASTOS ESTÁN VACÍOS
                if($resumen['base'] == NULL AND $resumen['cobrado'] == NULL AND $resumen['ventas'] == NULL AND $resumen['gastos'] == NULL)
                {
                    //ELIMINA EL RESUMEN
                    deleteResumenActual($resumen['idresumen']);
                }
            }

            $return = $request;
        } else {
            $return = '0';
        }
        return $return;
    }
}