<?php 

class BaseModel extends Mysql
{
    PRIVATE $intIdBase;
    PRIVATE $intIdUsuario;
    PRIVATE $intIdRuta;
    PRIVATE $intMonto;
    PRIVATE $strObservacion;
    PRIVATE $strFecha;

    public function __construct()
    {
        parent::__construct();
    }	

    public function insertBase(int $usuario, int $ruta, int $monto, string $observacion, string $fecha)
    {
        $this->intIdUsuario = $usuario;
        $this->intIdRuta = $ruta;
        $this->intMonto = $monto;
        $this->strObservacion = $observacion;
        $this->strFecha = $fecha;
        $return = 0;

        //VARIFICA SI EXISTA UNA BASE CON LA MISMA FECHA
        $sql = "SELECT idbase FROM base WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);

        if(empty($request))
        {
            $query_insert = "INSERT INTO base(personaid, codigoruta, monto, observacion, hora, datecreated) VALUES(?,?,?,?,?,?)";
            $arrData = array($this->intIdUsuario, $this->intIdRuta, $this->intMonto, $this->strObservacion, NOWTIME, $this->strFecha);
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
}