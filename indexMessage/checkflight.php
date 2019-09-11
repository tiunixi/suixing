<?php
/**
 * 查找航班信息
 * 
 */

header('Content-Type: application/json; charset=utf8'); 

require_once('../MVC_frame/mysql.class.php');

class checkflight {
    private $pdoObject;
    /**
     * 构造方法，建立对象
     */
    public function __construct(){
        
        $this->pdoObject = new pdoSql();
    }
    /**
     * 查找航班数据
     */
    public function searchFlight(){
        $allFlightMessage = array();
        
        $routeData = $this->select_line();
        if(empty($routeData)){
            //echo "没有找到航线，可以查看其它线路";
        }else{
            foreach($routeData as $key=>$value){
                $s_flightTableWhere = array("f_r_id"=>$value['r_id']);
                $r_flightTable = $this->pdoObject->select_all("flight_table",array("*"),$s_flightTableWhere);
                //var_dump($r_flightTable);
                if(empty($r_flightTable)){
                    //echo "没有找到航班信息";

                }else{
                    foreach($r_flightTable as $k=>$v){
                        
                        $flightMessage = array_merge($v,$value);
                        
                        array_push($allFlightMessage,$flightMessage);
                    }
                }
            }
        }
        //var_dump($allFlightMessage);
        echo json_encode($allFlightMessage,JSON_UNESCAPED_UNICODE);//JSON_UNESCAPED_UNICODE让中文不编码
    }



    /**
     * 把输入的两个城市去掉市，区，县，特别行政区
     */

     public function select_city($cityName){
        $cityName = str_replace(array("市","区","县","特别行政区","州"),"",$cityName);
        $s_cityLike = "SELECT * FROM city WHERE `city` like '".$cityName."%'";
        $s_cityLike_r = $this->pdoObject->select_sql($s_cityLike);
        return $s_cityLike_r;
        
     }
    /**
       * 把起点城市和终点城市
       * 
       */
      public function select_line() {
          
        if(!empty($_GET['startCity'])&&!empty($_GET['toCity'])){
            $cityLine = array();
            $startCity = $this->select_city($_GET['startCity']);
            $toCity = $this->select_city($_GET['toCity']);
               
            foreach($startCity as $key=>$value){
                foreach($toCity as $k=>$v){
                    $s_route = "SELECT * FROM route_table where r_from_id=".$value['id']." AND r_to_id=".$v['id'];
                    $s_route_r = $this->pdoObject->select_sql($s_route);

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
                   $i_seleclcont = array("ip_id"=>$_SESSION['ip'],"original_start"=>$_GET['startCity'],
               "starCity_id"=>$startCity[0]['id'],"start_city"=>$startCity[0]['city'],"original_to"=>$_GET['toCity'],
           "tocity_id"=>$toCity[0]['id'],"toCity"=>$toCity[0]['city']);
               }else{
                   $i_seleclcont = array("original_start"=>$_GET['startCity'],
                   "starCity_id"=>$startCity[0]['id'],"start_city"=>$startCity[0]['city'],"original_to"=>$_GET['toCity'],
               "tocity_id"=>$toCity[0]['id'],"toCity"=>$toCity[0]['city']);
               }

               $this->pdoObject->insert("management_selectcount",$i_seleclcont);

               return  $cityLine;
       }
     }
    /**
     * 查找推荐线路
     */
    /* public function searchrecommend(){
        $recommendData = array();
        $pdoSqlObject = new pdoSql();
        if((int)$_SESSION['startCity'][0]>4404||(int)$_SESSION['toCity'][0]>4404){
    } */
}

/**
 * 给前端传递json方法
 */
$test = new checkflight;
$test->searchFlight();
