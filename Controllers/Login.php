<?php 

class Login extends Controllers{
    public function __construct()
    {
        session_start();
        if(isset($_SESSION['login'])){
            header('Location: '.base_url().'/prestamos');
        }
        parent::__construct();
    }

    public function login()
    {
        $data['page_tag'] = "Login - CREDIMAST";
        $data['page_title'] = "Credimast";
        $data['page_name'] = "login";
        $data['page_functions_js'] = "functions_login.js";
        $this->views->getView($this,"login",$data);
    }

    public function loginUser()
    {
        //dep($_POST);exit;
        if($_POST){
            if(empty($_POST['txtEmail']) || empty($_POST['txtCodigo']) || empty($_POST['txtRuta'])){
                $arrResponse = array('status' => false, 'msg' => 'Error de datos.');
            }else{
                $strRuta = strtolower(strClean($_POST['txtRuta']));
                $intCodigo = intval($_POST['txtCodigo']);
                $strUsuario = strtolower(strClean($_POST['txtEmail']));
                $requestUser = $this->model->loginUser($strRuta, $intCodigo, $strUsuario);			

                if(empty($requestUser)){
                    $arrResponse = array('status' => false, 'msg' => 'Los datos proporcionados no coinciden.');
                }else{
                    //dep($requestUser);exit;
                    $arrData = $requestUser;
                    if($arrData['status'] == 1){
                        $_SESSION['idUser'] = $arrData['idpersona'];
                        $_SESSION['login'] = true;
                        $_SESSION['idRol'] = $arrData['rolid'];
                        $_SESSION['ruta'] = $arrData['nombre'];
                        $_SESSION['idRuta'] = $arrData['codigo'];
                        $_SESSION['timeout'] = true;
                        $_SESSION['inicio'] = time();

                        $arrData = $this->model->sessionLogin($_SESSION['idUser']);
                        sessionUser($_SESSION['idUser']);

                        $arrResponse = array('status' => true, 'msg' => 'ok.');
                    }else{
                        $arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
                    }
                }
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}