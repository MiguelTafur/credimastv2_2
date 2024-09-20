<?php

class RutasModel extends Mysql
{
    PRIVATE $strNombre;
    PRIVATE $intCodigo;
    PRIVATE $intIdRuta;

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

    public function selectRuta(int $idruta)
    {
        $this->intIdRuta = $idruta;
        $sql = "SELECT idruta,codigo, nombre, datecreated as pagamento FROM ruta WHERE idruta = $this->intIdRuta";
        $request = $this->select($sql);
        return $request;
    }

    public function insertRuta(string $nombre, int $codigo, string $dia)
    {
        $this->strNombre = $nombre;
        $this->intCodigo = $codigo;
        $this->strDia = $dia;
        $return = 0;

        $sql = "SELECT codigo FROM ruta WHERE codigo = $this->intCodigo";
        $request = $this->select($sql);

        if(empty($request)) 
        {
            $query_insert = "INSERT INTO ruta(nombre, codigo, datecreated) VALUES(?,?,?)";
            $arrData = array($this->strNombre, $this->intCodigo, $this->strDia);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = '0';
        }

        return $return;
    }

    public function updateRuta(int $id, int $codigo, string $nombre)
    {
        $this->intIdRuta = $id;
        $this->strNombre = $nombre;
        $this->intCodigo = $codigo;

        $sql = "UPDATE ruta SET nombre = ?, codigo = ? WHERE idruta = $this->intIdRuta";
        $arrData = array($this->strNombre, $this->intCodigo);
        $request = $this->update($sql, $arrData);

        return $request;
    }

    public function deleteRuta(int $id)
    {
        $this->intIdRuta = $id;

        $sqlPr = "SELECT * FROM ruta ru INNER JOIN persona pe ON(ru.idruta = pe.codigoruta) 
                    WHERE pe.codigoruta = $this->intIdRuta AND pe.rolid = 1 AND ru.estado = 1 AND pe.status = 1";
        $requestPr = $this->select_all($sqlPr);
        
        if(empty($requestPr)){
            $sql = "UPDATE ruta SET estado = ? WHERE idruta = $this->intIdRuta";
            $arrData = array(0);
            $request = $this->update($sql, $arrData);
        }else{
            $request = "0";	
        }

        return $request;
    }
}