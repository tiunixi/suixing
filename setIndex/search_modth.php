<?php
$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
require_once("../MVC_frame/mysql.class.php");
/**
 * 采用逆向三字检索算法
 */
class searchModth {


    /**
     * 把匹配到的相关联的索引主键取出来
     * 用逆向三字匹配法
     * 返回结果是数组，第一个键是1为匹配1个字的，2为匹配二个字，3为匹配匹配3个字
     * 任意写第二个参数就是匹配拼音，这个方法可以匹配拼音，但是不是很好，不能进行优化排序
     */
    public function searchIndex($char,$is_pinyin=""){
        $indexArray = array();
        $pdoSqlObject = new pdoSql();
        $table = "index_table";                 //查找的表
        $selectconten = array("i_id");
        $isTraverseAll = "";
        
        for($i=0;true;){          //循环取出三个字的循环
            
            if(mb_strlen($char)>=(3+$i)){
                $threechar = mb_substr($char,-3-$i,3);           //取出三个字
            }else{
                $threechar = mb_substr($char,-3-$i,3-((3+$i)-mb_strlen($char)));          //如果字数少于3￥也就是不够取出三个字
            }
            
            
            $where = empty($is_pinyin)?array("i_content"=>$threechar):array("i_pinyin"=>$threechar);            
            $selectResult = $pdoSqlObject->select_all($table,$selectconten,$where);
             
            if(empty($selectResult)){
                $threecharLess = $threechar;

                if(mb_strlen($threecharLess)==1){                   //如果为一个字符并且没有找到相关城市
                    $isTraverseAll = $threechar.$isTraverseAll;
                }
                
                for($j=1;mb_strlen($threecharLess)>1;$j++){
                    $threecharLess = mb_substr($threecharLess,1);       //去掉最左边字符

                    $where2 = empty($is_pinyin)?array("i_content"=>$threecharLess):array("i_pinyin"=>$threecharLess);              //查找
                    $selectResult2 = $pdoSqlObject->select_all($table,$selectconten,$where2);

                    if(empty($selectResult2)){              //这个为空时两种情况，一是继续循环，2是数据库没有这个索引
                        if(mb_strlen($threecharLess)==1){
                            $isTraverseAll = $threecharLess.$isTraverseAll;
                            $i++;                           //如果最后一个字也不匹配就加1
                        }

                    }else{
                        $indexArray[mb_strlen($threecharLess)][] = (int)$selectResult2[0]["i_id"];
                        $i = $i+mb_strlen($threecharLess);
                        $isTraverseAll = $threecharLess.$isTraverseAll;
                        break;
                    }
                }
                
                
            }else{
                $indexArray[mb_strlen($threechar)][] = (int)$selectResult[0]["i_id"];       //用来存放匹配三个字的
                $i = $i+mb_strlen($threechar);
                $isTraverseAll = $threechar.$isTraverseAll;
                
            }

            //var_dump($isTraverseAll);
            if($isTraverseAll==$char){      //把所有字符都遍历完毕
                break;                  //跳出循环
            }
        
        }
        return $indexArray;
    }

    /**
     * 把匹配到的拼音的索引主键取出来,用正向匹配法则
     * 结果是
     */
    public function searchPinyin_index($char){
        $indexArray = array();
        $pdoSqlObject = new pdoSql();
        $table = "index_table";                 //查找的表
        $selectconten = array("i_id");
        $selectResult_1 = $pdoSqlObject->select_all($table,$selectconten,array("i_pinyin"=>$char));
        
        if(empty($selectResult_1)){

            for($i=0;$i<mb_strlen($char);){
                
                $sevenchar = mb_substr($char,$i,7);         //取出七个字

                $where = array("i_pinyin"=>$sevenchar);

                //$selectResult = $pdoSqlObject->select_all($table,$selectconten,$where);
                $sql = "SELECT i_id FROM index_table WHERE i_pinyin='".$sevenchar."' ORDER BY i_id LIMIT 5";
                $selectResult = $pdoSqlObject->select_sql($sql);

                
                if(empty($selectResult)){
                    $sevencharLess = $sevenchar;

                    if(mb_strlen($sevencharLess)<3){
                        $i = $i+mb_strlen($sevencharLess);
                        break;
                    }

                    for($j=0;mb_strlen($sevencharLess)>2;$j++){
                        $sevencharLess = mb_substr($sevencharLess,0,mb_strlen($sevencharLess)-1);       //去掉最后一个

                        $where2 = array("i_pinyin"=>$sevencharLess);
                        //$selectResult2 = $pdoSqlObject->select_all($table,$selectconten,$where2);

                        $sql = "SELECT i_id FROM index_table WHERE i_pinyin='".$sevencharLess."' ORDER BY i_id LIMIT 5";
                        $selectResult2 = $pdoSqlObject->select_sql($sql);

                        if(empty($selectResult2)){
                            mb_strlen($sevencharLess)==2?$i++:$i;
                        }else{
                            foreach($selectResult2 as $value){
                                $indexArray[mb_strlen($sevencharLess)][]= $value['i_id'];
                            }

                            $i = $i+mb_strlen($sevencharLess);
                            break;
                    }
                }


                }else{
                    foreach($selectResult as $value){
                        $indexArray[mb_strlen($sevenchar)][]= $value['i_id'];
                    }

                    $i = $i+mb_strlen($sevenchar);
                }
            }
        }else{
            foreach($selectResult_1 as $value){
            $indexArray[mb_strlen($char)][] = $value['i_id'];
            }
        }
       
        return $indexArray;
    }




    /**
     * 用索引表生成的数组，从关系表中找出城市和id
     * $char searchIndex方法的参数
     * 如果设置了第二个参数就用改进后的拼音匹配，设置了第二个为空字符串，第三个有参数就用逆向三字发匹配英文
     */
    public function selectTocity($char,$pingyin="",$is_pinyin=""){
        $cityMessage = array();
        $cityId = array();
        $pdoSqlObject = new pdoSql();
        $table1 = "city_index_rela";
        $table2 = "city";
        $selectconten = array("r_city_id");
        $selectconten2 = array("city");
        
        $indexId = empty($pingyin)?$this->searchIndex($char,$is_pinyin):$this->searchPinyin_index($char);
        krsort($indexId);
        
        foreach($indexId as $key=>$allvalue){            //循环所有的索引，查找关系表的城市id
            foreach($allvalue as $value){
                $where = array("r_index_id"=>$value);

                $sql = "SELECT r_city_id FROM city_index_rela WHERE r_index_id=".$value." ORDER BY  r_city_id LIMIT 5";
                //$selectResult = $pdoSqlObject->select_all($table1,$selectconten,$where);
                $selectResult = $pdoSqlObject->select_sql($sql);
                
                foreach($selectResult as $v){
                    $cityId[$key][] = $v['r_city_id'];        //把查询结果输出来
                }
            }
            break;
        }
        
        /**
         * 把他们从城市表里面找出来
         * 数组键为3是匹配3个字，1是1个字，2是2一个字
         */
        
        foreach($cityId as $key=>$allvalue){
            foreach($allvalue as $value){
                $cityMessage_less = array();
                $where = array("id"=>$value);

                if($value>4403){
                    $table2 = "station";
                    $selectconten2 = array("name");
                }

                $selectResult2 = $pdoSqlObject->select_all($table2,$selectconten2,$where);
                if(!empty($selectResult2)){
                    foreach($selectResult2 as $v){
                        if($value>4403){ 
                            $cityMessage_less[0] = $value;
                            $cityMessage_less[1] = $v['name'];
                            $cityMessage_less[2] = $key;
                            
                        }else{  
                            $cityMessage_less[0] = $value;
                            $cityMessage_less[1] = $v['city'];
                            $cityMessage_less[2] = $key;

                        }
                        array_push($cityMessage,$cityMessage_less);
                    }
                }

            }
        }
       //var_dump($cityMessage);
        return $cityMessage;
    }


}

/* *
 * @example
 * 
 * 返回类型
 * array (size=2)
  0 => 
    array (size=3)
      0 => string '5353' (length=4)
      1 => string '洗马乡' (length=9)
      2 => int 3
  1 => 
    array (size=3)
      0 => string '78238' (length=5)
      1 => string '洪广镇' (length=9)
      2 => int 3
*/
// $test = new searchModth();

// $test->selectTocity("ahdbfhdfgdsfgadadfgefgdsfgadfgaiusdhfbharbfeah","all"); 