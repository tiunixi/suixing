<?php
    //配置文件
    namespace lib\core\config;
    $config = array(
        //数据库配置
        'dbconfig' => array('dbtype' => 'mysql','dbhost' => '127.0.0.1','dbuser' => 'root',
                'dbpwd' => '','dbname' => 'suixing','dbcharset' => 'utf8'),
        //日志配置
        'logconfig' => array('IS_START' => 'true','PATH' => FRAME.'/log','DB_NAME' => 'log'),
        //视图配置
        'viewconfig' => array('FORMAT' => 'html','PATH' => FRAME.'\html')
    );
    
?>