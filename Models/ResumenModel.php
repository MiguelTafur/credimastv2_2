<?php 

class ResumenModel extends Mysql
{
    PRIVATE $intIdRuta;
    PRIVATE $intIdPersona;
    PRIVATE $intValor;
    PRIVATE $intTipo;
    PRIVATE $intIdResumen;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectResumenActual(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT re.idresumen, re.base, re.cobrado, re.ventas, re.gastos, re.datecreated FROM resumen re LEFT OUTER JOIN persona pe ON(re.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $ruta AND re.datecreated = '".NOWDATE."'";
        $request = $this->select($sql);
        return $request;
    }

    //REGISTRANDO EL RESUMEN 
    public function insertResumen(int $idpersona)
    {
        $this->intIdPersona = $idpersona;
        $query_insert = "INSERT INTO resumen(personaid, datecreated) VALUES(?,?)";
        $arrData = array($this->intIdPersona, NOWDATE);
        $request = $this->insert($query_insert, $arrData);
        return $request;
    }

    public function updateResumen(int $idpersona, $valor, int $tipo)
    {
        $this->intIdPersona = $idpersona;
        $this->intValor = $valor;
        $this->intTipo = $tipo;

        if($this->intTipo == 1)
        {
            $query_update = "UPDATE resumen SET base = ? WHERE personaid = $this->intIdPersona";
        } else if($this->intTipo == 2){
            $query_update = "UPDATE resumen SET cobrado = ? WHERE personaid = $this->intIdPersona";
        } else if($this->intTipo == 3){
            $query_update = "UPDATE resumen SET ventas = ? WHERE personaid = $this->intIdPersona";
        } else if($this->intTipo == 4){
            $query_update = "UPDATE resumen SET gastos = ? WHERE personaid = $this->intIdPersona";
        }

        $arrData = array($this->intValor);
        $request = $this->update($query_update, $arrData);
        return $request;
    }

    public function deleteResumen($idresumen)
    {
        $this->intIdResumen = $idresumen;

        $query_delete = "DELETE FROM resumen WHERE idresumen = $this->intIdResumen";
        $request = $this->delete($query_delete);
        return $request;
    }
}