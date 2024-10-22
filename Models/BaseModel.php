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

        /*$sql = "UPDATE base SET personaid = ?, monto = ?, hora = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $arrData = array($this->intIdUsuario, $this->intMonto, NOWTIME);
        $request = $this->update($sql, $arrData);*/

        $sql = "INSERT INTO base(personaid, codigoruta, monto, hora, datecreated, status) VALUES(?,?,?,?,?,?)";
        $arrData = array($this->intIdUsuario, $this->intIdRuta, $this->intMonto, NOWTIME, $this->strFecha, 1);
        $request = $this->insert($sql, $arrData);

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

    public function selectBaseActualAnterior(int $ruta, string $fecha = NULL)
    {
        $this->intIdRuta = $ruta;

        if($fecha == NULL) {
            $fechaBase = getResumenAnterior($this->intIdRuta)['datecreated'] ?? NOWDATE;
        } else {
            $fechaBase = $fecha;
        } 

        $sql = "SELECT idbase, (SELECT nombres FROM persona WHERE idpersona = personaid) as personaid, monto, hora, datecreated, status FROM base WHERE codigoruta = $this->intIdRuta AND datecreated = '{$fechaBase}'";
        $request = $this->select_all($sql);

        return $request;
    }
}