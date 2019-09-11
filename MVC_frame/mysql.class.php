<?php
/**
 * 设置数据库连接的类
 * 
 */

class pdoSql {
    private $dbms = "mysql";
    private $host = "localhost";       //数据库主机名
    //private $dbName = "flight_line";
    private $user = "root";
    private $pwd = "";
    private $pdoObject;

    public function __construct($dbName="flight_line",$charset="utf8"){
        try{
            $setcharset = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"set names ".$charset);
            
            $dsn = $this->dbms.":host=".$this->host.";dbname=".$dbName;       //构造pdo dsn
            $this->pdoObject = new PDO($dsn,$this->user,$this->pwd,$setcharset);
            $this->pdoObject->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);       //设置错误处理模式为抛出异常
            
        }catch(PDOException $e){
            echo $e->getMessage()."<br/>";      //输出错误信息
        }
    }

    /**
     * 执行通用sql语句的方法 不能用查询语句，没有返回值
     */
    public function prepareSql($sql){
        
        try{
            $result = $this->pdoObject->prepare($sql);      //预处理
           
            $result->execute();                     //执行
            

        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }
        
    }
    
    /**
     * 执行插入语句
     * 参数：@param $tbName=表名 $dataArray=要插入的信息得关联数组
     */
    public function insert($tbName,$dataArray){
        $column = array();
        $valueArray = array();
        
        foreach($dataArray as $key=>$value){            //遍历$dataArray数组

            $column[] = $key;
            is_String($value)?$valueArray[] = "'".$value."'":$valueArray[] = $value;        //判断是不是字符串
        }

        $columnChar = implode(",",$column);
        $valueChar = implode(",",$valueArray);
        $sql = "INSERT INTO ".$tbName." (".$columnChar.") values(".$valueChar.")";
       
        try{
            $result = $this->pdoObject->prepare($sql);      //预处理
           
            $result->execute();                     //执行
            
        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }

        return $this->pdoObject->lastInsertId();
    }


    /**
     * 执行删除语句
     * @param $table=表名，$where=条件
     */
    public function delet($table,$where){
        foreach($where as $key=>$value){
            //$value = $this->pdoObject->quote($value);     //为字符添加引号和特殊转义
            is_String($value)?$condition[] = $key."='".$value."'":$condition[] = $key."=".$value;
        }
       
        $condition = implode(" and ",$condition);
        $sql = "DELETE FROM ".$table." WHERE ".$condition;
        
        try{
            $result = $this->pdoObject->prepare($sql);      //预处理
           
            $result->execute();                     //执行
        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }
    }


    /**
     * 执行更改语句
     * @param ￥table=表名 ￥where=条件 $arr=字段=什么值
     */
    public function update($table,$arr,$where){

        foreach($where as $key=>$value){
            is_String($value)?$condition[] = $key."='".$value."'":$condition[] = $key."=".$value;
        }
        foreach($arr as $key=>$value){
            is_String($value)?$result[] = $key."='".$value."'":$result[] = $key."=".$value;
        }

        $result = implode(",",$result);
        $condition = implode(" and ",$condition);
        $sql = "UPDATE ".$table." SET ".$result." WHERE ".$condition;
            

        try{
            $result = $this->pdoObject->prepare($sql);      //预处理
           
            $result->execute();                     //执行
        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }
    }

    /**
     * 执行简单查找语句，查找全部
     * @param ￥table、表名，￥where，查找天剑的关联数组
     * $content 查找的内容
     */
    public function select_all($table,$content,$where=array()){

        $content = implode(",",$content);
        if(!empty($where)){
            foreach($where as $key=>$value){
                is_String($value)?$condition[] = $key."='".$value."'":$condition[] = $key."=".$value;
            }

            
            $condition = implode(" and ",$condition);
            $sql = "SELECT ".$content." FROM ".$table." WHERE ".$condition;
        }else{
            $sql = "SELECT ".$content." FROM ".$table;
        }
        
        try{
            $result = $this->pdoObject->prepare($sql);      //预处理
           
            $result->execute();                     //执行
            $resultArray = $result->fetchAll(PDO::FETCH_ASSOC);         
                          
            return $resultArray;

        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }
    }

    /**
     * 执行sql查询语句
     * 
     * @param string 执行的查询sql语句
     * @access public
     * @return array 二维数组易维长度是个数，二维是关联数组
     */
    public function select_sql($sql){
        
        try{
            $result = $this->pdoObject->prepare($sql);      //预处理
           
            $result->execute();                     //执行
            $resultArray = $result->fetchAll(PDO::FETCH_ASSOC);         
                          
            return $resultArray;

        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }
    }

    /**
     * 用js输出信息并跳转页面，第一个参数为空就直接跳转
     * 
     * @param $info 用来提示的信息
     * @param $url 跳转到的连接
     * @access public
     * @return null
     */
    public static function skip($url, $info=""){
        if(!empty($info)){
            echo "<script type='text/javascript'>
            alert('$info');
            window.location.href='$url';
            </script>";
        }else{
            echo "<script type='text/javascript'> window.location.href='$url';</script>";
        }
    }
}


/* *
*@example
 $test = new pdoSql("final_work");
 $tbName = "c_comment";
 $dataArray = array("status"=>0);
 $arr = array("c_w_id"=>1,"c_type"=>2,"c_content"=>"哈哈","c_from_id"=>1);
 $data = $test->insert($tbName,$arr);
 var_dump($data);
 */