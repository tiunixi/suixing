<?php
/**
 * 爬虫文件 
 * 
 * 爬取航旅纵横数据，并把数据保存到存放航班信息得数据表
 * @author rwb
 * @version v1.0 2018 3.12
 */

$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
 require_once("../phpReptile/nationalFlight.php");

 $cur_dir = dirname(__FILE__); //获取当前文件的目录
 chdir($cur_dir); //把当前的目录改变为指定的目录。
 require_once("../MVC_frame/mysql.class.php");


/**
 * 爬取信息类，调用了getMessageFromFlightCode的爬虫
 * 
 * @author rwb
 * @version v1.0
 * @date 2018-3-12
 */
class get_flightMessage {

    /**
     * 调用其他爬虫，获取航班所有信息，并存入航班信息表中
     * 
     * @access public
     * @return null
     * @since phpReptile/nationalFlight.php class getMessageFromFlightCode->getFlightMessage()
     */
    public static function get_flightMessageMain(){
        $getMessageFromFlightCodeObject = new getMessageFromFlightCode();       //一代爬虫类
        $pdoObiect = new pdoSql;                                                //pdo数据库处理类

        $table_code = "flightcode_code";                                        //航班代码表
        $conten_updata = array("is_get"=>1);                                    //爬取后更新航班代码表的is_get（是否爬取）
        $table_message = "flight_message";                                      //航班信息表

        $content = array("flight_code");                                        //航班代码表里面的航班字段
        $where = array("is_get"=>0);                                            //从航班代码表获取is_get为0（未爬取）的航班的条件
        $result_code = $pdoObiect->select_all($table_code,$content,$where);
        
        if(!empty($result_code)){                                               //有航班号需要爬取,没有就跳过
            foreach($result_code as $value){
                $flightCode = $value['flight_code'];
                $flightMessage = $getMessageFromFlightCodeObject->getFlightMessage($flightCode);    //能组合成多天航班信息

                if(empty($flightMessage)){                                          //没有航班信息就什么都不做
                    continue;
                }else{
                    foreach($flightMessage as $value){                              //遍历信息，一个一个插入
                        $sql_insert = "INSERT INTO flight_message (flightCode,startCity,toCity,mlieage,usetime,model,startairport,toAirport) 
                        values ('".$value[0]."','".$value[1]."','".$value[2]."',".$value[3].",'".$value[4]."','".$value[5]."','".$value[6]."','".$value[7]."')";
                        
                        $pdoObiect->prepareSql($sql_insert);                        //插入航班信息到flight_message
                        
                    }
                    
                    $where_updata = array("flight_code"=>$flightCode);
                    $pdoObiect->update($table_code,$conten_updata,$where_updata);    //更新航班号表为已经爬取
                    
                }
            }
        }
    }


}

/**
 * 数据处理类，
 * 
 * 把航班信息数据取出来，并插入到航线和航班表，做到更新数据的效果
 * @author rwb(Rural wild baby)
 * @version v1.0
 * @dtae 2018-3-12
 */
class save_flightMessage {
    /**
     * 把数据从航班信息表取出来，并更新数据is_inflight_table值
     * 
     * @access public
     * @return null
     * @since pdosql 数据库处理类
     */
    public function takeMessage(){
        $pdoObject = new pdosql;
        $table_flightMessage = "flight_message";                                                            //先声明要用的变量
        $conten_flightMessage = array("*");
        $where_flightMessage = array("is_inflight_table"=>0);
        $updata_flightMessage = array("is_inflight_table"=>1);                                              //处理后信息后更新的信息

        $flightMessage_2 = $pdoObject->select_all($table_flightMessage,$conten_flightMessage,$where_flightMessage);     //提取信息
        if(!empty($flightMessage_2)){
            foreach($flightMessage_2 as $value){
                $is_success = save_flightMessage::setFlightLine_table($value);                                //建立航线表和航班表
                
                if(empty($is_success)){
                    continue;
                }else{
                    $updata_Where = array("id"=>$value['id']);                                                //更新信息表的is_inflight_table
                    $pdoObject->update("flight_message",$updata_flightMessage,$updata_Where);
                }
            }
        }
    }

    /**
     * 获取城市表的主键，如果城市表中没有这个城市就插入这个城市
     * 
     * @access public
     * 
     * @return int 查找城市对应城市表的主键
     * @param string $city 城市的字符串
     * 
     */
    public static function selectCityId($city){
        $select_pdoObject = new pdosql;
        $table_select = "city";
        $content_select = array("id");
        $where_select = array("city"=>$city);

        $result_city = $select_pdoObject->select_all($table_select,$content_select,$where_select);      //执行查询语句

        if(empty($result_city)){                                                                        //如果没有就插入到城市
            $city_insert_content = array("city"=>$city,"state"=>0);
            $cityId = $select_pdoObject->insert($table_select,$city_insert_content);
            return $cityId;
        }else{
            return $result_city[0]['id'];                                                              //返回id
        }
    }

    /**
     * 建立航线表
     * 
     * @access public
     * @param array 从航班信息表直接取出去来得到的数组
     * @return int 返回这条信息是否制作了航线表
     */
    public static function setFlightLine_table($messageArray){
        $set_pdoObject = new pdosql;

        $fromcityId = save_flightMessage::selectCityId($messageArray['startCity']);
        $tocityId = save_flightMessage::selectCityId($messageArray['toCity']);

        $select_content = array("r_id");
        $select_where = array("r_from_id"=>$fromcityId,"r_to_id"=>$tocityId);

        $route_result = $set_pdoObject->select_all("route_table",$select_content,$select_where);         //查找航线表，航线是不是存在
        if(empty($route_result)){
            $mileage = $messageArray['mlieage'];                                                        //没有找到就插入到航线表
            $valuation = 0;
            if($mileage>=800){                                                                          //做航线估价
                $valuation = $mileage*0.75+50+140;  
            }else{
                $valuation = $mileage*0.75+50+70;
            }
            $valuation = (int)$valuation;       //化为整数      
            
            $insert_ruoteMessage = array("r_from_id"=>$fromcityId,"r_to_id"=>$tocityId,"mileage"=>$mileage,"valuation"=>$valuation);
            $insert_ruoteMessageId = $set_pdoObject->insert('route_table',$insert_ruoteMessage);        //插入航线表

            $is_success = save_flightMessage::setFlight_table($insert_ruoteMessageId,$messageArray);    //插入航班表
            return $is_success;                                                                         //返回是不是已经插入航班信息表
        }else{
            $insert_ruoteMessageId = $route_result[0]['r_id'];

            $is_success = save_flightMessage::setFlight_table($insert_ruoteMessageId,$messageArray);
            return $is_success;                                                                       //返回是不是已经插入航班信息表
        }
    }

    /**
     * 建立航班表或者更新航班表
     * 
     * @access public
     * @param int $insert_ruoteMessageId对应航线表的id
     * @param array $messageArray从信息表取出来的信息 组成的数组
     * @return int 插入航班表的id
     */
    public static function setFlight_table($insert_ruoteMessageId,$messageArray){
        $set_pdoObject = new pdosql;
        $select_flight_where = array("f_r_id"=>$insert_ruoteMessageId,"f_flightCode"=>$messageArray['flightCode']);
        $result_flight = $set_pdoObject->select_all("flight_table",array("f_id"),$select_flight_where);     //查找航班是不是已经在库中

        if(empty($result_flight)){

            $insert_flightMessage = array("f_r_id"=>$insert_ruoteMessageId,"f_flightCode"=>$messageArray['flightCode'],
            "f_flightTime"=>$messageArray['usetime'],"f_planModel"=>$messageArray['model'],"f_fromAirport"=>$messageArray['startairport'],
            "f_toAirport"=>$messageArray['toAirport']);

            $is_success = $set_pdoObject->insert("flight_table",$insert_flightMessage);         //插入航班表
            return $is_success;
        }else{                                                                                  //用航班号和航线id查找这个航班信息是不是存在
            
            $update_flightMessage_where = array("f_id"=>$result_flight[0]['f_id']);             //存在就更新信息
            $updata_flightMessage = array("f_r_id"=>$insert_ruoteMessageId,"f_flightCode"=>$messageArray['flightCode'],
            "f_flightTime"=>$messageArray['usetime'],"f_planModel"=>$messageArray['model'],"f_fromAirport"=>$messageArray['startairport'],
            "f_toAirport"=>$messageArray['toAirport']);

            $set_pdoObject->update("flight_table",$updata_flightMessage,$update_flightMessage_where);       //更新信息
            return $result_flight[0]['f_id'];
        }    
    }
    
    
}

/**
 * @example
 * get_flightMessage::get_flightMessageMain();      //爬取航旅纵横信息放到flightMessage表中（用调用爬虫）
 * $testObject = new save_flightMessage;
 * $testObject->takeMessage();                     //更新城市，航线和航班表（无调用爬虫）
 */




?>