<?php
    //管理文件，用来驱动开始文件，以及调用控制器的方法
    namespace lib;
    //加载文件库
    require_once(CORE.'/config/require_list.php');  //加载核心文件列表
    //加载开始文件
    require_once(LIB.'/start.php');
    if(!isset($config)){
        die('找不到核心配置文件');
    }
    //开始
    start::run($config);
    //调用控制器的方法
    C(start::$con,start::$med);
    start::init_log($config['logconfig']);
?>