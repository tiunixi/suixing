<?php
    //数据库类，封装了一些数据库函数
    namespace lib\core;
    class DB{

        public static $rowcount;
        public static $con;
        public static $config;
        public static function init($config){
            self::connect($config);
        }
        //数据库链接
        public static function connect($config){
            self::$config = $config;
            self::$rowcount = 0;
            $type = $config['dbtype'];
            $host = $config['dbhost'];
            $user = $config['dbuser'];
            $dbpwd = $config['dbpwd'];
            $db_name = $config['dbname'];
            $db_charset = $config['dbcharset'];
            self::$rowcount = 0;
            $dsn = "$type:host=$host;dbname=$db_name";
            try {
                //建立持久化的PDO连接
                self::$con = new \PDO($dsn, $user, $dbpwd, array(\PDO::ATTR_PERSISTENT => true));
                self::$con->exec("set character set '$db_charset'");//读库
                self::$con->exec("set names '$db_charset'");//写库    
            } catch (Exception $e) {
                die('连接数据库失败!');
            }
        }
        //执行数据库命令的函数
        public static function query($sql){
            $result = self::$con->query($sql);
            if(!$result)
                die('数据库语句执行失败');
            self::$rowcount = $result->rowCount();
            return $result;
        }
        //数据库查找函数，查找一条
        public static function find($table,$condition){
            $sql = "select * from $table where $condition";
            try{
                $result = self::$con->query($sql);
                if(!$result)
                    die('数据库查询语句执行失败');
                self::$rowcount = $result->rowCount();
            }catch(Exception $e){
                die('查找数据失败');
            }
            return $result->fetch();
        }
        //查找所有
        public static function findAll($table,$condition){
            $sql = "select * from $table where $condition";
            try{
                $result = self::$con->query($sql);
                if(!$result)
                    die('数据库查询语句执行失败');
                self::$rowcount = $result->rowCount();
                while($row = $result->fetch()){
                    $list[] = $row;
                }
            }catch(Exception $e){
                die('查找数据失败');
            }
            return isset($list) ? $list : "";
        }
        //插入
        public static function insert($table,$data_name,$data){
            $sql = "insert into $table($data_name) values($data)";
            try{
                $result = self::$con->exec($sql);
                if(!$result)
                    die('数据库插入语句执行失败');
                self::$rowcount = 1;
            }catch(Exception $e){
                die('插入数据失败');
            }
        }
        //更新
        public static function update($table,$data,$condition){
            $sql = "updata $table set $data where $condition";
            try{
                self::$rowcount = self::$con->exec($sql);
                if(!self::$rowcount)
                    die('数据库更新语句执行失败');
            }catch(Exception $e){
                die('更新数据失败');
            }
        }
        public static function select_all($table,$content,$where=array()){

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
                $result = self::$con->prepare($sql);      //预处理
               
                $result->execute();                     //执行
                $resultArray = $result->fetchAll(\PDO::FETCH_ASSOC);         
                              
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
    public static function select_sql($sql){
        
        try{
            $result = self::$con->prepare($sql);      //预处理
           
            $result->execute();                     //执行
            $resultArray = $result->fetchAll(\PDO::FETCH_ASSOC);         
                          
            return $resultArray;

        }catch(PDOException $e){
            echo "<pre>";
            echo "Error:".$e->getMessage()."<br/>";
            echo "Code:".$e->getCode()."<br/>";
            echo "File:".$e->getFile()."<br/>";
            echo "Line:".$e->getTraceAsString()."<br/></pre>";
        }
    }

    
    }
?>