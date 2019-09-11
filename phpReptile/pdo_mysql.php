<?php
require_once('function/requireClass.php');
set_time_limit(0);//脚本运行时间不受时间限制
class storeReptile_indb {
    //private $podObject = new pdoMysql();

    /**
     * 从文件里取出爬取的数据
     * 并组合和去重
     */
    public function takeDataFormFile(){
        $lessDataFlight = array();
        $dataFlight = array();

        $fileDoObject = new fileDo("file_A/national_flightMessage_A.txt");
        $originalData = $fileDoObject->fileRead_A();

        foreach($originalData as $k=>$value){           //数组的去重
            if($k>=count($originalData)-1){
                    
            }else if($value==$originalData[$k+1]){
                unset($originalData[$k]);
                 }
         }
        
                //把数组里的数据分成两部分，一类是信息少的一类是信息全的
         foreach($originalData as $key=>$value){
             
             if(is_string($value[0])){
                array_push($lessDataFlight,$value);
                unset($originalData[$key]);
             }
         }
         
         $dataFlight = array($originalData,$lessDataFlight);
         return $dataFlight;                //四维数组
    }


    /**
     * 把重新组合的文件，重新遍历
     * 从多维数组变成二维数组
     * 把数据放到了文件中
     */
    public function changeDataStructure(){
        $fileDoObject = new fileDo("file_A/goodData.txt");

        $completeData = $this->takeDataFormFile();       //获取完整的数据

        foreach($completeData[0] as $value){
            
             foreach($value as $v){
                $fileDoObject->fileWrite_A($v);
             }     
                
        }

    }


    /**
     * 查出城市数据表里面的主键
     * 参数：查找的城市名
     * 返回：找到的城市id
     * 说明：如果找到城市名字一样的城市，就说明并die掉
     */
    public function selectCityId($city){
        $pdoMysql_object = new pdoMysql();
        $sql ="SELECT id from city WHERE city like '".$city."%'";
        $data = $pdoMysql_object->prepareSql($sql);
       if(is_null($data)){
          echo "城市未找到";
           return 0;
       }
        else if(isset($data[1])){                   //找到重复的城市名
            //不是通用方法，找到重复的规律
            
            return $data[1]['id']; 
        }else{
            return $data[0]['id'];
        }
    }

    /**
     * 查出航班起点城市和终点城市的主键
     * 往航线表里面插入数据
     * 制作航线表
     */
    public function setFlighttable(){
        $fileDoObject_2 = new fileDo("file_A/problem_A.txt");
        $fileDoObject = new fileDo("file_A/goodData.txt");      //建立文件处理对象
        $goodData = $fileDoObject->fileRead_A();
        $pdoMysql_object = new pdoMysql();          //建立pdo对象
        //  $sql = "INSERT into city (province,city) value('新疆','博乐')";        //手动输入缺失的省份
        //  $pdoMysql_object->prepareSql_2($sql);die;                       //不得不承认这是我干过的最蠢的事

        $fileDoObject_2->fileEmptied();         //问题文件文件清空

        foreach($goodData as $key=>$value){
            
            $fromCityId = $this->selectCityId($value[1]);
            $toCityId = $this->selectCityId($value[2]);

            if($fromCityId==0||$toCityId==0){           //检测可能出现的两种情况并跳过此次循环  跳过城市表里面没有城市的航班信息插入数据库中
                $problem = array($key);                 //把问题所在地写到文件当中
                $fileDoObject_2->fileWrite_A($problem);

                continue;
            }

            $sql = "SELECT r_id FROM route_table WHERE r_from_id=".$fromCityId." and r_to_id=".$toCityId;
            $isRepate = $pdoMysql_object->prepareSql($sql);                 //检测这个航班信息是不是已经插入过

            if(!empty($isRepate)){      //信息已经存在就跳过
                continue;
            }

            $mileage = $value[3];
            $valuation = 0;
            if($mileage>=800){
                $valuation = $mileage*0.75+50+140;  
            }else{
                $valuation = $mileage*0.75+50+70;
            }
            $valuation = (int)$valuation;       //化为整数           
            $sql2 = "INSERT INTO route_table (r_from_id,r_to_id,mileage,valuation) value (".$fromCityId.",".$toCityId.",".$mileage.",".$valuation.")";           
            $pdoMysql_object->prepareSql_2($sql2);               //执行插入语句
        }

    }

    /**
     * 用不全老旧的数据建立航线表
     */
    public function setRouteTable(){
        $fileDoObject_2 = new fileDo("file_A/problem_A.txt");
        $oldData = $this->takeDataFormFile()[1];
        $pdoMysql_object = new pdoMysql();          //建立pdo对象
        
        foreach($oldData as $key=>$value){
            $fromCity = substr($value[3],0,6);
            $toCity = substr($value[4],0,6);
            
            $fromCityId = $this->selectCityId($fromCity);
            $toCityId = $this->selectCityId($toCity);

            if($fromCityId==0||$toCityId==0){           //检测可能出现的两种情况并跳过此次循环
                $problem = array($key);                 //把问题所在地写到文件当中
                $fileDoObject_2->fileWrite_A($problem);

                continue;
            }

            $sql = "SELECT r_id FROM route_table WHERE r_from_id=".$fromCityId." and r_to_id=".$toCityId;
            
            $isRepate = $pdoMysql_object->prepareSql($sql);                 //检测这个航班信息是不是已经插入过

            if(!empty($isRepate)){      //信息已经存在就跳过
                continue;
            }

            $sql2 = "INSERT INTO route_table (r_from_id,r_to_id) value (".$fromCityId.",".$toCityId.")";
            $pdoMysql_object->prepareSql_2($sql2);               //执行插入语句
        }
    }

    /**
     * 新数据建立航班表
     */
    public function setFlightTable_new(){
        $pdoMysql_object = new pdoMysql();          //建立pdo对象
        $fileDoObject = new fileDo("file_A/goodData.txt");      //建立文件处理对象
        $goodData = $fileDoObject->fileRead_A();

        foreach($goodData as $key=>$value){
            $fromCityId = $this->selectCityId($value[1]);
            $toCityId = $this->selectCityId($value[2]);

            if($fromCityId==0||$toCityId==0){           //跳过本次循环
                continue;
            }

            $sql = "SELECT r_id FROM route_table WHERE r_from_id=".$fromCityId." and r_to_id=".$toCityId;
            $isRepate = $pdoMysql_object->prepareSql($sql);                 //查找航线主键
            $r_id = $isRepate[0]['r_id'];
            
            $sql3 = "SELECT f_id FROM flight_table WHERE f_r_id=".$r_id." and f_flightCode='".$value[0]."'";
            
            $isExist = $pdoMysql_object->prepareSql($sql3);                  //查找航班是不是已经插入
            
            if(!empty($isExist)){
                continue;
            }

            if(!empty($r_id)){
                $sql2 = "INSERT INTO flight_table (f_r_id,f_flightCode,f_flightTime,f_planModel,f_fromAirport,
                f_toAirport) value (".$r_id.",'".$value[0]."','".$value[4]."','".$value[5]."','".$value[6]."','".$value[7]."')";
                
                $pdoMysql_object->prepareSql_2($sql2);               //执行插入语句

            }

        }
    }

    /**
     * 旧数据建立航班表
     */
    public function setFlightTable_old(){
        $oldData = $this->takeDataFormFile()[1];
        $pdoMysql_object = new pdoMysql();          //建立pdo对象
        
        foreach($oldData as $key=>$value){
            $fromCity = substr($value[3],0,6);
            $toCity = substr($value[4],0,6);
            
            $fromCityId = $this->selectCityId($fromCity);
            $toCityId = $this->selectCityId($toCity);

            if($fromCityId==0||$toCityId==0){           //检测可能出现的两种情况并跳过此次循环
                continue;
            }

            $sql = "SELECT r_id FROM route_table WHERE r_from_id=".$fromCityId." and r_to_id=".$toCityId;
            $isRepate = $pdoMysql_object->prepareSql($sql);                 //查找航线主键
            $r_id = $isRepate[0]['r_id'];

            $sql3 = "SELECT f_id FROM flight_table WHERE f_r_id=".$r_id." and f_flightCode='".$value[0]."'";
            
            $isExist = $pdoMysql_object->prepareSql($sql3);                  //查找航班是不是已经插入

            if(!empty($isExist)){
                continue;
            }

            if(!empty($r_id)){
                $sql2 = "INSERT INTO flight_table (f_r_id,f_flightCode,f_flightTime,f_fromAirport,
                f_toAirport) value (".$r_id.",'".$value[0]."','".$value[5]."','".$value[3]."','".$value[4]."')";
                
                $pdoMysql_object->prepareSql_2($sql2);               //执行插入语句

            }
        }
    }
}

//  $test = new storeReptile_indb();
// $test->setFlighttable();         //新数据建立航线表
// $test->setRouteTable();          //老数据建立航线表
// $test->setFlightTable_new();         //新数据建立航班表
// $test->setFlightTable_old();            //老数据建航班

