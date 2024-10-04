<?php 

class GastosModel extends Mysql
{
    PRIVATE $intIdGasto;
    PRIVATE $intIdRuta;
    PRIVATE $strFecha;

    public function __construct()
    {
        parent::__construct();
    }	

    //TRAE LOS GASTOS DE SU RESPECTIVA RUTA
    public function selectGastos(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT ga.nombre, ga.monto, ga.hora, ga.datecreated FROM gastos ga
                LEFT OUTER JOIN persona pe ON(ga.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $this->intIdRuta AND ga.status != 0";
        $request = $this->select_all($sql);

        return $request;
    }

    //TRAE LOS GASTOS DE SU RESPECTIVA FECHA Y RUTA
    public function selectGastosFecha(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $ruta;

        $sql = "SELECT ga.nombre, ga.monto, ga.hora, ga.datecreated FROM gastos ga
                LEFT OUTER JOIN persona pr ON(ga.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $this->intIdRuta AND ga.status != 0 AND datecreated = '{$this->strFecha}'";
        $request = $this->select_all($sql);

        return $request;
    }

    //TRAE UN SOLO GASTO
    public function selectGasto(int $idgasto)
    {
        $this->intIdGasto = $idgasto;

        $sql = "SELECT * FROM gastos WHERE idgasto = $this->intIdGasto";
        $request = $this->select_all($sql);

        return $request;
    }
}

 ?>