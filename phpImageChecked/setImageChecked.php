<?php
/**
 * 生成图片验证码文件
 * 
 * 生成验证码用于后台登录验证
 * @author rwb
 * @version v1.0 2018 3.23
 */
session_start();
ob_clean();             //清除缓存
header("content-type:image/png");           //声明文件类型是image或png
/**
 * 生成图像的类
 * 
 * @author internet
 * @version v1.0
 * @date 2018-3-12
 */
class setImageChecked {
    /**
     * 生成随机验证码
     * 
     * 用十六进制生成随机验证码 并把验证码存放到$_SESSION['check_checks']变量中
     * @access public 
     * @return null
     * @since internet
     */
    public static function setCheck(){
        
        
        $new_number="";
        for($i=0;$i<4;$i++){
            $new_number.=dechex(rand(0,15));
        }
        $_SESSION['check_checks'] = $new_number;
    }


    /**
     * 生成图像的验证码类的主方法
     * 
     * @access public
     * @return null
     * @since setCheck() 调用了本类的setImageChecked::setCheck()方法用于生成验证码
     */
    public static function setImage(){
        setImageChecked::setCheck();                                            //用于生成验证码，本类的方法
        //var_dump($_SESSION['check_checks']);die;
        $image_width = 80;                                                      //生成图片的宽度
        $image_height = 40;                                                     //图片高度
        $num_image = imagecreate($image_width,$image_height);                   //用于创建一副空白图像 返回一个图像资源
        imagecolorallocate($num_image,255,255,255);                              //为一幅图像分配颜色 第一次调用为背景设置颜色
        for($i = 1;$i < 200;$i++){
            $x = mt_rand(1,$image_width-9);
            $y = mt_rand(1,$image_height-9);                                      //随机生成雪花背景
            $color = imagecolorallocate($num_image,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
            imagestring($num_image,1,$x,$y,"*",$color);
         }
        for($i=0;$i<strlen($_SESSION['check_checks']);$i++){                      //将验证码，随机的放在画布上
            $font = mt_rand(3,5);
            $x = mt_rand(1,8)+$image_width*$i/4;
            $y = mt_rand(1,$image_height/4);
            $color = imagecolorallocate($num_image,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200));
            imagestring($num_image,5,$x,$y,$_SESSION['check_checks'][$i],$color);       //把验证码一个一个的放进画布中
        }
        
        imagepng($num_image);                   //输出图片
        imagedestroy($num_image);               //销毁图像
    }
}

setImageChecked::setImage();                    //调用生成图片方法