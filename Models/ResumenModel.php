<?php 

class ResumenModel extends Mysql
{
    PRIVATE $intIdRuta;
    PRIVATE $intIdPersona;
    PRIVATE $intValor;
    PRIVATE $intTipo;
    PRIVATE $intIdResumen;
    PRIVATE $strFecha;

    public function __construct()
    {
        parent::__construct();
    }


    //TRAE EL RESUMEN DE LA FECHA ACTUAL
    public function selectResumenActual(int $ruta, string $fecha = NULL)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha ?? NOWDATE;

        $sql = "SELECT re.idresumen, re.base, re.cobrado, re.ventas, re.gastos, re.datecreated FROM resumen re
                LEFT OUTER JOIN persona pe ON(re.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $this->intIdRuta AND re.status = 0 AND re.datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE EL RESUMEN CON EL ESTADO 0 Y CON LA FECHA ACTUAL DIFERENTE
    public function selectResumenAnterior(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT re.idresumen, re.base, re.cobrado, re.ventas, re.gastos, re.datecreated, re.status FROM resumen re
                LEFT OUTER JOIN persona pe ON(re.personaid = pe.idpersona) 
                WHERE pe.codigoruta = $this->intIdRuta AND re.status = 0 AND re.datecreated != '".NOWDATE."'";
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

    //ACTUALIZA EL RESUMEN SEGÚN EL TIPO(BASE, COBRADO, VENTAS, GASTOS)
    public function updateResumen(int $idpersona, $valor, int $tipo, string $fecha)
    {
        $this->intIdPersona = $idpersona;
        $this->intValor = $valor;
        $this->intTipo = $tipo;
        $this->strFecha = $fecha;

        if($this->intTipo == 1)
        {
            $query_update = "UPDATE resumen SET base = ? WHERE personaid = $this->intIdPersona AND datecreated = '{$this->strFecha}'";
        } else if($this->intTipo == 2){
            $query_update = "UPDATE resumen SET cobrado = ? WHERE personaid = $this->intIdPersona AND datecreated = '{$this->strFecha}'";
        } else if($this->intTipo == 3){
            $query_update = "UPDATE resumen SET ventas = ? WHERE personaid = $this->intIdPersona AND datecreated = '{$this->strFecha}'";
        } else if($this->intTipo == 4){
            $query_update = "UPDATE resumen SET gastos = ? WHERE personaid = $this->intIdPersona AND datecreated = '{$this->strFecha}'";
        }

        $arrData = array($this->intValor);
        $request = $this->update($query_update, $arrData);

        //TRAE LOS DATOS DEL RESUMEN
        $sql = "SELECT base, cobrado, ventas, gastos, total FROM resumen WHERE personaid = $this->intIdPersona AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);

        //CALCULA EL TOTAL DEL RESUMEN
        $request['total'] = ($request['base'] + $request['cobrado']) - ($request['ventas'] + $request['gastos']);

        //ACTUALIZA EL TOTAL DEL RESUMEN
        $query_update = "UPDATE resumen SET total = ? WHERE personaid = $this->intIdPersona AND datecreated = '{$this->strFecha}'";
        $arrData = array($request['total']);
        $request = $this->update($query_update, $arrData);

        return $request;
    }

    //ELIMINA EL RESUMEN
    public function deleteResumen($idresumen)
    {
        $this->intIdResumen = $idresumen;

        $query_delete = "DELETE FROM resumen WHERE idresumen = $this->intIdResumen";
        $request = $this->delete($query_delete);
        return $request;
    }
}