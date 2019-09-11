<?php
namespace app\Model;
use lib\core\DB;

/**
*船务信息
*/
class ship
{
	private $start;
	private $end;
	private $line;
	private $ship_name;
	private $money;
	private $time;
	private $days;
	private $qite;

	//获取航线的信息
	function message($start,$end){
		$this->start = $start;
		$this->end = $end;
		$this->dbconfig = DB::$config;
		if(empty($start)||empty($end)){
			throw new Exception("请传入参数！");
		}
	}
}
?>