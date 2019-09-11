<?php
//echo $_SERVER['DOCUMENT_ROOT'].dirname(__FILE__);
$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
require_once("../../phpQuery/phpQuery.php");


/**
 * 爬去城市机场及其坐标信息
*用curl爬取网页信息
*/
class curlcity {
    private $urls;

    public function __construct($url){  //构造方法
        $this->urls = $url;
    }
    //curl获取网页信息
    //参数：是否代理ip
    public function curlMethod($isDaiIp=false){
        if(!is_array($this->urls)){
            $ch = curl_init($this->urls);
            //创建句柄
            
            //curl_setopt($ch,CURLOPT_URL,$this->urls);
                        //curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); //是否返回原生的raw
            if(!empty($isDaiIp)){
                if(is_array($isDaiIp)){         //检测ip池的时候用的方法
                    $ipRand = $isDaiIp;
                }else{
                    $ipRand = getHttpIp();//获取随机ip
                }
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, $ipRand[0]);//设置httpip信息
                curl_setopt($ch,CURLOPT_PROXY,$ipRand[1]);//http代理
                //curl_setopt($ch,CURLOPT_PROXYPORT,$ipArray[2]);//端口
                curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,1);//启用http代理
                curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
                curl_setopt($ch,CURLOPT_REFERER,"http://www.baidu.com/");//伪造来路地址
                curl_setopt($ch,CURLOPT_PROXYAUTH,CURLAUTH_BASIC);//代理认证模式
            }
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//会将服务器返回的localhost放在head中
            curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36
            (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"); //请求中包含一个"User-Agent: "头的字符串
            curl_setopt($ch,CURLOPT_HEADER,0);//是否需要头部信息
            curl_setopt($ch,CURLOPT_TIMEOUT,30);//最大秒数
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//curl_exec()获取的信息是否以字符串返回
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//FALSE 禁止 cURL 验证对等证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//FALSE 不检查证书
            $datas = curl_exec($ch);
            curl_close($ch);
            sleep(2);
            return $datas;
        }else{  //如果传过来的是一个人URL数组
            $dataArray = [];
            $mh = curl_multi_init();
           
            foreach($this->urls as $i=>$url){
                
                $conn[$i] = curl_init();
                curl_setopt($conn[$i],CURLOPT_URL,$url);
                        //curl_setopt($conn[$i], CURLOPT_BINARYTRANSFER, true); //是否返回原生的raw
                if(!empty($isDaiIp)){
                    $ipRand = getHttpIp();//获取随机ip
                    
                    curl_setopt($conn[$i], CURLOPT_HTTPHEADER,$ipRand[0]);//设置httpip信息
                    curl_setopt($conn[$i],CURLOPT_PROXY,$ipRand[1]);//http代理
                    //curl_setopt($conn[$i],CURLOPT_PROXYPORT,$ipArray[2]);//端口
                    curl_setopt($conn[$i],CURLOPT_HTTPPROXYTUNNEL,1);//启用http代理
                    curl_setopt($conn[$i],CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
                    curl_setopt($conn[$i],CURLOPT_REFERER,"http://www.baidu.com/");//伪造来路地址
                    curl_setopt($conn[$i],CURLOPT_PROXYAUTH,CURLAUTH_BASIC);//代理认证模式
                }
                curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1);//会将服务器返回的localhost放在head中
                curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, false);//禁止curl对服务器进行验证
                curl_setopt($conn[$i], CURLOPT_SSL_VERIFYHOST, false);//检查服务器SSL证书中是否存在一个公用名(common name)

                curl_setopt($conn[$i],CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36
                (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"); //请求中包含一个"User-Agent: "头的字符串
                curl_setopt($conn[$i],CURLOPT_HEADER,0);//是否需要头部信息
                curl_setopt($conn[$i],CURLOPT_TIMEOUT,30);//最大秒数
                curl_setopt($conn[$i],CURLOPT_RETURNTRANSFER,1);//curl_exec()获取的信息是否以字符串返回
                
                curl_multi_add_handle($mh, $conn[$i]); 
            }
            
            $active = null;
                // 执行批处理句柄
                do {
                    sleep(2);
                    $mrc = curl_multi_exec($mh, $active);
                } while ($active);


            //得到各个页面信息
            foreach($this->urls as $i=>$url){
            $datas = curl_multi_getcontent($conn[$i]);
           
            array_push($dataArray,$datas);
            }
            //关闭句柄组中的单个句柄
            foreach($this->urls as $i=>$url){
                curl_multi_remove_handle($mh, $conn[$i]); 
                curl_close($conn[$i]);
            }

            curl_multi_close($mh);//关闭句柄组
           
            return $dataArray;
        }

    }

}


/**
 * 从爬取到的数据用phpquery取出想要具体的数据
 * 
 */
class phpqueryGet {
    
    //第一个参数是匹配的文件
    //第一个参数你想要的框架的选择器
    //第二的参数这个框架里具体的信息选择器
    //获取文本信息
    function getDetailedmess($data,$oneSelect,$twoSelect){
        $datas = [];
        $document = phpQuery::newDocumentHTML($data);
        //用PHPquery对对象进行解析

        if(!empty($twoSelect)){
            $doc = phpQuery::pq("");
            $text_box = $doc->find($oneSelect);
            
            foreach($text_box as $text){
                $data = pq($text)->find($twoSelect)->text();
                array_push($datas,$data);
            }
        }else{
            $doc = phpQuery::pq("");
            $text_box = $doc->find($oneSelect);
            
            foreach($text_box as $text){
                $data = pq($text)->text();
                
                array_push($datas,$data);
            }
        }
        return $datas;
    }
    //第一个参数是匹配的shuju
    //第一个参数你想要的标签的选择器
    //第二的参数标签属性名称
    //用来采集标签属性
    function getTabAttributes($data,$oneSelect,$twoSelect){
        $datas = [];
        $document = phpQuery::newDocumentHTML($data);
        //用PHPquery对对象进行解析
        $doc = phpQuery::pq("");
        $text_box = $doc->find($oneSelect);
        foreach($text_box as $text){
            $data = $text->getAttribute($twoSelect);
            array_push($datas,$data);
        }
        return $datas;
    }

}


/**
 * 文件处理类
 * 方法持续更新
 */
class fileDo{
    private $path;
    
    public function __construct($path){
        $this->path = $path;
        
    }

    /**
     * 按数组的格式，序列化存入文件中
     * 参数是要存入文件的数组
     */
    function fileWrite($fileArray){
        if(is_array($fileArray)){
        $file_hwnd = fopen($this->path,"w");
        fwrite($file_hwnd,serialize($fileArray));//写入序列化数组
        fclose($file_hwnd);

        }else{
            echo "这个方法的参数是数组";
        }  
    }

    /**
     * 按行存序列化数据
     */
    function fileWrite_A($fileArray){
        if(is_array($fileArray)){
        $file_hwnd = fopen($this->path,"a");
        $fileData = serialize($fileArray)."\r\n";
        fwrite($file_hwnd,$fileData);//写入序列化数组
        fclose($file_hwnd);

        }else{
            echo "这个方法的参数是数组";
        }  
    }

    /**
     * 把数组序列化的文件读出来
     */
    function fileRead(){
        $file_hwnd = fopen($this->path,"r");
        if(filesize($this->path)>0){
        $content = fread($file_hwnd,filesize($this->path));//读取文件内容
        fclose($file_hwnd);
        $fileArray = unserialize($content);
        return $fileArray;
        }else{
            return 0;
        }
    }

    /**
     * 按行读取序列化数组
     */
    function fileRead_A(){
        $fileArray = array();
        $i = 0;
        $file_hwnd = fopen($this->path,"r");
        if(filesize($this->path)>0){
            while(!feof($file_hwnd)){
                $fileData = unserialize(fgets($file_hwnd));
                if(!empty($fileData)){
                array_push($fileArray,$fileData);
                }
            }
            fclose($file_hwnd);
            return $fileArray;
        }else{
            return 0;
        }
    }

    /**
     * 文件清空
     * 
     */
    function fileEmptied(){
        $file_hwnd = fopen($this->path,"w");
        fclose($file_hwnd);
    }
}


/**
 * pdo连接数据ku
 */
class pdoMysql {
    private $dbms = "mysql";      //数据库类型
    private $host = "localhost";       //数据库主机名
    private $dbName = "flight_line";
    private $user = "root";
    private $pwd = "";
    private $pdoObject;

    public function __construct(){              //初始化pdo对象
        try{
            $dsn = $this->dbms.":host=".$this->host.";dbname=".$this->dbName;       //构造pdo dsn
            $this->pdoObject = new PDO($dsn,$this->user,$this->pwd);
            $this->pdoObject->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);       //设置错误处理模式为抛出异常
            
        }catch(PDOException $e){
            echo $e->getMessage()."<br/>";      //输出错误信息
        }
    }


    /**
     * 用来执行查询语句，返回一个二维数组
     */
    public function prepareSql($sql){
        
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
     * 用来执行插入，删除，更改等语句
     */
    public function prepareSql_2($sql){
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
}
?>