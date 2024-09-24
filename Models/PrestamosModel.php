<?php 

class PrestamosModel extends Mysql
{
    PRIVATE $fecha_actual;
    PRIVATE $intIdPrestamo;
    PRIVATE $intIdCliente;
    PRIVATE $intIdResumen;
    PRIVATE $intMonto;
    PRIVATE $intTotal;
    PRIVATE $intFormato;
    PRIVATE $intPlazo;
    PRIVATE $intTaza;
    PRIVATE $intParcela;
    PRIVATE $intPago;
    PRIVATE $intPagado;
    PRIVATE $intIdPago;
    PRIVATE $strObservacion;
    PRIVATE $intStatus;
    PRIVATE $intBase;
    PRIVATE $intGasto;
    PRIVATE $strNombre;
    PRIVATE $intPosicion;

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
                    pr.parcela,
                    pr.total,
                    pr.datecreated,
                    pr.fechavence,
                    pr.datefinal,
                    pr.pagoid,
                    pr.pago,
                    pr.datepago,
                    pr.status,
                    pr.orden
                FROM prestamos pr 
                INNER JOIN persona pe 
                ON (pr.personaid = pe.idpersona)
                WHERE (pe.codigoruta = $ruta and pr.status = 1) or (pe.codigoruta = $ruta AND pr.status = 2 and pr.datefinal = " . NOWDATE . ") ORDER BY orden";
        $request = $this->select_all($sql);
        return $request;
    }
}