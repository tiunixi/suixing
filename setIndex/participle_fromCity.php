<?php
$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
require_once("../MVC_frame/mysql.class.php");
set_time_limit(0);//脚本运行时间不受时间限制

/**
 * 从数据库里面取出所有城市和主键
 */
class getCityFromTable {
    private $mysqlObject;
    public $cityData;

    /**
     * 构造方法，构造连接数据库的类
     * 顺便全部都找出来算了，
     */
    public function __construct($table,$content,$where=array()){
        $this->mysqlObject = new pdoSql;
        
         
        $this->cityData = $this->mysqlObject->select_all($table,$content,$where);
    }

}


/**
 * 建立索引表时的分词方法
 * 采用逆向最大匹配法
 * 每次采用三字分词
 */
class participleClass {
    /**
     * 截取掉第一个字符，去掉第一个字符再截取
     * ￥char要截取都字符
     * 组成一个数组
     */
    public function removeChar($char){
        $allIndex[] = $char;
        $threeCharLess = $char;
        for($i=0;$i<mb_strlen($char)-1;$i++){
            $threeCharLess = mb_substr($threeCharLess,1);
            $allIndex[] = $threeCharLess;
        }

        return $allIndex;
    }


    /**
     * 逆向三字分词
     * @param $char=要分解的字符串
     */
    public function getparticiple($char){
        $threeArray = array();

            $charLess = $char;
            for($i=0;$i<mb_strlen($char);$i++){
                $threeChar = mb_substr($charLess,-3);
                
                $threeArray = array_merge($threeArray,$this->removeChar($threeChar));
                 $charLess =  mb_substr($charLess,0,mb_strlen($charLess)-1);  
            }
        
        return $threeArray;
        
    }

    /**
     * 插入索引表和关系表的通用方法
     * $table表名￥index 插入内容
     */
    public function insert_index($table,$index){
        $pdoSqlObject = new pdoSql();
        $select = array("r_id");
        $is_exist = $pdoSqlObject->select_all($table,$select,$index);
        if(empty($is_exist)){                       //为空就执行插入
            $insert_id = $pdoSqlObject->insert($table,$index);
            return $insert_id;
        }
        return $is_exist[0]['r_id'];

    }

    /**
     * 建立索引表,插入数据
     * 
     */
    public function setindex_table(){
        $pdoSqlObject = new pdoSql();
        $getCityFromTableObject = new getCityFromTable("city",array("id","city"),array("state"=>0));
        $cityData = $getCityFromTableObject->cityData;
        $table = "index_table";                     //索引表，用来查找和插入
        $table_real = "city_index_rela";

        $select_index_id = array("i_id");              //查找索引表的列
        
        foreach($cityData as $key=>$value){
            $city_id = (int)$value['id'];
            $index_array = $this->getparticiple($value['city']);        //用逆向三字分词发，对城市进行分词
            
            foreach($index_array as $index){
                
                $select_index_content = array("i_content"=>$index);     //查找内容作为查找条件
                $is_index = $pdoSqlObject->select_all($table,$select_index_id,$select_index_content);
                
                if(!empty($is_index)){
                    
                    $index_id = (int)$is_index[0]['i_id'];
                    
                    $rela = array("r_city_id"=>$city_id,"r_index_id"=>$index_id);               //不为空应该插入到关系表中
                    $relation_id = $this->insert_index($table_real,$rela);
                    
                    $update = array("state"=>1);
                    $update_where = array("id"=>$city_id);
                    $pdoSqlObject->update("city",$update,$update_where);                    //更新城市表的id是否有用

                }else{
                    $index_id = $pdoSqlObject->insert($table,$select_index_content);    //插入索表
                    $index_id = (int)$index_id;                                         //转换整型
                    
                    $rela = array("r_city_id"=>$city_id,"r_index_id"=>$index_id);       //准备插入关系表
                    $relation_id = $this->insert_index($table_real,$rela);                     //插入关系表

                    $update = array("state"=>1);
                    $update_where = array("id"=>$city_id);
                    $pdoSqlObject->update("city",$update,$update_where);                    //更新城市表的id是否有用

                }
                
            }
            echo $key/count($cityData);
        }
        echo "操作完成";
    }

}

/**
 * @example
 * $test = new participleClass();      //建立索引表和关系表
 * $test->setindex_table();
 */
