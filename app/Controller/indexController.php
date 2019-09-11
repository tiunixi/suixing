<?php
    namespace app\Controller;
    class index{

        function index(){
            // $data = \PY::encode("张","");
            // p($data);
            // die;
            $view = V('index');
            $view->display('index/index','');
        }

        function get_station(){
            $mod = M('station');
            $data = $mod->get_station();
            foreach ($data as $key) {
                $pingyin = str_replace(" ","",\PY::encode($key['name'],""));
                echo ($key['name'].",".$pingyin.";");
            }
        }
    }