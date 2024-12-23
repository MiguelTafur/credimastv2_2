<?php 

class ResumenModel extends Mysql
{
    PRIVATE $intIdRuta;
    PRIVATE $intIdPersona;
    PRIVATE $intValor;
    PRIVATE $intTipo;
    PRIVATE $intIdResumen;
    PRIVATE $strFecha;
    PRIVATE $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    //TRAE EL RESUMEN CON EL ESTADO 0 Y CON LA FECHA ACTUAL DIFERENTE
    public function selectResumenAnterior(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT * FROM resumen WHERE codigoruta = $this->intIdRuta AND status = 0 AND datecreated != '".NOWDATE."'";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE EL RESUMEN DE LA FECHA ACTUAL Y EL STATUS 0
    public function selectResumenActual(int $ruta, string $fecha = NULL)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = $fecha ?? NOWDATE;
        
        $sql = "SELECT * FROM resumen WHERE codigoruta = $this->intIdRuta AND status = 0 AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE EL RESUMEN CON LA FECHA ACTUAL Y EL STATUS 1
    public function selectResumenActual1(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $this->strFecha = NOWDATE;
        
        $sql = "SELECT * FROM resumen WHERE codigoruta = $this->intIdRuta AND status = 1 AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);
        return $request;
    }

    //TRAE LOS ULTIMOS 8 RESUMENES 
    public function selectUltimosResumen(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT idresumen, 
                        (SELECT nombres FROM persona WHERE idpersona = personaid) as personaid,
                        base, 
                        cobrado, 
                        ventas, 
                        gastos,
                        total, 
                        hora, 
                        datecreated 
                FROM resumen WHERE codigoruta = $this->intIdRuta ORDER BY datecreated DESC LIMIT 8";
        $request = $this->select_all($sql);
        return $request;
    }

    //DEVUELVE EL TOTAL DEL ÚLTIMO RESUMEN SE NO INGRESÓ UNA BASE
    public function selectResumenUltimo(int $ruta)
    {
        $this->intIdRuta = $ruta;
        $return = 0;

        $sql = "SELECT * FROM resumen WHERE codigoruta = $this->intIdRuta AND status = 1 ORDER BY datecreated DESC";
        $request = $this->select($sql);

        $return = $request['total'] ?? 0;

        return $return;
    }

    //REGISTRANDO EL RESUMEN 
    public function insertResumen(int $idpersona, int $ruta)
    {
        $this->intIdPersona = $idpersona;
        $this->intIdRuta = $ruta;
        $query_insert = "INSERT INTO resumen(personaid, codigoruta, datecreated) VALUES(?,?,?)";
        $arrData = array($this->intIdPersona, $this->intIdRuta, NOWDATE);
        $request = $this->insert($query_insert, $arrData);
        return $request;
    }

    //ACTUALIZA EL RESUMEN SEGÚN EL TIPO(BASE, COBRADO, VENTAS, GASTOS)
    public function updateResumen(int $ruta, $valor, int $tipo, string $fecha)
    {
        $this->intIdRuta = $ruta;
        $this->intValor = $valor;
        $this->intTipo = $tipo;
        $this->strFecha = $fecha;

        if($this->intTipo == 1)
        {
            $query_update = "UPDATE resumen SET base = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        } else if($this->intTipo == 2){
            $query_update = "UPDATE resumen SET cobrado = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        } else if($this->intTipo == 3){
            $query_update = "UPDATE resumen SET ventas = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        } else if($this->intTipo == 4){
            $query_update = "UPDATE resumen SET gastos = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        }

        $arrData = array($this->intValor);
        $request = $this->update($query_update, $arrData);

        //TRAE LOS DATOS DEL RESUMEN
        $sql = "SELECT base, cobrado, ventas, gastos, total FROM resumen WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
        $request = $this->select($sql);

        //CALCULA EL TOTAL DEL RESUMEN
        $request['total'] = ($request['base'] + $request['cobrado']) - ($request['ventas'] + $request['gastos']);

        //ACTUALIZA EL TOTAL DEL RESUMEN
        $query_update = "UPDATE resumen SET total = ? WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}'";
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


    public function statusResumen(int $idresumen, int $status)
    {
        $this->intIdResumen = $idresumen;
        $this->intStatus = $status;

        $query_update = "UPDATE resumen SET status = ?, hora = ? WHERE idresumen = $this->intIdResumen";
        $arrData = array($this->intStatus, NOWTIME);
        $request = $this->update($query_update, $arrData);

        return $request;
    }

    //BUSCADOR DE RANGO DE FECHAS
    public function selectResumenD(string $fechaI, string $fechaF, int $ruta)
	{
		$this->strFecha = $fechaI;
		$this->strFecha2 = $fechaF;
		$this->intIdRuta = $ruta;
		$arrDatos = array();

		$sql = "SELECT  base, 
                        cobrado,
                        ventas,
                        gastos,
                        total,
                        hora, 
                        datecreated 
                FROM resumen
                WHERE datecreated BETWEEN '{$this->strFecha}' AND '{$this->strFecha2}' AND codigoruta = $this->intIdRuta ORDER BY datecreated DESC";
		$request = $this->select_all($sql);

		/*foreach ($request as $gastos)
		{
			$gastosD = $gastos['datecreated'];
			$gastosD .= " | ";
			$gastosD .= $gastos['monto'];
			$gastosD .= " | ";
			$gastosD .= getFormatGastos($gastos['datecreated']);
            $gastosD .= " | ";
			$gastosD .= $gastos['nombres'];
			array_push($arrDatos, $gastosD);
		}

		$arrData = array("gastos" => $arrDatos);*/

		return $request;

	}
    

    //ACTUALIZA LOS GASTOS EN LA TABLA RESUMEN
    
    public function accionGastos(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT idgasto, monto, datecreated FROM gastos
                WHERE codigoruta = $this->intIdRuta ORDER BY datecreated DESC";
        $request = $this->select_all($sql);

        foreach ($request as $gasto) {
            $gastos = $gasto['monto'];
            $idgasto = $gasto['idgasto'];
            
            $query_update = "UPDATE resumen SET gastoid = ? WHERE gastoid = $idgasto";
            $arrData = array($gastos);
            $request2 = $this->update($query_update, $arrData);
        }

        return $request;
    }


    //ACTUALIZA LA BASE EN LA TABLA RESUMEN
    public function accionBase(int $ruta)
    {
        $this->intIdRuta = $ruta;

        $sql = "SELECT idbase, monto FROM base
                WHERE codigoruta = $this->intIdRuta ORDER BY datecreated DESC";
        $request = $this->select_all($sql);

        foreach ($request as $base) {
            $monto = $base['monto'];
            $idbase = $base['idbase'];
            
            $query_update = "UPDATE resumen SET baseid = ? WHERE baseid = $idbase";
            $arrData = array($monto);
            $request2 = $this->update($query_update, $arrData);
        }

        return $request2;
    }
}