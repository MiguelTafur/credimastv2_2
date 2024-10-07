<?php 

class GastosModel extends Mysql
{
    PRIVATE $intIdGasto;
    PRIVATE $intIdRuta;
    PRIVATE $strFecha;
    PRIVATE $strNombre;
    PRIVATE $intMonto;
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
        $request = $this->select($sql);

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
        $this->intMonto = $valor;
        $this->strFecha = $fecha;
        $this->intIdRuta = $ruta;
        $return = 0;

        $query_insert = "INSERT INTO gastos(personaid, codigoruta, nombre, monto, hora, datecreated) VALUES(?,?,?,?,?,?)";
        $arrData = array($this->intIdUsuario, $this->intIdRuta,$this->strNombre, $this->intMonto, NOWTIME, $this->strFecha);
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

    public function updateGasto(int $idgasto, string $nombre,  int $monto, int $ruta, string $fecha)
    {
        $this->intIdGasto = $idgasto;
        $this->strNombre = $nombre;
        $this->intMonto = $monto;
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha;

        $query_update = "UPDATE gastos SET nombre = ?, monto = ? WHERE idgasto = $this->intIdGasto";
        $arrData = array($this->strNombre, $this->intMonto);
        $request = $this->update($query_update, $arrData);

        if(!empty($request))
        {
            //TRAE LA SUMA DE LOS GASTOS
            $sumaGastos = $this->sumaGastos($this->intIdRuta, $this->strFecha)['sumaGastos'];

            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $sumaGastos, 4, $this->strFecha);
        }

        return $request;
    }

    //ELIMINA GASTO
    public function deleteGasto(int $idgasto, int $ruta)
    {
        $this->intIdGasto = $idgasto;
        $this->intIdRuta = $ruta;
        $return = 0;
        
        //TRAE LA FECHA
        $fechaGasto = $this->selectGasto($this->intIdGasto)['datecreated'];

        //ELIMINA EL GASTO
        $query_delete = "DELETE FROM gastos WHERE idgasto = $this->intIdGasto";
        $request = $this->delete($query_delete);

        if(!empty($request))
        {
            //TRAE LA SUMA
            $sumaGastos = $this->sumaGastos($this->intIdRuta, $fechaGasto)['sumaGastos'];

            //ACTUALIZA LA COLUMNA "gastos" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $sumaGastos, 4, $fechaGasto);
        } 

        $return = $request;
        
        return $return;
    }
}

 ?>