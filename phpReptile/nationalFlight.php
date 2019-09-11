<?php
require_once("function/requireClass.php");      //应用处理类

/**
 * 获得航班号的类
 */
class getFlightCode {       //根据航空公司代码得到航班代码
    private $flightCompany = array("中国国际航空"=>"CA","山东航空"=>"SC","深圳航空"=>"ZH",
    "昆明航空"=>"KY","西藏航空"=>"TV","中国东方航空"=>"MU","上海航空"=>"FM",
    "幸福航空"=>"JR","中国南方航空"=>"CZ","厦门航空"=>"MF","重庆航空"=>"OQ",
    "海南航空"=>"HU","祥鹏航空"=>"8L","天津航空"=>"GS","西部航空"=>"PN",
    "大新华航空"=>"CN","京都航空"=>"JD","扬子江快运"=>"Y8","福州航空"=>"FU",
    "乌鲁木齐航空"=>"UQ","吉祥航空"=>"HO","九元航空"=>"AQ","春秋航空"=>"9C",
    "华夏航空"=>"G5","四川航空"=>"3U","成都航空"=>"EU","奥凯航空"=>"BK","瑞丽航空"=>"DR",
    "顺丰航空"=>"O3","友和道通"=>"UW","鲲鹏航空"=>"VD","青岛航空"=>"QW",);

            //列出航空公司及其对应的编码
    
    public function mainModth(){        //这个类的主方法

        $num = 1;       //用来显示爬取数据个数
        $phpQueryObject = new phpqueryGet();
        $allFlightCode = array();   //所有的航班编号用来做返回值

        foreach($this->flightCompany as $oneCompanyKey=>$oneCompany){   //遍历航空公司数组 组成爬取连接
           $href = "http://flight.mangocity.com/airwaysschedule-".$oneCompany.".html";
            //用curl类爬取航班代码
            $oneCompanyCode = array();          //把一个航空公司的航班编号存放到一个数组中

            $curlObject = new curlcity($href);

            $fligthCodeHtml = $curlObject->curlMethod();

            $fligthCode = $phpQueryObject->getDetailedmess($fligthCodeHtml,"div.box_airway > table tr","td:eq(1) > p:eq(1)");
            $flightStartTime = $phpQueryObject->getDetailedmess($fligthCodeHtml,"div.box_airway > table tr","td:eq(2) > p:eq(0)");
            $flightStopTime = $phpQueryObject->getDetailedmess($fligthCodeHtml,"div.box_airway > table tr","td:eq(2) > p:eq(1)");
            $flightStartAirport = $phpQueryObject->getDetailedmess($fligthCodeHtml,"div.box_airway > table tr","td:eq(3) > p:eq(0)");
            $flightStopAirport = $phpQueryObject->getDetailedmess($fligthCodeHtml,"div.box_airway > table tr","td:eq(3) > p:eq(1)");
            $onFlightTime = $phpQueryObject->getDetailedmess($fligthCodeHtml,"div.box_airway > table tr","td:eq(4)");
            $onFlightTime = str_replace(array("\r","\n","\r\n","\t"," "),"", $onFlightTime);          //去除空格和换行

            foreach($fligthCode as $codeKey=>$code){            //把每个数组都取出来和各自的信息对应
                if(!empty($code)){          //不为空的数据取出来
                $flightMessage = array($fligthCode[$codeKey],$flightStartTime[$codeKey],$flightStopTime[$codeKey],$flightStartAirport[$codeKey],$flightStopAirport[$codeKey],$onFlightTime[$codeKey]);
                
                array_push($oneCompanyCode,$flightMessage);     //传到一个航班公司的数组当中
                
                }
            }
            $oneCompanyCode = array($oneCompanyKey=>$oneCompanyCode);  //变成和航空公司相关联的关联数组
            $allFlightCode = array_merge($allFlightCode,$oneCompanyCode);//把多个关联数组组合起来

            echo $num."data ";          //用来显示爬取网页个数
            $num++;
            
        }
        return $allFlightCode;

        
    }
    
    /**
     * 把爬取来的航班的编号存放到文件中
     */
    public function saveFlightCode_inTxt(){
        $fileDoObject1 = new fileDo("file_A/flightCode_A.txt");          //一行存一个数组的版本
        $fileDoObject2 = new fileDO("file/flightCode_01.txt");         //一个文件存一个数组的版本
        $allFlightCode = $this->mainModth();
        
        $num = 0;//显示记录信息得条数
        
        $fileDoObject2->fileWrite($allFlightCode);         //先存入一个人文件的版本

        foreach($allFlightCode as $oneCompanyKey=>$oneCompany){
           
            foreach($oneCompany as $oneFlightCode){
                $fileDoObject1->fileWrite_A($oneFlightCode);        //存入会失去航空公司信息


            }
            
        }
    }

}

// $test1 = new getFlightCode();
// $test1->saveFlightCode_inTxt();


/**
 * 按爬取航班号爬取信息
 */
class getMessageFromFlightCode {

    
    /**
     * 获取网站信息
     * @prgram $flight传入的航空代码
     */
    public function getFlightMessage($flightcode){
        $oneFlightArray = array();                          //一个航班号组成的所有航班信息
        $afterCity = array();

        $phpQueryObject = new phpqueryGet();
        $flightCodeHref = "http://www.umetrip.com/mskyweb/fs/fc.do?flightNo=".$flightcode;
        $curlObject = new curlcity($flightCodeHref);             //建立爬取对象

        $flightMessageHtml = $curlObject->curlMethod();             //调用获取的html文档的方法
        
        preg_match_all("/cities.push[(]{1}[\"]{1}[\x{4e00}-\x{9fa5}]+/u",$flightMessageHtml,$afterCityOld);        //用正则匹配js里的数据
        foreach($afterCityOld[0] as $cityKey=>$city){
            $afterCity[$cityKey] = substr($city,13);
        }
        //$afterCity = $phpQueryObject->getDetailedmess($flightMessageHtml,"#p_box > .cir_l.cir_r","span");     //所有经过的城市的数组
        $mileage = $phpQueryObject->getDetailedmess($flightMessageHtml,"div.p_info > ul","li.mileage > span");      //总里程
        $onTime = $phpQueryObject->getDetailedmess($flightMessageHtml,"div.p_info > ul","li.time > span");          //全程时间
        $planAge = $phpQueryObject->getDetailedmess($flightMessageHtml,"div.p_info > ul","li.age > span");          //机型和机龄
        $afterAirport = $phpQueryObject->getDetailedmess($flightMessageHtml,"div.del_com > div.fly_box","div.f_tit > h2");  //获取机场信息
        

        $afterAirport = str_replace(array("\r","\n","\r\n","\t"," "),"",$afterAirport);
        
        foreach($mileage as $key=>$oneMileage){
            $mileage[$key] = (int)substr($oneMileage,0,strlen($oneMileage)-2);      //把总里程字符串转换成能计算的整数
            
        }
        
        foreach($afterCity as $fromCityKey=>$fromCity){                     //一个航班号可能有多个航班信息，把一个航班编号组成多个航班信息
            for($i=$fromCityKey+1;$i<count($afterCity);$i++){               //通过这个循环把多个城市按排序组合
                $sumMileage = 0;
                $sumTime = "";
                for($j=$fromCityKey;$j<$i;$j++){                        //把多个航班的信息整合起来
                    $sumMileage = $sumMileage+$mileage[$j];
                    $sumTime = $this->timeStringPlus($sumTime,$onTime[$j]);
                }

                $flightMessage = array($flightcode,$afterCity[$fromCityKey],$afterCity[$i],$sumMileage,$sumTime,$planAge[0],
                $afterAirport[$fromCityKey],$afterAirport[$i]);  //把一个航班信息组合起来
                array_push($oneFlightArray,$flightMessage);
            }
        }
        return $oneFlightArray;

    }


    /**
     * 字符串时间加法计算的方法
     * @porgram $data1,$data2传过来的时间字符串
     * 返回：时间相加成的字符串
     */
    public function timeStringPlus($data1,$data2){
        if($data1!=""){
            $hour_1 = (int)substr($data1,0,strlen($data1)-5);       //小时转换成能计算的整型
            $hour_2 = (int)substr($data2,0,strlen($data2)-5);
            $minue_1 = (int)substr($data1,-5,2);                //分钟装换成能计算的整型
            $minue_2 = (int)substr($data2,-5,2);                //一个中文算3个长度

            
            $sumHour = $hour_1+$hour_2;
            $summinue = $minue_1+$minue_2;
            if($summinue>=60){
                $sumHour++;
                $summinue = $summinue-60;
            }

                                                        //把装换后的小时分钟数字转变成字符串
            if($summinue<10){
                $sumTime = $sumHour."小时0".$summinue."分";
            }else{
                $sumTime = $sumHour."小时".$summinue."分";
            }
        }else{
            $sumTime = $data2;
        }

        return $sumTime;
    }

    /**
     * 实时从航班编号文件文件中取出航班数组，并记录行数
     * 调用本类中获取网站信息得方法
     * 参数
     */
    public function getMseeageModth($runNum1){
        $fileDoObject_flightCode = new fileDo("file_A/flightCode_A.txt");        //存放航班代码的文件
        $fileDoObject_flightmessage = new fileDo("file_A/national_flightMessage_A.txt");
        $fileDoObject_variable = new fileDo("file/variableFile.txt");            //存放变量信息的文件


        $runNum = $runNum1;
        $flightCodeArray = $fileDoObject_flightCode->fileRead_A();              //读取航班数组
        
            for($runNum;$runNum<count($flightCodeArray);$runNum++){

                $flightcode = $flightCodeArray[$runNum][0];             
                $flightDataArray = $this->getFlightMessage($flightcode);

                if(empty($flightDataArray)){                                //如果没有找到航班信息
                    $flightDataArray = $flightCodeArray[$runNum];
                }

                $fileDoObject_flightmessage->fileWrite_A($flightDataArray);    //把信息数组一行一行的存到文件中
                
                echo ($runNum+1)."data ";

                $variable = array($runNum);
                $fileDoObject_variable->fileWrite($variable);              //把循环的次数存到文件当中
            }

    }

    /**
     * 判断是第几次运行，继续上一次运行
     * 会出现数组重复，取出的时候的去重
     */
    public function canStopGetMessage(){
        
       
        $fileDoObject_variable = new fileDo("file/variableFile.txt");            //存放变量信息的文件

        
        $variableArray = $fileDoObject_variable->fileRead();                    // 读取变量

        if(empty($variableArray)){                  //第一次运行时的方法
            $this->getMseeageModth(0);
            
        }else{                                                          //超过第二次运行时
            $this->getMseeageModth($variableArray[0]);    
        }
    }
}



// $flightMessageTest = new getMessageFromFlightCode();
// $flightMessageTest->canStopGetMessage();

