<?php
/**
 * 用于查找用户查找路线的数据
 */
class selectDisplay {
    /**
     * 用户查找信息的显示
     */
    private $pdosqlObject;
    /**
     * 构造方法，建立对象
     */
    public function __construct(){
        $this->pdosqlObject = new pdoSql();
    }

    public function getSelectMessage(){
        $sql = "SELECT id,concat(start_city,toCity) as b,COUNT(id) as num from management_selectcount group by b ORDER BY num DESC LIMIT 10";
        
        $S_result = $this->pdosqlObject->select_sql($sql);
        //var_dump($S_result);
        if(!empty($S_result)){
            foreach($S_result as $key=>$value){
                $sql_count = "SELECT start_city,toCity from management_selectcount WHERE id=".$value['id'];
                $sql_weekNum = "SELECT COUNT(*) from management_selectcount where DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(createtime) and concat(start_city,toCity)='".$value['b']."'"; //查找在最近一周插进去的数据
                
                $S_resultcontent = $this->pdosqlObject->select_sql($sql_count);
                $S_weekNum = $this->pdosqlObject->select_sql($sql_weekNum);
                $S_result[$key]['startCity'] = $S_resultcontent[0]['start_city'];
                $S_result[$key]['toCity'] = $S_resultcontent[0]['toCity'];
                
                $S_result[$key]['weekNum'] = $S_weekNum[0]['COUNT(*)'];
            }
            
        }
        return $S_result;
    }
}