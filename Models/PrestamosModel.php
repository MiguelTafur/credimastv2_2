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
}