<?php
namespace app\Model;
use lib\core\DB;
/**
* 车票价Modle类，得到的是计算后的价格对比
*/
class TrainsPrice
{
	private static $dpplace;
	private static $arrplace;
	public static function Search_trains($dpplace,$arrplace){
		
		if(empty($dpplace)||empty($arrplace)){
			throw new Exception("没有参数传入,需要两个参数！");
		}else{
			self::$dpplace=$dpplace;
			self::$arrplace=$arrplace;
			$sql="SELECT A.about_id,A.trainNo,A.stationSort AS dpSort,B.stationSort AS arrSort,A.runTime as ArunTime,B.runTime as BrunTime,A.distance as Adistance,B.distance as Bdistance,A.stationName AS dpSta,B.stationName AS arrSta,A.startTime AS AstartTime,B.startTime AS BstartTime FROM station_stop AS A,station_stop AS B WHERE A.stationName like '{$dpplace}%' AND B.stationName  like '{$arrplace}%' AND A.about_id=B.about_id AND A.stationSort<B.stationSort";
			// echo $sql;exit();

			$results_obj= DB::query($sql);
			$PDO=DB::$con;
			$results_obj->setFetchMode($PDO::FETCH_ASSOC);
			$results= $results_obj->fetchAll();//得到所有的满足条件的车次信息$results[0]['about_id']/$results[0]['dpSort']/$results[0]['arrSort']
			$trainTable=self::noAgainData($results);//此数据是唯一的有效车次信息表	
			$trainTable=self::computeTicket($trainTable);//将价格加上后
			// self::TimeUpSort($trainTable);
			return  $trainTable;
		}
		
	}

	/**
	*@param 计算各个车次的各种票价
	*return 加上了票价的$trainTible车次表 
	*/
	private static function computeTicket($trainTable){
		$Add_price_table=[];
	    foreach ($trainTable as $train) {

	    	$train_type=mb_substr(strtoupper($train['trainNo']), 0,1,'utf-8');//得到某种类型的车G.D.C.K.Z.T.P.S.Y.2262
	    	if($train_type=='K'||$train_type=='T'||$train_type=='Z')
	    	{
	           $distance=$train['Bdistance']-$train['Adistance'];      
	           if($distance<=200){
	               $baise_pri=0.05861*$distance;//基础价
	               $train=self::do_price($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=201&&$distance<=500){
	           	   $baise_pri=0.052749*($distance-200)+11.722;//基础价
	           	   $train=self::do_price($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=501&&$distance<=1000){
	           	   $baise_pri=0.046888*($distance-500)+27.5467;//基础价
	           	   $train=self::do_price($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=1001&&$distance<=1500){
	          	   $baise_pri=0.041027*($distance-1000)+50.9907;//基础价
	               $train=self::do_price($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=1501&&$distance<=2500){
	          	   $baise_pri=0.035166*($distance-1501)+71.5042;//基础价
	               $train=self::do_price($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=2501){
	          	   $baise_pri=0.029305*($distance-2500)+106.6702;//基础价
	               $train=self::do_price($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }

	    	}else if($train_type=='G'){
	    	   /*高铁票价实行递远递减优惠，500-1000km部分打9折，1000-1500km部分打8折，1500-2000km部分打7折，2000km以上部分打6折，不同席别的票价差异体现为费率的不同，由于商务座或特等座受市场价浮动严重不计算*/
	    	   $distance=$train['Bdistance']-$train['Adistance'];
	    	   if($distance<=500){
	    	   	   if($distance<=20){
	    	   	   	$bSeat=0.46*20.0;//二等座
	    	   	   	$aSeat=0.74*20.0;//一等座
	    	   	   }else{
	    	   	   	$bSeat=0.46*$distance;
	    	   	   	$aSeat=0.74*$distance;
	    	   	   }
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if($distance>=501&&$distance<=1000){
	    	   	   $bSeat=0.46*500+($distance-500)*0.46*0.9;
	    	   	   $aSeat=0.74*500+($distance-500)*0.74*0.9;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if($distance>=1001&&$distance<=1500) {
	    	   	   $bSeat=0.46*500+500*0.46*0.9+($distance-1000)*0.46*0.8;
	    	   	   $aSeat=0.74*500+500*0.74*0.9+($distance-1000)*0.74*0.8;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if($distance>=1501&&$distance<=2000) {
	    	   	   $bSeat=0.46*500+500*0.46*0.9+500*0.46*0.8+($distance-1500)*0.46*0.7;
	    	   	   $aSeat=0.74*500+500*0.74*0.9+500*0.74*0.8+($distance-1500)*0.74*0.7;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if ($distance>=2001) {
	    	   	   $bSeat=0.46*500+500*0.46*0.9+500*0.46*0.8+500*0.46*0.7+($distance-2000)*0.46*0.6;
	    	   	   $aSeat=0.74*500+500*0.74*0.9+500*0.74*0.8+500*0.74*0.7+($distance-2000)*0.74*0.6;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }
	    	}else if($train_type=='C'||$train_type=='D'){
	         $distance=$train['Bdistance']-$train['Adistance'];
	    	   if($distance<=500){
	    	   	   if($distance<=20){
	    	   	   	$bSeat=0.30*20.0;//二等座
	    	   	   	$aSeat=0.37*20.0;//一等座
	    	   	   }else{
	    	   	   	$bSeat=0.30*$distance;
	    	   	   	$aSeat=0.37*$distance;
	    	   	   }
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if($distance>=501&&$distance<=1000){
	    	   	   $bSeat=0.30*500+($distance-500)*0.30*0.9;
	    	   	   $aSeat=0.37*500+($distance-500)*0.37*0.9;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if($distance>=1001&&$distance<=1500) {
	    	   	   $bSeat=0.30*500+500*0.30*0.9+($distance-1000)*0.30*0.8;
	    	   	   $aSeat=0.37*500+500*0.37*0.9+($distance-1000)*0.37*0.8;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if($distance>=1501&&$distance<=2000) {
	    	   	   $bSeat=0.30*500+500*0.30*0.9+500*0.30*0.8+($distance-1500)*0.30*0.7;
	    	   	   $aSeat=0.37*500+500*0.37*0.9+500*0.37*0.8+($distance-1500)*0.37*0.7;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }else if ($distance>=2001) {
	    	   	   $bSeat=0.30*500+500*0.30*0.9+500*0.30*0.8+500*0.30*0.7+($distance-2000)*0.30*0.6;
	    	   	   $aSeat=0.37*500+500*0.37*0.9+500*0.37*0.8+500*0.37*0.7+($distance-2000)*0.37*0.6;
	    	   	   $train['bSeat']=round($bSeat);
	               $train['aSeat']=round($aSeat);
	               array_push($Add_price_table,$train);
	    	   }
	    	}else{
	    	     /*铺快列车如：7768*/
	           $distance=$train['Bdistance']-$train['Adistance'];      
	           if($distance<=200){
	               $baise_pri=0.05861*$distance;//基础价
	               $train=self::do_price2($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=201&&$distance<=500){
	           	   $baise_pri=0.052749*($distance-200)+11.722;//基础价
	           	   $train=self::do_price2($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=501&&$distance<=1000){
	           	   $baise_pri=0.046888*($distance-500)+27.5467;//基础价
	           	   $train=self::do_price2($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=1001&&$distance<=1500){
	          	   $baise_pri=0.041027*($distance-1000)+50.9907;//基础价
	               $train=self::do_price2($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=1501&&$distance<=2500){
	          	   $baise_pri=0.035166*($distance-1501)+71.5042;//基础价
	               $train=self::do_price2($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           }
	           if($distance>=2501){
	          	   $baise_pri=0.029305*($distance-2500)+106.6702;//基础价
	               $train=self::do_price2($baise_pri,$train);//加工基础价得到各种座位价格
	               array_push($Add_price_table,$train);
	           } 
	    	}
	    }
	    return $Add_price_table;
	}

	/**
	*@param 得到车次结果表里价格最低的车次'下标'=$result_key
	*/
	private static function MinPricekey($trainTable){
	    
	  foreach ($trainTable as $key => $train) {    
	      if(!empty($train['hardSeat'])){
	      $temp_Price=$train['hardSeat'];
	      // echo '这是火车<br/>';
	      }
	      if(!empty($train['bSeat'])){
	      $temp_Price=$train['bSeat'];
	      // echo '这是高铁<br/>';
	      }
	      if($key==0){
	      $MinPrice=$temp_Price;
	      $result_key=$key;
	      continue;
	      }
	      if($MinPrice>=$temp_Price){
	      $MinPrice=$temp_Price;
	      $result_key=$key;
	      }
	  }
	  // echo '最小价格车次下标：'.$result_key;
	  return $result_key;
	}

	/**
	*@param 得到车次结果表里价格最高的车次'下标'=$result_key
	*/
	private static function MaxPricekey($trainTable){
	    
	  foreach ($trainTable as $key => $train) {    
	      if(!empty($train['hardSeat'])){
	      $temp_Price=$train['hardSeat'];
	      // echo '这是火车<br/>';
	      }
	      if(!empty($train['bSeat'])){
	      $temp_Price=$train['bSeat'];
	      // echo '这是高铁<br/>';
	      }
	      if($key==0){
	      $MaxPrice=$temp_Price;
	      $result_key=$key;
	      continue;
	      }
	      if($MaxPrice<=$temp_Price){
	      $MaxPrice=$temp_Price;
	      $result_key=$key;
	      }
	  }
	  // echo '最小价格车次下标：'.$result_key;
	  return $result_key;
	}

	/**
	*@param 计算K/Z/T车型票价 
	*/
	private static function do_price($baise_pri,$train){
	               $add_train_type=0.4*$baise_pri;//K车型附加票价
	           	   $add_kongtiao=0.25*$baise_pri;//空调费         
	               $hardSeat=($baise_pri+$add_train_type+$add_kongtiao)*1.5+1.0;//硬座基础价+空调+车型 x浮动价 1元铁路发展基金
	               $hardBed=($baise_pri+$add_train_type+$add_kongtiao+1.2*$baise_pri)*1.5+2.0+10.0;//+10元卧铺+多1元硬卧候车费
	               $softBed=(2.0*$baise_pri+$add_train_type+$add_kongtiao+1.95*$baise_pri)*1.5+1.0+10.0;
	               
	               $train['hardSeat']=round($hardSeat);
	               $train['hardBed']=round($hardBed);
	               $train['softBed']=round($softBed);  
	    return $train;          
	}

	/**
	*@param 计算铺快和其他
	*/
	private static function do_price2($baise_pri,$train){
	               $add_train_type=0.2*$baise_pri;//K车型附加票价
	           	      
	               $hardSeat=($baise_pri+$add_train_type)*1.5+1.0;//硬座基础价+空调+车型 x浮动价 1元铁路发展基金
	               $hardBed=($baise_pri+$add_train_type+1.2*$baise_pri)*1.5+2.0+10.0;//+10元卧铺+多1元硬卧候车费
	               $softBed=(2.0*$baise_pri+$add_train_type+1.95*$baise_pri)*1.5+1.0+10.0;
	               
	               $train['hardSeat']=round($hardSeat);
	               $train['hardBed']=round($hardBed);
	               $train['softBed']=round($softBed);  
	    return $train;                        
	}

	/**
	*@param 与栈里面的数据对比，得到保持车次不重复，即G1312/G1213只出现一次
	*/
	private static function stickOnly($arrData,$arrs){
		$count=count($arrs)-1;
		while($count>=0){
			if($arrs[$count]['trainNo']==$arrData['trainNo']){
				return false;
	        }
	           $count=$count-1;
	   	}
		return true;

		// foreach ($arrs as $arr) {
		// 	if($arr['trainNo']==$arrData['trainNo']){
		// 		return false;
		// 	}
		// }
		// return true;
	}

	/**
	*@param 得到唯一的有效车次信息表
	*/
	private static function noAgainData($arrDatas){
	   $arrs=[];
	   foreach ($arrDatas  as $key=> $arrData) {
	   	    if($key==0){
	   	     array_push($arrs, $arrData);
	   	    // print_r($arrs);
	   	    // echo "key:$key<br>";
	   	    continue;	
	   	    }

	   	    if(self::stickOnly($arrData,$arrs)==true){
	   	    array_push($arrs, $arrData);
	   	    }
	   }
	   return $arrs;
	}

	/**
	*按价格升序排列
	*/
	static function PriceUpSort($trainsTable){
	  for($i=0;$i<count($trainsTable)-1;$i++)
	  {
	      for($j=0;$j<count($trainsTable)-$i-1;$j++)
	      {
	        if(isset($trainsTable[$j]['hardSeat'])){
	          $Aj=$trainsTable[$j]['hardSeat'];
	        }
	        if(isset($trainsTable[$j]['bSeat'])){
	          $Aj=$trainsTable[$j]['bSeat'];
	        }
	        if(isset($trainsTable[$j+1]['hardSeat'])){
	          $Bj=$trainsTable[$j+1]['hardSeat'];
	        }
	        if(isset($trainsTable[$j+1]['bSeat'])){
	          $Bj=$trainsTable[$j+1]['bSeat'];
	        }
	        if($Aj>$Bj)
	        {
	          $temp=$trainsTable[$j+1];
	          $trainsTable[$j+1]=$trainsTable[$j];
	          $trainsTable[$j]=$temp;
	        }
	      }
	  }
		return $trainsTable;
	}
	/**
	*按时间升序排列
	*/
	static function TimeUpSort($trainsTable){
	  for($i=0;$i<count($trainsTable)-1;$i++)
	  {
	      for($j=0;$j<count($trainsTable)-$i-1;$j++)
	      {
	        $Aj=$trainsTable[$j]['BrunTime']-$trainsTable[$j]['ArunTime'];
	        $Bj=$trainsTable[$j+1]['BrunTime']-$trainsTable[$j+1]['ArunTime'];
	        if($Aj>$Bj)
	        {
	          $temp=$trainsTable[$j+1];
	          $trainsTable[$j+1]=$trainsTable[$j];
	          $trainsTable[$j]=$temp;
	        }
	      }
	  }
		return $trainsTable;
	}
	
}
?>