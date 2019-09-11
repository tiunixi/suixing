<?php
/**
 * 爬虫文件
 * 
 * 用php从非常准 爬取航班编号，存入数据库中，再提炼有用信息存入数据库
 * @author rwb
 * @version  v1.0 2018.3.10
 * 
 */

 $cur_dir = dirname(__FILE__); //获取当前文件的目录
 chdir($cur_dir); //把当前的目录改变为指定的目录。
 require_once("../phpReptile/function/requireClass.php");       //爬虫处理类

 $cur_dir = dirname(__FILE__); //获取当前文件的目录
 chdir($cur_dir); //把当前的目录改变为指定的目录。
 require_once("../MVC_frame/mysql.class.php");      //数据库处理类


/**
 * 爬虫类
 * 
 * 用php从飞常准 爬取航班编号 并把数据存入到数据中，再提炼有用信息存入数据库
 * @author rwb
 * @version  v1.0 2018.3.10
 */
class getFlightCode2_0 {



    /**
     * 用来爬取飞常准的网页
     * 
     * @access public 
     * @return array 所用网页html代码组成的数组
     * @since 一代爬虫的通用方法里面的curl，phpquery方法
     */
    public static function getFligthCodeHtml(){
        $phpqueryObject = new phpqueryGet();    //phpquery类对象处理北京航空网站获取页面数

        $url = "http://www.variflight.com/sitemap.html?AE71649A58c77=";
        $curlObject = new curlcity($url);                   //建立curl对象

        $htmlData[] = $curlObject->curlMethod();      //调用curl类的方法爬取数据

        $bie_url = "http://www.bcia.com.cn/business/flightList.jspx?action=list&ajax=html&fl.flightNo=%E8%BE%93%E5%85%A5%E8%88%AA%E7%8F%AD%E5%8F%B7&language=zh&week=9&flightType=1&fl.originCityCode=&flightStruts=2&dateString=&fl.airlineIata=&pageInfo.pageIndex=1&dateRadio=1&flightCityS=&status=";
        //北京航空网站的一页
        $bei_curlObject = new curlcity($bie_url);
        $bei_htmlData = $bei_curlObject->curlMethod();
        $bei_html_page_array = $phpqueryObject->getDetailedmess($bei_htmlData,"div.page_flight > p:eq(1)","b");
        
        $bei_html_page = (int)$bei_html_page_array[0];
        //把获取页面数装换成整数

        if(!empty($bei_html_page)){
            for($i=1;$i<=$bei_html_page;$i++){
                $bei_url_array[] = "http://www.bcia.com.cn/business/flightList.jspx?action=list&ajax=html&fl.flightNo=%E8%BE%93%E5%85%A5%E8%88%AA%E7%8F%AD%E5%8F%B7&language=zh&week=9&flightType=1&fl.originCityCode=&flightStruts=2&dateString=&fl.airlineIata=&pageInfo.pageIndex=".$i."&dateRadio=1&flightCityS=&status=";
            }                       //组合成一个爬取连接数组

            $bei_curlObject = new curlcity($bei_url_array);
            $bei_htmlData_array = $bei_curlObject->curlMethod();        //爬取所用北京航空HTML

            $allHtmlData = array_merge($htmlData,$bei_htmlData_array);
        
            return $allHtmlData;
        }else{
            return $htmlData;
        }
    }



    /**
     * 把爬取的飞常准HTML存入到数据库中
     * 
     * @access public
     * @since 一代爬虫通用方法的pdo类
     * @return null
     */
    public static function saveHtml(){
        $pdoObject = new pdoMysql();
        $htmlData_array = self::getFligthCodeHtml();       //获取数据

        $sql_select = "SELECT id FROM flightcode_html";      //查找html代码的行数
        $html_line =  $pdoObject->prepareSql($sql_select);
        
        if(count($html_line)>1000){                         //如果HTML数据大于1000行，就去掉前100行
            $sql_delete = "DELETE FROM flightcode_html WHERE `id`<(SELECT MAX(id) FROM(SELECT id FROM `flightcode_html`) AS t)-900";
            $pdoObject->prepareSql_2($sql_delete);
        }

        foreach($htmlData_array as $key=>$htmlData){        //把所有HTML代码转义 并执行插入语句
        $escapingData = htmlspecialchars($htmlData,ENT_QUOTES);
        
        
        $sql = "INSERT INTO flightcode_html (content,type_html) value ('".$escapingData."',0)";
        

        $pdoObject->prepareSql_2($sql);
        //echo "插入".($key+1)."个成功<br/>";
        }
        
    }



    /**
     * 取出最新爬取的数据的HTML 过滤出想要的数据
     * 
     * @access public
     * @since pdo类和phpquery类
     * @return array 由提取的航班代码组成的索引数组
     * 
     */
    public static function getHtmlData(){
        $allFlightCode = array();       //先声明返回的数组，

        $pdoObiect = new pdoSql;        //pdo类生成对象
        $phpqueryObject = new phpqueryGet;      //phpquery类
        $table_html = "flightcode_html";        //html代码的数据表
        $content = array("content");            //提取内容
        $where = array("type_html"=>0);         //提取的条件是type_html=0

        $result_html = $pdoObiect->select_all($table_html,$content,$where);         //执行查找

        if(!empty($result_html)){
            
            
            foreach($result_html as $key=>$value){
                $htmlData = htmlspecialchars_decode($value['content']);             //反转义HTML代码
                
                if($key==0){                                                        //飞常准的信息提取方法
                    $codeData = $phpqueryObject->getDetailedmess($htmlData,"div.list > a","");
                    unset($codeData[0]);                                            //去除第一个无用数据

                    $allFlightCode = array_merge($allFlightCode,$codeData);         //数组合并
                }else{                                                              //北京航班信息提取方法
                    $bei_codeData = $phpqueryObject->getDetailedmess($htmlData,"table.new_flight2 > tr","td:eq(0)");
                    unset($bei_codeData[0]);
                    foreach($bei_codeData as $k=>$v){
                        $bei_codeData[$k] = str_replace(array("\r","\n","\r\n","\t"," "),"",$v);            //去除空格
                    }
                    
                    $allFlightCode = array_merge($allFlightCode,$bei_codeData);
                }
                
            }

            $updata = array("type_html"=>1);                             //更新HTML代码库的type_html=1
            $pdoObiect->update($table_html,$updata,$where);

        }
        return $allFlightCode;
        
    }


    /**
     * 把过滤后的数据 存放到航班号的数据库中
     * 
     * @access public
     * @since pdo封装的类
     * @return null
     * 
     */
    public static function saveCode(){
        $pdoObiect = new pdoSql;
        $table_flightcode = "flightcode_code";

        $codeData = self::getHtmlData();            //获取过滤数据

        if(!empty($codeData)){
            foreach($codeData as $value){
                $where = array("flight_code"=>$value);
                $content1_1 = array("id");
                $result_isSave = $pdoObiect->select_all($table_flightcode,$content1_1,$where);      //查找要存入的航班号在航班号表是不是已经存在

                if(empty($result_isSave)){                                                          //在航班号表不存在就存入表中
                    $saveMessage = array("flight_code"=>$value,"is_get"=>0);
                    $pdoObiect->insert($table_flightcode,$saveMessage);     //插入语句
                }
            }
        }
        //echo "存入成功";
    }


}



/**
 * 文件执行情况，分两步执行
 * @example 
 * $testObject = new getFlightCode2_0;
 * $testObject->saveHtml()      //存储爬取来的HTML代码，有调用爬虫
 * $testObject->saveCode();     //存储提取后的航班号代码，无调用爬虫
 */



?>