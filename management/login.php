<?php
/**
 * 登录文件
 * 
 * 用于检测账号，密码或者验证码是否正确
 */
session_start();
require_once("../MVC_frame/mysql.class.php");

/* $password = md5("");
$sql = "INSERT management_account (user,`password`) value ('suixing','".$password."')";
$pdoObject = new pdosql();
$pdoObject->prepareSql($sql); die;*/        //插入账号

/**
 * 检查账号密码和验证码是否正确
 */
class loginCheck {
    /**
     * 检测账号和密码
     */
    public function checkUser($user,$password){
        $pdosqlObject = new pdoSql();
        $password = md5($password);
        $s_accountWhere = array("user"=>$user,"password"=>$password);

        $r_account = $pdosqlObject->select_all("management_account",array("*"),$s_accountWhere);
        if(!empty($r_account)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 检测验证码是否正确
     */
    public function checkCheck($check){
        if(!empty($_SESSION['check_checks'])){
            if($_SESSION['check_checks']==$check){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 用来返回是否允许登录
     */
    public function login(){
        if(empty($_POST['login_user'])||empty($_POST['login_pass'])||empty($_POST['yan'])){
            pdoSql::skip('html/manage.html');
        }else{
            $user = $_POST['login_user'];
            $pass = $_POST['login_pass'];
            $checks = $_POST['yan'];

            if($this->checkUser($user,$pass)&&$this->checkCheck($checks)){
                $_SESSION['login'] = 1;
                echo "true";
            }else{
                return false;
            }
        }

    }
}

/**
 * @example
 */
$loginObject = new loginCheck();
$loginObject->login();