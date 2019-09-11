<?php
/**
 * 数据更新脚本
 * 
 * 用于定时更新数据
 * @author rwb
 * @version v1.0 2018-3-13
 */
set_time_limit(0);//脚本运行时间不受时间限制

 require_once('getFlightCode.php');
 $cur_dir = dirname(__FILE__); //获取当前文件的目录
 chdir($cur_dir); //把当前的目录改变为指定的目录。
 require_once('get_flightMessage.php');
 require_once("../setIndex/participle_fromCity.php");
 require_once("../setIndex/pinyin_participle.php");
 


    $testObject = new getFlightCode2_0;
    $testObject->saveHtml();      //存储爬取来的HTML代码，有调用爬虫    5~10秒
    $testObject->saveCode();     //存储提取后的航班号代码，无调用爬虫       35秒
    
    get_flightMessage::get_flightMessageMain();      //爬取航旅纵横信息放到flightMessage表中（要调用爬虫） 爬1000次要60分钟（还剩2000个航班号没有航班信息）
    $testObject = new save_flightMessage;
    $testObject->takeMessage();                     //更新城市，航线和航班表（无调用爬虫）

    $test = new participleClass();      //更新索引表和关系表
    $test->setindex_table();

    $test = new pinyin_index();         //更新拼音,应为生成拼音这个方法会出错，所以插入数据库的时候回出现一些问题
    $test->updateIndex();

    
?>