<?php 

class BaseModel extends Mysql
{
    PRIVATE $intIdBase;
    PRIVATE $intIdUsuario;
    PRIVATE $intIdRuta;
    PRIVATE $intMonto;
    PRIVATE $inputMonto;
    PRIVATE $strFecha;

    public function __construct()
    {
        parent::__construct();
    }	

    public function insertBase(int $usuario, int $ruta, int $monto, string $fecha)
    {
        $this->intIdUsuario = $usuario;
        $this->intIdRuta = $ruta;
        $this->intMonto = $monto;
        $this->strFecha = $fecha;
        $return = 0;

        //VARIFICA SI EXISTE UNA BASE CON LA MISMA FECHA
        $sql = "SELECT idbase FROM base WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);

        if(empty($request))
        {
            $query_insert = "INSERT INTO base(personaid, codigoruta, monto, hora, datecreated) VALUES(?,?,?,?,?)";
            $arrData = array($this->intIdUsuario, $this->intIdRuta, $this->intMonto, NOWTIME, $this->strFecha);
            $request = $this->insert($query_insert, $arrData);

            if(!empty($request))
            {
                //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
                setUpdateResumen($this->intIdRuta, $this->intMonto, 1, $this->strFecha);
            }

            $return = $request;
        } else {
            $return = '0';
        }

        return $return;
    }

    //EDITAR BASE
    public function updateBase(int $usuario, int $ruta, int $monto, string $fecha)
    {
        $this->intIdUsuario = $usuario;
        $this->intIdRuta = $ruta;
        $this->intMonto = $monto;
        $this->strFecha = $fecha;

        $sql = "UPDATE base SET personaid = ?, monto = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $arrData = array($this->intIdUsuario, $this->intMonto);
        $request = $this->update($sql, $arrData);

        if(!empty($request))
        {
            //ACTUALIZA LA COLUMNA "VENTAS" DE LA TABLA RESUMEN
            setUpdateResumen($this->intIdRuta, $this->intMonto, 1, $this->strFecha);
        }

        return $this->intMonto;
    }

    public function selectBase(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $fecha = getResumenAnterior($this->intIdRuta)['datecreated'] ?? NOWDATE;

        $sql = "SELECT * FROM base WHERE codigoruta = $this->intIdRuta AND datecreated = '{$fecha}'";
        $request = $this->select($sql);

        return $request;
        
    }

    public function selectMontoBase(int $ruta, int $base)
    {
        $this->intIdRuta = $ruta;
        $this->intMonto = $base;

        $fecha = getResumenAnterior($this->intIdRuta)['datecreated'] ?? NOWDATE;

        $sql = "SELECT * FROM base WHERE codigoruta = $this->intIdRuta AND datecreated = '{$fecha}' AND monto = $this->intMonto";
        $request = $this->select($sql);

        return $request;
        
    }
}