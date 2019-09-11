<?php
    //自定义函数的文件，在此定义可直接使用
    //初始化控制器的函数
    function C($name,$method){
        require_once('app/Controller/'.$name.'Controller.php');
        $controller = 'app\Controller\\'.$name;
        $obj = new $controller();
        $obj->$method();
    }
    //初始化模型的函数
    function M($name){
        require_once('app/Model/'.$name.'Model.php');
        $model = 'app\Model\\'.$name;
        $obj = new $model();
        return $obj;
    }
    //初始化视图
    function V($name){
        require_once('app/View/'.$name.'View.php');
        $view = 'app\View\\'.$name;
        $obj = new $view();
        return $obj;
    }
    //自定义的函数，作用：输出
    function p($var){
        print_r($var);
        print_r('<br>');
    }
?>