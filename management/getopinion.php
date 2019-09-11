<?php
/**
 * 插入用户留言模块
 */

 session_start();
 require_once("../MVC_frame/mysqli.class.php");

 /**
  * 插入用户留言
  */
  class insertopinion {
      /**
       * 获取并插入用户留言的方法
       */
      public function insert_opinion(){
          $pdoSqlObject = new pdoSql();
          if(empty($_SESSION['ip'])){
              die;
          }else{
              if(!empty($_POST['y_name'])&&!empty($_POST['y_email'])&&!empty($_POST['opinion'])){
                $i_leave = array("ip_id"=>$_SESSION['ip'],"y_name"=>$_POST['y_name'],"y_email"=>$_POST['y_email'],"opinion"=>$_POST['opinion']);
                $leave_id = $pdoSqlObject->insert("management_leave",$i_leave);

                if(empty($leave_id)){
                    echo "留言失败";
                }else{
                    echo "留言成功";
                }
              }
          }
      }
  }