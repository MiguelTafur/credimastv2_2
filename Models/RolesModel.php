<?php 

class RolesModel extends Mysql
{
    public $intIdrol;
    public $strRol;
    public $strDescripcion;
    public $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    // TRAER TODOS LOS ROLES
    public function selectRoles()
    {
        $whereAdmin = "";
        if($_SESSION['idUser'] != 1){
            $whereAdmin = " and idrol != 1";
        }
        //EXTRAE ROLES
        $sql = "SELECT * FROM rol WHERE status != 0".$whereAdmin;
        $request = $this->select_all($sql);
        return $request;
    }

    //TRAER ROL ESPECÍFICO
    public function selectRol(int $idrol)
    {
        $this->intIdrol = $idrol;
        $sql = "SELECT * FROM rol WHERE idrol = $this->intIdrol";
        $request = $this->select($sql);
        return $request;
    }

    //CREAR NUEVO ROL
    public function insertRol(string $rol, string $descripcion, int $status)
    {

        $return = "";
        $this->strRol = $rol;
        $this->strDescripcion = $descripcion;
        $this->intStatus = $status;

        $sql = "SELECT * FROM rol WHERE nombrerol = '{$this->strRol}' ";
        $request = $this->select_all($sql);

        if(empty($request))
        {
            $query_insert  = "INSERT INTO rol(nombrerol,descripcion,status) VALUES(?,?,?)";
            $arrData = array($this->strRol, $this->strDescripcion, $this->intStatus);
            $request_insert = $this->insert($query_insert,$arrData);
            $return = $request_insert;
        }else{
            $return = "0";
        }
        return $return;
    }

    //ACTUALIZAR ROL
    public function updateRol(int $idrol, string $rol, string $descripcion, int $status)
    {
        $this->intIdrol = $idrol;
        $this->strRol = $rol;
        $this->strDescripcion = $descripcion;
        $this->intStatus = $status;

        $sql = "SELECT * FROM rol WHERE nombrerol = '$this->strRol' AND idrol != $this->intIdrol";
        $request = $this->select_all($sql);

        if(empty($request))
        {
            $sql = "UPDATE rol SET nombrerol = ?, descripcion = ?, status = ? WHERE idrol = $this->intIdrol ";
            $arrData = array($this->strRol, $this->strDescripcion, $this->intStatus);
            $request = $this->update($sql,$arrData);
        }else{
            $request = "0";
        }
        return $request;			
    }

    //ELIMINAR ROL
    public function deleteRol(int $idrol)
    {
        $this->intIdrol = $idrol;
        $sql = "SELECT * FROM persona WHERE rolid = $this->intIdrol";
        $request = $this->select_all($sql);
        if(empty($request))
        {
            $sql = "UPDATE rol SET status = ? WHERE idrol = $this->intIdrol ";
            $arrData = array(0);
            $request = $this->update($sql,$arrData);
            $return = $request;
        }else{
            $return = "0";
        }
        return $return;
    }
}