<?php
    //视图类
    namespace lib\core;
    class view{

        public static $config;
        public static $class;
        //初始化，获取配置
        public static function init($config){
            self::$config = $config;
        }
        //把数据显示到模板文件
        public static function display($file,$busData = '',$trainData = '',$flightData = '',$shipData = ''){
            $path = self::$config['PATH'].'\\'.$file.'.'.self::$config['FORMAT'];
            if(!is_file($path)){
                p('模板文件不存在');
                die;
            }
            require_once($path);
        }
    }
?>