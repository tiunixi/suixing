<?php
    namespace app\Model;
    use lib\core\DB;
    class bus{
        private $data;
        private $start;
        private $end;
        private $start_id;
        private $end_id;
        
        function init(){
            $this->start = $_GET['start'];
            $this->end = $_GET['end'];
            if(strpos($this->start,'市') or strpos($this->start,'县'))
                $this->start = substr($this->start,0,strlen($this->start) - 3);
            if(strpos($this->end,'市') or strpos($this->end,'县'))
                $this->end = substr($this->end,0,strlen($this->end) - 3);
            $this->get_id();
        }
        //获取输出信息
        function print_line($lines){
            foreach ($lines as $line) {
                $this->data .= $line['start_time'].','.$line['arrive_time'].','.$line['time'].','.$line['start_station_name'].','.$line['end_station_name'].','.$line['type'].','.$line['price'].';';
            }
        }
        //获取站点id
        function get_id(){
            $result = DB::find("city","city like '%{$this->start}%'");
            if(DB::$rowcount > 0){
                $this->start_id = $result['id'];
                $this->end_id = DB::find("station","name='{$this->end}'")['id'];
            }else{
                $this->start_id = DB::find("station","name='{$this->start}'")['id'];
                $result = DB::find("city","city like '{$this->end}'");
                if($result > 0){
                    $this->end_id = $result['id'];
                }else{
                    $this->end_id = DB::find("station","name='{$this->end}'")['id'];
                }
            }
        }
        //获取线路信息
        function get_bus($order){
            if(!isset($_GET['start']) || !isset($_GET['end'])){
                return 0;
            }
            $this->init();
            DB::find("city_to_station","city_id={$this->start_id} and station_id={$this->end_id}");
            if(DB::$rowcount > 0){
                $lines = DB::findAll("line","start_station_id={$this->start_id} and end_station_id={$this->end_id} order by $order");
                if($lines != NULL){
                    $this->print_line($lines);
                }else{
                    $this->data = "无路线信息";
                }
            }else{
                $this->get_interchange($order);
            }
            return $this->data;
        }
        //获取转乘信息
        function get_interchange($order){
            //获取城市id
            $start_city = DB::find("city","city like '%{$this->start}%'");
            if(DB::$rowcount > 0){
                $start_city_id = $start_city['id'];
            }
            $end_city = DB::find("city","city like '%{$this->start}%'");
            if(DB::$rowcount > 0){
                $end_city_id = $end_city['id'];
            }
            if($start_city != NULL and $end_city != NULL){
                $this->city_to_city($start_city_id,$end_city_id,$order);
            }else if($start_city != NULL and $end_city == NULL){
                $this->city_to_station($start_city_id,$order);
            }else if($start_city == NULL and $end_city != NULL){
                $this->station_to_city($end_city_id,$order);
            }else if($start_city == NULL and $end_city == NULL){
                $this->station_to_station($order);
            }
        }
        //城市到城市
        function city_to_city($start_city_id,$end_city_id,$order){
            $second = DB::findAll("city_to_station a,station b,city c,line d","a.city_id={$start_city_id} and b.id=a.station_id  and c.city=b.`name` and d.start_station_id=c.id and d.end_station_id={$end_city_id} and d.state=1 order by {$order}");
            if($second == NULL){
                return 0;
            }
            $center_city_id = $second[0]['city_id'];
            $frist = DB::findAll("line","start_station_id={$this->start_id} and end_station_id={$center_city_id} and state=1 order by {$order}");
            $this->print_line($frist);
            $this->print_line($second);
        }
        //城市到小站
        function city_to_station($start_city_id,$order){
            $second = DB::findAll("city_to_station a,station b,city c,line d","a.city_id={$start_city_id} and b.id=a.station_id and c.city=b.`name` and d.start_station_id=c.id and d.end_station_id={$this->end_station_id} and d.state=1 order by {$order}");
            if($second == NULL){
                return 0;
            }
            $center_city_id = $second[0]['city_id'];
            $frist = DB::findAll("line","start_station_id={$this->start_id} and end_station_id={$center_city_id} and state=1 order by {$order}");
            $this->print_line($frist);
            $this->print_line($second);
        }
        //小站到城市
        function station_to_city($end_city_id,$order){
            p("station_to_city");
        }
        //小站到小站
        function station_to_station($order){
            $second = DB::findAll("line a,city_to_station b","b.station_id={$this->start_id} and a.start_station_id=b.city_id and a.end_station_id={$this->end_id} and a.state=1 order by {$order}");
            if($second == NULL){
                return 0;
            }
            $center_city_id = $second[0]['city_id'];
            $frist = DB::findAll("line","start_station_id={$this->start_id} and end_station_id={$center_city_id} and state=1 order by {$order}");
            $this->print_line($frist);
            $this->print_line($second);
        }
    }
?>