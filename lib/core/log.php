<?php
    //日志类
    namespace lib\core;
    class log{

        public static $config;
        public static $class;
        //初始化，获取配置
        public static function init($config){
            if(!isset($config)){
                die('找不到log配置文件');
            }
            if($config['IS_START'] == 'false')  //判断是否开启日志
                return 0;
            self::$config = $config;
            self::log('visit');
        }
        //写日志的函数
        public static function log($message){

            require_once("management/getIp.php");
    
            if(empty($_SESSION['ip'])){
                $ip_id = \insert_ip::insert_ips();
                $_SESSION['ip'] = $ip_id;
                
            }else{
                
            }
            //\lib\core\DB::insert($this->db_name,'host,message',"'{$_SERVER['HTTP_HOST']}','$message'");
        }
    }
?>