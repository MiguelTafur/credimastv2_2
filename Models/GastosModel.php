<?php 

class GastosModel extends Mysql
{
    PRIVATE $intIdGasto;
    PRIVATE $intIdRuta;
    PRIVATE $strFecha;
    PRIVATE $strNombre;
    PRIVATE $intValor;
    PRIVATE $intIdUsuario;

    public function __construct()
    {
        parent::__construct();
    }	

    //TRAE LOS GASTOS DE SU RESPECTIVA RUTA
    public function selectGastos(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT * FROM gastos WHERE codigoruta = $this->intIdRuta";
        $request = $this->select_all($sql);

        return $request;
    }

    //TRAE LOS GASTOS DE SU RESPECTIVA FECHA Y RUTA
    public function selectGastosFecha(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $ruta;

        $sql = "SELECT * FROM gastos WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
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

    //TRAE LA SUMA DE LOS GASTOS SEGÚN FECHA
    public function sumaGastos(int $ruta, string $fecha)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $sql = "SELECT SUM(monto) as sumaGastos FROM gastos WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //REGISTRAR GASTOS
    public function insertGasto(int $usuario, int $ruta, string $nombre, int $valor, string $fecha)
    {
        $this->intIdUsuario = $usuario;
        $this->strNombre = $nombre;
        $this->intValor = $valor;
        $this->strFecha = $fecha;
        $this->intIdRuta = $ruta;
        $return = 0;

        $query_insert = "INSERT INTO gastos(personaid, codigoruta, nombre, monto, hora, datecreated) VALUES(?,?,?,?,?,?)";
        $arrData = array($this->intIdUsuario, $this->intIdRuta,$this->strNombre, $this->intValor, NOWTIME, $this->strFecha);
        $request_insert = $this->insert($query_insert, $arrData);
        $return = $request_insert;

        if(!empty($request_insert))
        {
            //TRAE LA SUMA DE LOS Gastos
            $sumaGastos = $this->sumaGastos($this->intIdRuta, $this->strFecha)['sumaGastos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            $updateResumen = setUpdateResumen($this->intIdRuta, $sumaGastos, 4, $this->strFecha);

            $return = $updateResumen;
        } else {
            $return = '0';
        }   

        return $return;
    }
}

 ?>