<?php
/**
 * 后台点击查找调用的ajax事件
 */

header('Content-Type: application/json; charset=utf8'); 
class leave_ajaxj {
    /**
     * 更新留言显示已经被查看样式
     */
    private $pdosqlObject;
    /**
     * 构造方法，建立对象
     */
    public function __construct(){
        $this->pdosqlObject = new pdoSql();
    }

    /**
     * 根据id查找具体信息并更改是否已经查看的的状态
     */
    public function leave_display(){
        if(empty($_POST['leave_id'])){
            
        }else{
            $sql = "SELECT * FROM management_leave where id=".$_POST['leave_id'];
            $r_leave = $this->pdosqlObject->select_sql($sql);
            
            $this->pdosqlObject->update("management_leave",array("is_watch"=>1),array("id"=>$_POST['leave_id']));
            echo json_encode($r_leave);
        }
    }
}
$test = new leave_ajaxj();
$test->leave_display();