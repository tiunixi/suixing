<?php
/**
 * 检查用户输入的对应那些城市
 */
session_start();

$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
 require_once('../setIndex/search_modth.php');
 
 

 class checkCity {
    private $searchObject;
    private $pdoObject;
    /**
     * 构造方法，建立对象
     */
    public function __construct(){
        $this->searchObject = new searchModth();
        $this->pdoObject = new pdoSql();
    }
     /**
      * 得到用户输入的信息，查找出能对应的城市
      */
      
      public function checkCity_one($city){
        $pingyin_data = array();
        $chinese_data = array();
        preg_match_all('/[a-zA-Z]+/',$city,$pingyin);
        $chinese = preg_replace('/[0-9a-zA-Z]+/',"",$city);

        if(!empty($pingyin)){
            foreach($pingyin[0] as $value){
                $pingyin_data_less = $this->searchObject->selectTocity($value,"all");
                $pingyin_data = array_merge($pingyin_data,$pingyin_data_less);
            }
        }

        if(!empty($chinese)){
            $chinese_data = $this->searchObject->selectTocity($chinese);
        }
        $citydata = array_merge($chinese_data,$pingyin_data);
        
        for($i=0;$i<count($citydata);$i++){                     //冒泡排序进
            for($j=0;$j<count($citydata)-1;$j++){
                if($citydata[$j][2]<$citydata[$j+1][2]){
                    
                    $station_id = $citydata[$j+1][0];
                    $station = $citydata[$j+1][1];
                    $matchNum = $citydata[$j+1][2];

                    $citydata[$j+1][0] = $citydata[$j][0];
                    $citydata[$j+1][1] = $citydata[$j][1];
                    $citydata[$j+1][2] = $citydata[$j][2];

                    $citydata[$j][0] = $station_id;
                    $citydata[$j][1] = $station;
                    $citydata[$j][2] = $matchNum;
                }

                if($citydata[$j][2]==$citydata[$j+1][2]){
                    if(mb_strlen($citydata[$j][1])>mb_strlen($citydata[$j+1][1])){

                    $station_id = $citydata[$j+1][0];
                    $station = $citydata[$j+1][1];
                    $matchNum = $citydata[$j+1][2];

                    $citydata[$j+1][0] = $citydata[$j][0];
                    $citydata[$j+1][1] = $citydata[$j][1];
                    $citydata[$j+1][2] = $citydata[$j][2];

                    $citydata[$j][0] = $station_id;
                    $citydata[$j][1] = $station;
                    $citydata[$j][2] = $matchNum;
                    }
                }
            }
        }
        
        return $citydata;
      }

      

      /**
       * 通过前端数据查找一个城市数据
       */
      public function select_City(){
          if(!empty($_POST['city'])){
            $s_city = $this->checkCity_one($_POST['city']);
           
            
            if(count($s_city)>5){
                for($i=0;$i<5;$i++){
                    $selectCity[] = $s_city[$i];
                }
            }else{
                for($i=0;$i<count($s_city);$i++){
                    $selectCity[] = $s_city[$i];
                }

            }

            if(empty($selectCity)){
                $selectCity[][1]='未找到匹配城市';
            }
            echo json_encode($selectCity,JSON_UNESCAPED_UNICODE);//JSON_UNESCAPED_UNICODE让中文不编码
            exit;

          }else{
            die;
          }
      }
 }
 