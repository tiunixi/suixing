<?php
    session_start();
    define('FRAME',__DIR__);
    //定义常量
    define('APP',FRAME.'/app');//开发目录（控制器，模型，视图所在目录，开发使用）
    define('LIB',FRAME.'/lib');//框架主体目录
    define('CORE',LIB.'/core');//核心文件目录
    define('CONFIG',CORE.'/config');//配置文件所在目录
    //加载第三方库
    require_once(FRAME.'/vendor/autoload.php');
    //加载管理文件
    require_once(LIB.'/admin.php');

    
?>
