<?php
    namespace app\View;
    class details{

        function display($file,$busData = '',$trainData = '',$flightData = '',$shipData = ''){
            \lib\core\view::display($file,$busData,$trainData,$flightData,$shipData);
        }
    }
?>