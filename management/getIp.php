<?php

/**
 * 获取用户信息的文件
 * 
 * 获取用户ip，地理位置，浏览器类型，语言和操作系统
 * @author internet
 * @version 1.0
 * @date 2018,3.19
 */
$cur_dir = dirname(__FILE__); //获取当前文件的目录
chdir($cur_dir); //把当前的目录改变为指定的目录。
require_once("../MVC_frame/mysql.class.php");

 /**
  * 获取用户ip，地理位置，浏览器类型，语言和操作系统
 * @author internet
 * @version 1.0
 * @date 2018,3.23
  */
// 作用取得客户端的ip、地理信息、浏览器、本地真实IP
class get_gust_info { 
  
    ////获得访客浏览器类型
    function GetBrowser(){
     if(!empty($_SERVER['HTTP_USER_AGENT'])){
      $br = $_SERVER['HTTP_USER_AGENT'];
      if (preg_match('/MSIE/i',$br)) {    
                 $br = 'MSIE';
               }elseif (preg_match('/Firefox/i',$br)) {
       $br = 'Firefox';
      }elseif (preg_match('/Chrome/i',$br)) {
       $br = 'Chrome';
         }elseif (preg_match('/Safari/i',$br)) {
       $br = 'Safari';
      }elseif (preg_match('/Opera/i',$br)) {
          $br = 'Opera';
      }else {
          $br = 'Other';
      }
      return $br;
     }else{return "获取浏览器信息失败！";} 
    }
    
    ////获得访客浏览器语言
    function GetLang(){
     if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
      $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
      $lang = substr($lang,0,5);
      if(preg_match("/zh-cn/i",$lang)){
       $lang = "简体中文";
      }elseif(preg_match("/zh/i",$lang)){
       $lang = "繁体中文";
      }else{
          $lang = "English";
      }
      return $lang;
      
     }else{return "获取浏览器语言失败！";}
    }
    
     ////获取访客操作系统
    function GetOs(){
     if(!empty($_SERVER['HTTP_USER_AGENT'])){
      $OS = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i',$OS)) {
       $OS = 'Windows';
      }elseif (preg_match('/mac/i',$OS)) {
       $OS = 'MAC';
      }elseif (preg_match('/linux/i',$OS)) {
       $OS = 'Linux';
      }elseif (preg_match('/unix/i',$OS)) {
       $OS = 'Unix';
      }elseif (preg_match('/bsd/i',$OS)) {
       $OS = 'BSD';
      }else {
       $OS = 'Other';
      }
            return $OS;  
     }else{return "获取访客操作系统信息失败!";}   
    }
    
    ////获得访客真实ip
    function Getip(){   
     
     if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ //获取代理ip
        $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
     }else{
        $ips = $_SERVER['REMOTE_ADDR'];
     }
     
    
     if($ips=="127.0.0.1"||$ips=="::1"){//获得本地真实IP
        return $this->get_onlineip();   
     }else{
        return $ips; 
     }
    }
    
    ////获得本地真实IP
    function get_onlineip() {
        $mip = file_get_contents("http://ip.chinaz.com/getip.aspx");        //中国站长获取本地ip
        
         if(!empty($mip)){
            preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",$mip,$sip);
            
            return $sip[0];
         }else{
             return "获取本地IP失败！";}
     }
    

    ////根据ip获得访客所在地地名
    function Getaddress($ip=''){
     if(empty($ip)){
         $ip = $this->Getip();    
     }
     $ipadd = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=".$ip);//根据新浪api接口获取
     if($ipadd){
      $charset = iconv("gbk","utf-8",$ipadd);   
      preg_match_all("/[\x{4e00}-\x{9fa5}]+/u",$charset,$ipadds);
      
      return $ipadds;   //返回一个二维数组
     }else{
         return "addree is none";
        }  
    }

   }


   /**
    * 把用户的ip信息存放到数据库中
    * @author rwb
    * @version 1.0
    * @date 2018,3.23
    */
   class insert_ip {
    /**
     * 插入到ip信息表中
     * 
     * @access public
     * @since  数据库封装好的类，和获取信息得类
     * @return int 这个ip信息在数据库的主键
     */
    public static function insert_ips(){
        $pdoObject = new pdosql;
        $gifo = new get_gust_info();
        $ip = $gifo->Getip();               //获取ip
        $browser = $gifo->GetBrowser();     //获取浏览器
        $language = $gifo->GetLang();       //获取语言
        $opreating = $gifo->GetOs();        //获取操作系统
        $address = $gifo->Getaddress();     //获取地址
            if(!is_array($address[0])){                 //有些信息并不是都能获取，把没有信息的变量定义，避免报错
                $add[2] = "";
                $add[0] = "";
                $add[1] = ""; 
            }else if(isset($address[0][2])){
                $add[2] = $address[0][2];
                $add[0] = $address[0][0];
                $add[1] = $address[0][1]; 
            }else if(isset($address[0][1])){
                $add[0] = $address[0][0];
                $add[1] = $address[0][1]; 
                $add[2] = "";
            }else if(isset($address[0][0])){
                $add[0] = $address[0][0];
                $add[2] = "";
                $add[1] = ""; 
            }
                            
        $select_ips = array("ip"=>$ip,"browser"=>$browser,"language"=>$language,"nationality"=>$add[0],"province"=>$add[1],"city"=>$add[2],"opreating"=>$opreating);

        $select_isIps = $pdoObject->select_all("management_userdata",array("*"),$select_ips);       //查找相同是不是有相同的
        
        if(empty($select_isIps)){                                                                   //不为空就插入ip信息表中，空就只插入访客信息表
            $ips_id = $pdoObject->insert("management_userdata",$select_ips);

            $pdoObject->insert("management_num",array("ip_id"=>$ips_id));
            return $ips_id;                                                                        //返回ip信息的主键
        }else{
            $pdoObject->insert("management_num",array("ip_id"=>$select_isIps[0]['id']));
            return $select_isIps[0]['id'];
        }
    }
   }
   //insert_ip::insert_ips();