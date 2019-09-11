<?php
/**
 * 执行文件
 * 
 * 把已经爬取到数据的航班号存到航班号表中
 * @author rwb
 * @version  v1.0 2018.3.11
 */
require_once("../MVC_frame/mysql.class.php");      //数据库处理类

 /**
     * 执行类
     * 
     * 把已经爬取到数据的航班号存到航班号表中
     * @author rwb
     * @version  v1.0 2018.3.11
     */
class saveFlight_isget {

    /**
     * 从flight——table取出所用航班号，检查flight——code是不是有，没有存入到flight_code中
     * 
     * 静态方法
     * @access public 
     * @since 封装好的pdo
     * @return null
     */
    public static function saveFlight(){
        $pdoObiect = new pdoSql;

        $table_flight = "flight_table";     //已经爬取信息得航班表
        $content = array("f_flightCode");   //查找航班号

        $table_flightcode = "flightcode_code";      //所有航班号组成的表
        $result_allFlight = $pdoObiect->select_all($table_flight,$content);     //调用pdo查找

        foreach($result_allFlight as $value){
            $where = array("flight_code"=>$value['f_flightCode']);  //查找条件是不是已经存在
            $content1_1 = array("id");
            $result_isSave = $pdoObiect->select_all($table_flightcode,$content1_1,$where);

            if(empty($result_isSave)){  
                $saveMessage = array("flight_code"=>$value['f_flightCode'],"is_get"=>1);
                $pdoObiect->insert($table_flightcode,$saveMessage);     //插入语句
            }
        }
        // echo "成功";

    }

}


/**
 * 把爬虫1.2爬取的航班号数据提取出来存放到航班表中
 * 
 * 不需要放到数据更新的代码中
 * @example 
 * saveFlight_isget::saveFlight();
 */

?>