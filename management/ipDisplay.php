<?php
/**
 * 把最近访客的ip信息整理显示出来
 */
$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
require_once("../MVC_frame/mysql.class.php");

class ipDisplay {
    private $pdosqlObject;
    /**
     * 构造方法，建立对象
     */
    public function __construct(){
        $this->pdosqlObject = new pdoSql();
    }
    /**
     * 查找最近访客的ip信息
     */
    public function getipMessage(){
        $sql = "SELECT * FROM management_userdata ORDER BY id DESC LIMIT 10";
        
        $S_result = $this->pdosqlObject->select_sql($sql);
        if(!empty($S_result)){
            foreach($S_result as $key=>$value){
                $sql_count = "SELECT COUNT(*) FROM management_num WHERE ip_id=".$value['id'];
                $sql_weekNum = "SELECT COUNT(*) from management_num where DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(createtime) and ip_id=".$value['id']; //查找在最近一周插进去的数据
                
                $S_resultNum = $this->pdosqlObject->select_sql($sql_count);
                $S_weekNum = $this->pdosqlObject->select_sql($sql_weekNum);
                
                $S_result[$key]['num'] = $S_resultNum[0]['COUNT(*)'];
                $S_result[$key]['weekNum'] = $S_weekNum[0]['COUNT(*)'];
            }
           
            return $S_result;
        }
    }

    /**
     * 
     */
}
// $test = new ipDisplay();
// $test->getipMessage();