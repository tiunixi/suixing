<?php
namespace app\Model;
use lib\core\DB;



class flight {
    private $pdoObject;
    /**
     * 构造方法，建立对象
     */
    public function __construct(){
        
       // $this->pdoObject = DB;
    }
    /**
     * 查找航班数据
     */
    public function searchFlight($start,$end){
        $allFlightMessage = array();
        
        $routeData = $this->select_line($start,$end);
        if(empty($routeData)){
            return $allFlightMessage;;
        }else{
            foreach($routeData as $key=>$value){
                $s_flightTableWhere = array("f_r_id"=>$value['r_id']);
                $r_flightTable = DB::select_all("flight_table",array("*"),$s_flightTableWhere);
                //var_dump($r_flightTable);
                if(empty($r_flightTable)){
                    return $allFlightMessage;;

                }else{
                    foreach($r_flightTable as $k=>$v){
                        
                        $flightMessage = array_merge($v,$value);
                        
                        array_push($allFlightMessage,$flightMessage);
                    }
                }
            }
        }
        //var_dump($allFlightMessage);
        //echo json_encode($allFlightMessage,JSON_UNESCAPED_UNICODE);//JSON_UNESCAPED_UNICODE让中文不编码
        return $allFlightMessage;
    }



    /**
     * 把输入的两个城市去掉市，区，县，特别行政区
     */

     public function select_city($cityName){
        $cityName = str_replace(array("市","区","县","特别行政区","州"),"",$cityName);
        $s_cityLike = "SELECT * FROM city WHERE `city` like '".$cityName."%'";
        $s_cityLike_r = DB::select_sql($s_cityLike);
        return $s_cityLike_r;
        
     }
    /**
       * 把起点城市和终点城市
       * 
       */
      public function select_line($start,$end) {
         
        if(!empty($start)&&!empty($end)){
            $cityLine = array();
            $startCity = $this->select_city($start);
            $toCity = $this->select_city($end);
               
            foreach($startCity as $key=>$value){
                foreach($toCity as $k=>$v){
                    $s_route = "SELECT * FROM route_table where r_from_id=".$value['id']." AND r_to_id=".$v['id'];
                    $s_route_r = DB::select_sql($s_route);

                    if(empty($s_route_r)){

                    }else{
                        $cityLine[]=$s_route_r[0];
                    }
                }
            }
            

            if(empty($startCity[0]['id'])){
                $startCity[0]['id'] = 0;
                $startCity[0]['city'] = $start;
            }

            if(empty($toCity[0]['id'])){
                $toCity[0]['id'] = 0;
                $toCity[0]['city'] = $end;
            }
            //插入查询记录信息
            if(!empty($_SESSION['ip'])){
                

                $i_seleclcont = "ip_id,original_start,starCity_id,start_city,original_to,tocity_id,toCity";
                $i_data = "'".$_SESSION['ip']."','".$start."',".$startCity[0]['id'].",'".$startCity[0]['city']."','".$end."',".$toCity[0]['id'].",'".$toCity[0]['city']."'";
                //var_dump($i_data);die;
            }else{
                $i_seleclcont = "original_start,starCity_id,start_city,original_to,tocity_id,toCity";
                $i_data = "'".$start."',".$startCity[0]['id'].",'".$startCity[0]['city']."','".$end."',".$toCity[0]['id'].",'".$toCity[0]['city']."'";
            }
               /* if(!empty($_SESSION['ip'])){
                   $i_seleclcont = array("ip_id"=>$_SESSION['ip'],"original_start"=>$start,
               "starCity_id"=>$startCity[0]['id'],"start_city"=>$startCity[0]['city'],"original_to"=>$end,
           "tocity_id"=>$toCity[0]['id'],"toCity"=>$toCity[0]['city']);
               }else{
                   $i_seleclcont = array("original_start"=>$start,
                   "starCity_id"=>$startCity[0]['id'],"start_city"=>$startCity[0]['city'],"original_to"=>$end,
               "tocity_id"=>$toCity[0]['id'],"toCity"=>$toCity[0]['city']);
                    $i_select = "original_start,starCity_id,start_city,original_to,tocity_id,toCity";
                    $i_data = $start.",".
               }
                */
               DB::insert("management_selectcount",$i_seleclcont,$i_data);
 
               return  $cityLine;
       }
     }
    
}
