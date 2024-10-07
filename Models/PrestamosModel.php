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
    public function selectPrestamos(int $ruta)
    {
        $this->intIdRuta = $ruta;
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
                WHERE (pe.codigoruta = $this->intIdRuta and pr.status = 1) or (pe.codigoruta = $this->intIdRuta AND pr.status = 2 and pr.datefinal = '" . NOWDATE . "') ORDER BY pr.datecreated ASC";
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
            $sql = "UPDATE prestamos SET status = ? WHERE idprestamo = $this->intIdPrestamo";
            $arrData = array(0);
            $request = $this->update($sql, $arrData);

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
}