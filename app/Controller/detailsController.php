<?php
    namespace app\Controller;
    use \lib\core\DB;
    class details{

        private $busData = '';
        private $trainData = '';
        private $flightData = '';
        private $shipData = '';

        function bus(){
            if(!isset($_GET['start']) || !isset($_GET['end'])){
                $this->index();
                return 0;
            }
            $mod = M('bus');
            $this->busData = $mod->get_bus("price");
        }

        function trains(){
           
            $trains_obj= M('TrainsPrice');
            if(!isset($_GET['start']) || !isset($_GET['end'])){
                $this->index();
                return 0;
            }
            $this->trainData = $trains_obj->Search_trains($_GET['start'],$_GET['end']);
        }

        function stopApi(){          
            $trains_obj= M('StopInfo');
            $this->trainData = $trains_obj->get_stop_info($_GET['about_id'],$_GET['dpSort'],$_GET['arrSort']);
        }

        function flight(){
           
            if(!isset($_GET['start']) || !isset($_GET['end'])){
                $this->index();
                return 0;
            }
            $flight_obj= M('flight');
            $this->flightData = $flight_obj->searchFlight($_GET['start'],$_GET['end']);
        }

        function show(){
            //$this->bus();
            $this->trains();
            $this->flight();
            $view = V('details');
            $view->display('detailsHtml/details',$this->busData,$this->trainData,$this->flightData,$this->shipData);
        }
        
    }
?>