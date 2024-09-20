<?php

class RutasModel extends Mysql
{
    PRIVATE $strNombre;
    PRIVATE $intCodigo;
    PRIVATE $intIdRuta;
    PRIVATE $strDia;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectRutas()
    {
        $sql = "SELECT idruta, codigo, nombre, datecreated as pagamento FROM ruta WHERE estado = 1";
        $request = $this->select_all($sql);
        return $request;
    }

    public function insertRuta(string $nombre, int $codigo, string $dia)
    {
        $this->strNombre = $nombre;
        $this->intCodigo = $codigo;
        $this->strDia = $dia;

        

        $query_insert = "INSERT INTO ruta(nombre, codigo, datecreated) VALUES(?,?,?)";
        $arrData = array($this->strNombre, $this->intCodigo, $this->strDia);
        $request_insert = $this->insert($query_insert, $arrData);

        return $request_insert;
    }
}